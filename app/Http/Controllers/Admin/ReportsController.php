<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LoadCampaign30day;
use App\Model\SmsCampaign_24h;
use App\Model\SmsCampaignId;
use App\Model\AccSmsBalance;
use App\Model\User;
use App\Model\SmsCampaign;
use App\Model\SenderIdRegister;
use App\Model\Operator;
use App\Model\SenderIdUser;
use Auth;
use DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function sms_flexi_reports(){
        $q_start_date = Carbon::now()->subDay();
        $q_end_date = Carbon::now();

        $sms_user = SmsCampaign_24h::groupBy('user_id')->get();
        
        $flexi_user = LoadCampaign30day::groupBy('user_id')
                                    ->where('created_at','>', $q_start_date)
                                    ->where('created_at','<', $q_end_date)
                                    ->get();
        return view('admin.reports.sms_flexi_reports',compact('sms_user','flexi_user'));
    }

    public function operator_reports(Request $request){
        if ($request->has('start_date') && $request->has('end_date')) {
            $start = $request->start_date;
            $end = $request->end_date;
            $a = Carbon::parse($request->start_date);
            $b = Carbon::parse($request->end_date);
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        }else{
            $start = Carbon::now()->subDays(7)->format('Y-m-d');
            $end = Carbon::now()->format('Y-m-d');
            $a = Carbon::parse(now()->subDays(7)->format('Y-m-d'));
            $b = Carbon::parse(now()->format('Y-m-d'));
            $q_start_date = Carbon::now()->subDays(7);
            $q_end_date = Carbon::now();
        }
        $days = $a->diffInDays($b);
        $sms_report = SmsCampaign::with('operator')
                                 ->select(DB::raw("count(*) as total,operator_id"),DB::raw("sum(sc_sms_cost) as total_cost"))
                                 ->where('sc_sms_type',2)
                                 ->where('created_at','>=',$q_start_date)
                                 ->where('created_at','<=',$q_end_date)
                                 ->groupBy('operator_id')
                                 ->get();
        $nonMaskingReport = SmsCampaignId::where('sci_sms_type', 1)
                                            ->whereIn('sci_sender_operator', [1,2,3,4])
                                            ->select('sci_sender_operator',DB::raw("sum(sci_total_submitted) as total"),DB::raw("sum(sci_total_cost) as total_cost"))
                                            ->where('sci_targeted_time', '>=', $q_start_date)
                                            ->where('sci_targeted_time', '<=', $q_end_date)
                                            ->where(function ($query) {
                                                $query->where('sci_from_api',1);
                                                $query->orWhere('sci_from_api',NULL);
                                            })
                                            ->groupBy('sci_sender_operator')
                                            ->get();

        $flexi_report = LoadCampaign30day::select(DB::raw('count(*) as total,operator_id'),DB::raw('sum(campaign_price) as total_cost'))
                                    ->where('created_at', '>=', $q_start_date)
                                    ->where('created_at', '<=', $q_end_date)
                                    ->groupBy('operator_id')
                                    ->get();
        return view('admin.reports.operator_reports',compact('sms_report','flexi_report','nonMaskingReport','start','end','days'));
    }
    
    public function balance_transaction_reports(Request $request){
        if ($request->ajax()) {
            if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $q_start_date = Carbon::parse($request->start_date)->startOfDay();
                $q_end_date = Carbon::parse($request->end_date)->endOfDay();
            } else {
                $q_start_date = Carbon::now()->startOfDay();
                $q_end_date = Carbon::now()->endOfDay();
            }
            
            $query = AccSmsBalance::whereBetween('asb_submit_time', [$q_start_date, $q_end_date])
                                  ->whereHas('paidByUser', function($q) {
                                      $q->where('role', 4);
                                  });
            
            if ($request->filled('payment_mode')) {
                $query->where('asb_pay_mode', $request->payment_mode);
            }
            
            if ($request->filled('deal_type')) {
                $query->where('asb_deal_type', $request->deal_type);
            }
            
            if ($request->filled('payment_status')) {
                $query->where('asb_payment_status', $request->payment_status);
            }
            
            if ($request->filled('paid_by')) {
                $query->whereHas('paidByUser', function($q) use ($request) {
                    $q->where('company_name', 'like', '%' . $request->paid_by . '%')
                      ->orWhere('cellphone', 'like', '%' . $request->paid_by . '%');
                });
            }
            
            if ($request->filled('reference_id')) {
                $query->where('asb_pay_ref', 'like', '%' . $request->reference_id . '%');
            }
            
            $perPage = $request->get('per_page', 50);
            $page = $request->get('page', 1);
            
            $balance_transactions = $query->with(['paidByUser', 'payToUser', 'smsCampaignId', 'loadCampaignId'])
                                          ->orderBy('asb_submit_time', 'desc')
                                          ->paginate($perPage, ['*'], 'page', $page);
            
            $totalsQuery = AccSmsBalance::whereBetween('asb_submit_time', [$q_start_date, $q_end_date])
                                        ->whereHas('paidByUser', function($q) {
                                            $q->where('role', 4);
                                        });
            
            if ($request->filled('payment_mode')) {
                $totalsQuery->where('asb_pay_mode', $request->payment_mode);
            }
            if ($request->filled('deal_type')) {
                $totalsQuery->where('asb_deal_type', $request->deal_type);
            }
            if ($request->filled('payment_status')) {
                $totalsQuery->where('asb_payment_status', $request->payment_status);
            }
            
            $totals = $totalsQuery->select(
                DB::raw('COALESCE(SUM(asb_credit), 0) as total_credit'),
                DB::raw('COALESCE(SUM(asb_debit), 0) as total_debit')
            )->first();
            
            $filters = [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'payment_mode' => $request->payment_mode,
                'deal_type' => $request->deal_type,
                'payment_status' => $request->payment_status,
                'paid_by' => $request->paid_by,
                'reference_id' => $request->reference_id,
                'per_page' => $perPage
            ];
            
            return response()->json([
                'success' => true,
                'data' => view('admin.reports.balance_transaction_reports_table', compact('balance_transactions', 'totals', 'filters'))->render(),
                'total_credit' => number_format($totals->total_credit, 2),
                'total_debit' => number_format($totals->total_debit, 2),
                'net_balance' => number_format($totals->total_credit - $totals->total_debit, 2),
                'total_records' => $balance_transactions->total(),
                'current_page' => $balance_transactions->currentPage(),
                'last_page' => $balance_transactions->lastPage()
            ]);
        }
        
        $start = Carbon::now()->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d');
        return view('admin.reports.balance_transaction_reports', compact('start', 'end'));
    }

    public function exportBalanceTransactions(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            $q_start_date = Carbon::parse($request->start_date)->startOfDay();
            $q_end_date = Carbon::parse($request->end_date)->endOfDay();
        } else {
            $q_start_date = Carbon::now()->startOfDay();
            $q_end_date = Carbon::now()->endOfDay();
        }
        
        $query = AccSmsBalance::whereBetween('asb_submit_time', [$q_start_date, $q_end_date]);
        
        if ($request->filled('payment_mode')) {
            $query->where('asb_pay_mode', $request->payment_mode);
        }
        
        if ($request->filled('deal_type')) {
            $query->where('asb_deal_type', $request->deal_type);
        }
        
        if ($request->filled('payment_status')) {
            $query->where('asb_payment_status', $request->payment_status);
        }
        
        $transactions = $query->orderBy('asb_submit_time', 'desc')->get();
        
        $filename = 'balance_transactions_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Date & Time', 
                'Paid By', 
                'Pay To', 
                'Reference', 
                'Credit', 
                'Debit', 
                'Payment Mode', 
                'Deal Type', 
                'Status'
            ]);
            
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->asb_submit_time ? $transaction->asb_submit_time->format('Y-m-d H:i:s') : 'N/A',
                    $transaction->asb_paid_by ?? 'N/A',
                    $transaction->asb_pay_to ?? 'N/A',
                    $transaction->asb_pay_ref ?? 'N/A',
                    $transaction->asb_credit > 0 ? $transaction->asb_credit : 0,
                    $transaction->asb_debit > 0 ? $transaction->asb_debit : 0,
                    $transaction->asb_pay_mode ?? 'N/A',
                    $transaction->asb_deal_type ?? 'N/A',
                    $transaction->asb_payment_status == 1 ? 'Completed' : 'Pending'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sender ID wise message count per operator report
     */
    public function sender_operator_report(Request $request)
    {
        // Get filter values
        $start_date = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end_date = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $sender_id = $request->input('sender_id');
        $operator_id = $request->input('operator_id');

        // Get all operators
        $operatorsList = Operator::orderBy('ope_operator_name')->get();

        // Get message count from sms_campaign_ids table
        $messageCounts = DB::table('sms_campaign_ids as sci')
            ->join('sender_id_registers as sender', 'sci.sender_id', '=', 'sender.id')
            ->select(
                'sender.id as sender_register_id',
                'sender.sir_sender_id as sender_id',
                DB::raw('SUM(sci.sci_total_submitted) as total_messages')
            )
            ->whereBetween('sci.sci_targeted_time', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->groupBy('sender.id', 'sender.sir_sender_id')
            ->get()
            ->keyBy('sender_id');

        // Get all sender IDs with their assigned companies
        $senderData = DB::table('sender_id_registers as sender')
            ->leftJoin('sender_id_users as siu', 'siu.sender_id', '=', 'sender.id')
            ->leftJoin('users as u', 'siu.user_id', '=', 'u.id')
            ->select(
                'sender.id as sender_register_id',
                'sender.sir_sender_id as sender_id',
                'u.company_name',
                'u.cellphone',
                'siu.status'
            )
            ->orderBy('sender.sir_sender_id', 'ASC')
            ->get();

        // Group by sender_id
        $groupedSenders = [];
        foreach ($senderData as $item) {
            if (!isset($groupedSenders[$item->sender_id])) {
                $groupedSenders[$item->sender_id] = [
                    'sender_register_id' => $item->sender_register_id,
                    'sender_id' => $item->sender_id,
                    'companies' => [],
                    'cellphones' => []
                ];
            }
            if ($item->company_name) {
                $groupedSenders[$item->sender_id]['companies'][] = $item->company_name;
            }
            if ($item->cellphone) {
                $groupedSenders[$item->sender_id]['cellphones'][] = $item->cellphone;
            }
        }

        // Build final report with filters
        $senderReports = collect();
        
        foreach ($groupedSenders as $senderId => $data) {
            $operatorName = 'N/A';
            $totalMessages = 0;
            $matchedOperator = null;
            
            // Find matching operator
            foreach ($operatorsList as $operator) {
                if (strpos($senderId, $operator->ope_number) === 0) {
                    $operatorName = $operator->ope_operator_name;
                    $matchedOperator = $operator;
                    break;
                }
            }

            // APPLY OPERATOR FILTER - Only show senders for selected operator
            if ($operator_id && !empty($operator_id)) {
                if (!$matchedOperator || $matchedOperator->id != $operator_id) {
                    continue;
                }
            }

            // APPLY SENDER FILTER
            if ($sender_id && !empty($sender_id)) {
                if ($data['sender_register_id'] != $sender_id) {
                    continue;
                }
            }

            // Get message count from sms_campaign_ids
            if (isset($messageCounts[$senderId])) {
                $totalMessages = $messageCounts[$senderId]->total_messages;
            }

            $senderReports->push((object)[
                'sender_id' => $senderId,
                'companies' => !empty($data['companies']) ? implode(', ', array_unique($data['companies'])) : 'Not Assigned',
                'cellphones' => !empty($data['cellphones']) ? implode(', ', array_unique($data['cellphones'])) : 'N/A',
                'operator_name' => $operatorName,
                'operator_number' => $matchedOperator ? $matchedOperator->ope_number : 'N/A',
                'total_messages' => (int)$totalMessages,
                'assigned_count' => count(array_unique($data['companies']))
            ]);
        }

        // Sort by total_messages descending
        $senderReports = $senderReports->sortByDesc('total_messages');

        // Summary
        $summary = (object) [
            'total_messages' => $senderReports->sum('total_messages'),
            'total_cost' => 0,
            'total_senders' => $senderReports->count()
        ];

        // Get all senders for filter dropdown
        $senders = SenderIdRegister::orderBy('sir_sender_id')->get();
        
        // Get all operators for filter dropdown
        $operators = Operator::orderBy('ope_operator_name')->get();

        return view('admin.reports.sender_operator_report', compact(
            'senderReports',
            'summary',
            'senders',
            'operators',
            'start_date',
            'end_date'
        ));
    }

    /**
     * Export Sender Operator Report to CSV
     */
    public function exportSenderOperatorReport(Request $request)
    {
        // Get filter values
        $start_date = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end_date = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $sender_id = $request->input('sender_id');
        $operator_id = $request->input('operator_id');

        // Get all operators
        $operatorsList = Operator::orderBy('ope_operator_name')->get();

        // Get message count from sms_campaign_ids table
        $messageCounts = DB::table('sms_campaign_ids as sci')
            ->join('sender_id_registers as sender', 'sci.sender_id', '=', 'sender.id')
            ->select(
                'sender.id as sender_register_id',
                'sender.sir_sender_id as sender_id',
                DB::raw('SUM(sci.sci_total_submitted) as total_messages')
            )
            ->whereBetween('sci.sci_targeted_time', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->groupBy('sender.id', 'sender.sir_sender_id')
            ->get()
            ->keyBy('sender_id');

        // Get all sender IDs with their assigned companies
        $senderData = DB::table('sender_id_registers as sender')
            ->leftJoin('sender_id_users as siu', 'siu.sender_id', '=', 'sender.id')
            ->leftJoin('users as u', 'siu.user_id', '=', 'u.id')
            ->select(
                'sender.id as sender_register_id',
                'sender.sir_sender_id as sender_id',
                'u.company_name'
            )
            ->orderBy('sender.sir_sender_id', 'ASC')
            ->get();

        // Group by sender_id
        $groupedSenders = [];
        foreach ($senderData as $item) {
            if (!isset($groupedSenders[$item->sender_id])) {
                $groupedSenders[$item->sender_id] = [
                    'sender_register_id' => $item->sender_register_id,
                    'sender_id' => $item->sender_id,
                    'companies' => []
                ];
            }
            if ($item->company_name) {
                $groupedSenders[$item->sender_id]['companies'][] = $item->company_name;
            }
        }

        // Build report with filters
        $senderReports = [];
        foreach ($groupedSenders as $senderId => $data) {
            $operatorName = 'N/A';
            $matchedOperator = null;
            
            foreach ($operatorsList as $operator) {
                if (strpos($senderId, $operator->ope_number) === 0) {
                    $operatorName = $operator->ope_operator_name;
                    $matchedOperator = $operator;
                    break;
                }
            }

            // APPLY OPERATOR FILTER
            if ($operator_id && !empty($operator_id)) {
                if (!$matchedOperator || $matchedOperator->id != $operator_id) {
                    continue;
                }
            }

            // APPLY SENDER FILTER
            if ($sender_id && !empty($sender_id)) {
                if ($data['sender_register_id'] != $sender_id) {
                    continue;
                }
            }

            $totalMessages = isset($messageCounts[$senderId]) ? $messageCounts[$senderId]->total_messages : 0;

            $senderReports[] = (object)[
                'sender_id' => $senderId,
                'companies' => !empty($data['companies']) ? implode(', ', array_unique($data['companies'])) : 'Not Assigned',
                'operator_name' => $operatorName,
                'total_messages' => (int)$totalMessages
            ];
        }

        // Sort by total_messages descending
        usort($senderReports, function($a, $b) {
            return $b->total_messages - $a->total_messages;
        });

        // Generate CSV
        $filename = 'sender_operator_report_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($senderReports) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'SL',
                'Sender ID',
                'Operator',
                'Total Messages'
            ]);
            
            $sl = 1;
            foreach ($senderReports as $report) {
                fputcsv($file, [
                    $sl++,
                    $report->sender_id,
                    $report->operator_name,
                    $report->total_messages
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}