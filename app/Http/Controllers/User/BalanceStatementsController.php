<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AccSmsBalance;
use Auth;
use Carbon\Carbon;
use PDF;

class BalanceStatementsController extends Controller
{
    public function balance(Request $request){
        $user = Auth::user()->id;
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        }else{
            $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->subDays(15);
            $q_end_date = Carbon::now();
        }
        $stat = AccSmsBalance::where('asb_pay_to',$user)
                             ->where('asb_deal_type',1)
                             ->whereIn('asb_pay_mode',[1,2,3])
                             ->where('created_at', '>=', $q_start_date)
                             ->where('created_at', '<=', $q_end_date)
                             ->get();
        // dd($stat);
        return view('user.balance-statements',compact('stat','start_date','end_date'));
    }

    public function balance_report_download(Request $request){
        $user = Auth::user()->id;
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        }else{
            $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->subDays(15);
            $q_end_date = Carbon::now();
        }
        $stat = AccSmsBalance::where('asb_pay_to',$user)
                             ->where('asb_deal_type',1)
                             ->where('created_at', '>=', $q_start_date)
                             ->where('created_at', '<=', $q_end_date)
                             ->get();

        // return view('user.balance-statements-pdf',compact('stat'));

        $pdf = PDF::loadView('user.balance-statements-pdf', compact('stat'));
        return $pdf->download('BalanceStatements.pdf');
    }
}
