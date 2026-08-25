<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Model\AccSmsBalance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SmsBillReportController extends Controller
{
    public function showBillReport(Request $request)
    {
        $total_balance_bd = 0;
        $debit1 = 0;
        $debit2 = 0;
        $credit = 0;
        $balance = 0;
        $balancebd = 0;
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        } else{
            $start_date = Carbon::now()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->startOfDay();
            $q_end_date = Carbon::now()->endOfDay();
        }

        $transactions = AccSmsBalance::with('smsCampaignId.sender', 'loadCampaignId')->where('asb_pay_to', Auth::id())
            ->where('asb_submit_time', '>=', $q_start_date)
            ->where('asb_submit_time', '<=', $q_end_date)
            ->get();


        $customerCredit = AccSmsBalance::where(['asb_pay_to' => Auth::id()])->where('asb_submit_time','<',$q_start_date)->sum('asb_credit');
        $customerDebit = AccSmsBalance::where(['asb_pay_to' => Auth::id()])->where('asb_submit_time','<',$q_start_date)->sum('asb_debit');
        $balancebd = $customerCredit - $customerDebit;

        return view('user.reports.bill_report', compact('transactions', 'start_date', 'end_date','balancebd'));

    }

    public function billReportDownload(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        } else{
            $start_date = Carbon::now()->subDays(7)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->subDays(7);
            $q_end_date = Carbon::now();
        }

        $transactions = AccSmsBalance::with('smsCampaignId.sender', 'loadCampaignId')->where('asb_pay_to', Auth::id())
            ->where('asb_submit_time', '>=', $q_start_date)
            ->where('asb_submit_time', '<=', $q_end_date)
            ->orderBy('asb_submit_time', 'asc')
            ->get();

        $customerCredit = AccSmsBalance::where(['asb_pay_to' => Auth::id()])->where('asb_submit_time','<',$q_start_date)->sum('asb_credit');
        $customerDebit = AccSmsBalance::where(['asb_pay_to' => Auth::id()])->where('asb_submit_time','<',$q_start_date)->sum('asb_debit');
        $balancebd = $customerCredit - $customerDebit;


        $fileName = 'sms_bill_report_' . $start_date . '_to_' . $end_date . '.csv';

        return response()->stream(function () use ($transactions, $balancebd) {
            $file = fopen('php://output', 'w');
            // Keep Bangla readable when the CSV is opened with Microsoft Excel.
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Opening Balance', number_format($balancebd, 2, '.', '')]);
            fputcsv($file, []);
            fputcsv($file, [
                'SL', 'Campaign Type', 'Campaign Title', 'Submit Time', 'Sender ID',
                'Submitted', 'Total Sent', 'Debit', 'Credit', 'Running Balance'
            ]);

            $runningBalance = (float) $balancebd;

            foreach ($transactions as $index => $transaction) {
                $type = 'Deposit';
                $title = 'Deposit';
                $sender = 'N/A';
                $submitted = 'N/A';
                $totalSent = 'N/A';

                if ((int) $transaction->asb_pay_mode === 4) {
                    $campaign = $transaction->smsCampaignId;
                    $type = 'SMS Campaign';
                    $title = optional($campaign)->sci_campaign_title ?: optional($campaign)->sci_campaign_id;
                    $sender = optional(optional($campaign)->sender)->sir_sender_id ?: 'N/A';
                    $submitted = optional($campaign)->sci_total_submitted ?: 0;
                    $totalSent = $submitted;
                } elseif ((int) $transaction->asb_pay_mode === 5) {
                    $campaign = $transaction->loadCampaignId;
                    $type = 'Load Campaign';
                    $title = optional($campaign)->campaign_name ?: optional($campaign)->campaign_id;
                    $submitted = optional($campaign)->total_number ?: 0;
                    $totalSent = $submitted;
                }

                $debit = (float) $transaction->asb_debit;
                $credit = (float) $transaction->asb_credit;
                $runningBalance += $credit - $debit;

                fputcsv($file, [
                    $index + 1,
                    $type,
                    $title,
                    optional($transaction->asb_submit_time)->format('Y-m-d H:i:s'),
                    $sender,
                    $submitted,
                    $totalSent,
                    number_format($debit, 2, '.', ''),
                    number_format($credit, 2, '.', ''),
                    number_format($runningBalance, 2, '.', ''),
                ]);
            }

            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
