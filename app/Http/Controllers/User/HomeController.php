<?php

namespace App\Http\Controllers\User;

use App\Model\AccSmsBalance;
use App\Model\AccUserCreditHistory;
use App\Helpers\BalanceHelper;
use Carbon\Carbon;
use App\Model\AccSmsRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(){
        $data['transactions'] = AccSmsBalance::whereIn('asb_pay_mode', [1,2,3])
            ->where('asb_pay_to', Auth::id())
            ->orderBy('id', 'DESC')
            ->take(5)
            ->get();

        $data['campaign_rejects'] = AccSmsBalance::where('asb_pay_mode', 6)
            ->where('asb_pay_to', Auth::id())
            ->orderBy('id', 'DESC')
            ->take(3)
            ->get();

        $data['last_week_sms'] = AccUserCreditHistory::where('created_at', '>', Carbon::now()->subWeek(1))
            ->where('user_id', Auth::id())->sum('uch_sms_count');
        $data['last_week_cost'] = AccUserCreditHistory::where('created_at', '>', Carbon::now()->subWeek(1))
            ->where('user_id', Auth::id())->sum('uch_sms_cost');

        $data['last_month_sms'] = AccUserCreditHistory::where('created_at', '>', Carbon::now()->subMonth(1))
            ->where('user_id', Auth::id())->sum('uch_sms_count');
        $data['last_month_cost'] = AccUserCreditHistory::where('created_at', '>', Carbon::now()->subMonth(1))
            ->where('user_id', Auth::id())->sum('uch_sms_cost');

        $dateS = Carbon::now()->startOfMonth()->subMonth(11);
        $dateE = Carbon::now();
        $data['monthly_sms'] = AccUserCreditHistory::select(
                DB::raw('sum(uch_sms_count) as total_sms'), 
                DB::raw('sum(uch_sms_cost) as total_sms_cost'),  
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )
            ->whereBetween('created_at',[$dateS,$dateE])
            ->where('user_id', Auth::id())
            ->groupby('year','month')
            ->orderBy('id', 'desc')
            ->get();

        return view('user.index', compact('data'));
    }

    public function getStatistics(Request $request)
    {
        // Check if it's an AJAX request
        if ($request->ajax()) {
            try {
                $user_id = Auth::id();
                
                $statistics = [
                    'sms_credit' => AccSmsRate::with('operator')->where('user_id', $user_id)->get(),
                    'balance_bd' => \BalanceHelper::user_available_balance($user_id)
                ];

                return response()->json($statistics);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to load statistics: ' . $e->getMessage()], 500);
            }
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }
}
