<?php

namespace App\Http\Controllers\Admin;

use App\Model\AccUserCreditHistory;
use App\Model\SmsCamPending;
use App\Model\SmsCampaign_24h;
use App\Model\User;
use App\Model\Operator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $data['active_user'] = User::where('status', '1')->whereIn('role', [4,5])->count();
        $data['suspend_user'] = User::where('status', '2')->whereIn('role', [4,5])->count();
        $data['daily_sms'] = $this->getDailySmsData()->getData();
        $data['last_month_sms'] = AccUserCreditHistory::where('created_at', '>', Carbon::now()->subMonth(1))->sum('uch_sms_count');
        $data['total_sms'] = AccUserCreditHistory::sum('uch_sms_count');

        $dateS = Carbon::now()->startOfMonth()->subMonth(11);
        $dateE = Carbon::now();

        $data['monthly_sms'] = AccUserCreditHistory::select(
            DB::raw('sum(uch_sms_count) as total_sms'),
            DB::raw('sum(uch_sms_cost) as total_sms_cost'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month')
        )
        ->whereBetween('created_at', [$dateS,$dateE])
        ->groupBy('year','month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        $operators = Operator::all();
        $balances = [];

        foreach ($operators as $op) {
            $mnoMap = [
                'GP' => 'GP',
                'Robi' => 'RB',
                'Airtel' => 'RB',
                'Banglalink' => 'BL',
                'Teletalk' => 'TT',
            ];

            $iptspMap = [
                'RangsTel' => 'RT',
                'BanglarPhone' => 'BN',
                'IGL Tel' => 'ADN',
                'Premium' => 'PRM',
                'AmberIT' => 'AIT',
                'FusionNet' => 'FN',
                'Brilliant' => 'BR',
                'Metronet' => 'MTN',
                'RaceOnline' => 'RCO',
                'Mirnet' => 'MN',
                'Bracnet' => 'BN',
            ];

            $code = null;

            if (array_key_exists($op->ope_operator_name, $mnoMap)) {
                $code = $mnoMap[$op->ope_operator_name];
            } elseif (array_key_exists($op->ope_operator_name, $iptspMap)) {
                $code = $iptspMap[$op->ope_operator_name];
            }

            if ($code) {
                $sender = \App\Model\SenderIdVirtualNumber::where('operator_id', $op->id)->first();
                if ($sender) {
                    $balances[$op->ope_operator_name] = $this->getAnsBalance(
                        $code,
                        $sender->sivn_api_user_name,
                        $sender->sivn_api_password
                    );
                } else {
                    $balances[$op->ope_operator_name] = ['error' => 'No SenderIdVirtualNumber found'];
                }
            }
        }

        $data['ans_balances'] = $balances;

        return view('admin.index')->with('data', $data);
    }

    private function getAnsBalance($code, $username, $password)
    {
        $url = "https://api.mnpspbd.com/a2p-proxy-api/api/v1/check-credit-balance";

        $payload = [
            "username" => $username,
            "password" => $password,
            "mno" => $code,
            "apiKey" => "myQ1uzu3mRVWdjVq4A1mV5GscebslZ4y",
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return ['error' => curl_error($ch)];
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    public function loggedInUsers()
    {
        $logged_users = User::whereIn('login_status', [1, 2])
            ->where('last_active_time', '>=', Carbon::now()->subMinutes(2))
            ->where('id', '!=', auth()->id())
            ->get();
        return view('admin.allLoggedUsers', ['logged_users' => $logged_users]);
    }

    /**
     * Get IMMEDIATE queue count (ONLY messages ready to send now)
     * Messages with: NULL target_time OR target_time <= current time
     */
    public function getCount()
    {
        $count = SmsCamPending::where(function($query) {
            $query->whereNull('scp_target_time')
                  ->orWhere('scp_target_time', '<=', Carbon::now());
        })->count();
        
        return response($count);
    }

    /**
     * Get IMMEDIATE queue cost (ONLY messages ready to send now)
     */
    public function getCost()
    {
        $cost = SmsCamPending::where(function($query) {
            $query->whereNull('scp_target_time')
                  ->orWhere('scp_target_time', '<=', Carbon::now());
        })->sum('scp_sms_cost');
        
        return response(number_format($cost, 2, '.', ''));
    }

    /**
     * Get SCHEDULED queue count (ONLY future scheduled messages)
     */
    public function getScheduledCount()
    {
        $count = SmsCamPending::where('scp_target_time', '>', Carbon::now())
                              ->whereNotNull('scp_target_time')
                              ->count();
        return response($count);
    }

    /**
     * Get SCHEDULED queue cost (ONLY future scheduled messages)
     */
    public function getScheduledCost()
    {
        $cost = SmsCamPending::where('scp_target_time', '>', Carbon::now())
                             ->whereNotNull('scp_target_time')
                             ->sum('scp_sms_cost');
        return response(number_format($cost, 2, '.', ''));
    }

    public function getDailySmsData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $dailySms = AccUserCreditHistory::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(uch_sms_count) as sms_count')
            )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($dailySms);
    }

    public function getTodaySmsCounts()
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $smsData = SmsCampaign_24h::select('sct_message')->whereBetween('created_at', [$todayStart, $todayEnd])->get();

        $counts = array_fill(1, 10, 0);

        foreach ($smsData as $sms) {
            $final_text = preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $sms->sct_message);

            if (\SmsHelper::is_unicode($sms->sct_message)) {
                $segments = \SmsHelper::unicode_sms_count($final_text);
            } else {
                $segments = \SmsHelper::text_sms_count($final_text);
            }

            $key = $segments > 10 ? 10 : $segments;
            $counts[$key]++;
        }

        $result = [];
        foreach ($counts as $segments => $count) {
            $result[] = [
                'sms_segments' => $segments,
                'message_count' => $count
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function getMonthlySmsCount()
    {
        try {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();

            $dailyCounts = SmsCampaign_24h::select(
                    DB::raw('DATE(created_at) as date'),
                    'sct_sms_type',
                    DB::raw('COUNT(*) as sms_count')
                )
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date', 'sct_sms_type')
                ->orderBy('date')
                ->get();

            $grouped = $dailyCounts->groupBy('date');

            $dailyData = $grouped->map(function ($rows, $date) {
                $types = [];
                foreach ($rows as $row) {
                    $types[$row->sct_sms_type] = (int) $row->sms_count;
                }
                return [
                    'date' => Carbon::parse($date)->format('M d'),
                    'non_masking' => $types[1] ?? 0,
                    'masking' => $types[2] ?? 0,
                    'total' => ($types[1] ?? 0) + ($types[2] ?? 0),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'month' => $start->format('F Y'),
                'daily_data' => $dailyData,
                'total_count' => $dailyData->sum('total')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
