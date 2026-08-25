<?php

namespace App\Http\Controllers\Admin;

use App\Model\AccSmsBalance;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class BalanceController extends Controller
{
    //
    /*show add reseller credit form*/
    public function cdtCreate(){
        $resellers = User::where(['role'=> '4', 'position' => 1])->get();
    	return view('admin.balance.add_fund_credit', compact('resellers'));
    }

    /*store reseller credit*/
    public function cdtStore(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            "user_id" => ['required'],
            "credit_ammount" => ['required'],
            "payment_reference" => ['required'],
            "payment_method" => ['required'],
        ]);

        if($validateData->fails()){
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        if($request->target_time==null){
            $target_time = Carbon::now();
        }else{
            $target_time = date("Y-m-d h:i:s",strtotime($request->target_time));
        }

        try{
            AccSmsBalance::create([
                'asb_paid_by' => Auth::user()->id,
                'asb_pay_to' => $request->user_id,
                'asb_pay_ref' => $request->payment_reference,
                'asb_credit' => $request->credit_ammount,
                'asb_debit' => '0',
                'asb_submit_time' => Carbon::now(),
                'asb_target_time' => $target_time,
                'asb_pay_mode' => $request->payment_method,
                'asb_payment_status' => '1',
                'asb_deal_type' => '1',
            ]);

            session()->flash('type', 'success');
            session()->flash('message', 'successfully added credit balance..');
            return redirect()->back();
        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to add balance..');
            return redirect()->back();
        }
    }



    /*show add reseller debit form*/
    public function dbtCreate(){
        $resellers = User::where(['role'=> '4', 'position' => 1])->get();
		return view('admin.balance.add_fund_debit', compact('resellers'));
    }

    /*store reseller debited amount*/
    public function dbtStore(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            "user_id" => ['required'],
            "debit_amount" => ['required'],
            "payment_reference" => ['required'],
        ]);

        if($validateData->fails()){
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        try{
            AccSmsBalance::create([
                'asb_paid_by' => Auth::user()->id,
                'asb_pay_to' => $request->user_id,
                'asb_pay_ref' => $request->payment_reference,
                'asb_credit' => '0',
                'asb_debit' => $request->debit_amount,
                'asb_submit_time' => Carbon::now(),
                'asb_target_time' => Carbon::now(),
                'asb_pay_mode' => '1',
                'asb_payment_status' => '1',
                'asb_deal_type' => '2',
            ]);

            session()->flash('type', 'success');
            session()->flash('message', 'successfully debited balance..');
            return redirect()->back();
        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to debit balance..');
            return redirect()->back();
        }

    }



    public function show($id){
    	$SmsBalances = AccSmsBalance::where('asb_pay_to', $id)->orderBy('id', 'asc')->get();
    	$user = User::where('id', $id)->first();
        return view('admin.reseller.reseller_transaction_history', compact('SmsBalances', 'user'));
    }
}
