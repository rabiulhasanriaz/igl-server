<?php

namespace App\Http\Controllers\reseller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AccSmsBalance;
use App\Model\User;
use App\Model\EmployeeUserCommission;
use App\Model\EmployeeUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Helpers\BalanceHelper;

class BalanceController extends Controller
{
    /*show user credit balance form*/
    public function cdtCreate()
    {
        $resellers = User::where(['create_by'=> Auth::id()])->orderBy('company_name','asc')->get();
        $paymentable_balance = BalanceHelper::reseller_paymentable_balance(Auth::id());

        return view('reseller.balance.add_fund_credit', compact('resellers', 'paymentable_balance'));
    }


    // ======================Update 22-10-23=====================START
    public function getTransactionHistory(Request $request) {

        $userId = $request->input('userId');
        
        $transactionHistory = AccSmsBalance::whereIn('asb_pay_mode', [1,2,3])
            ->where('asb_pay_to', $userId)
            ->orderBy('asb_target_time', 'DESC')
            ->take(5)
            ->get();

        return view('reseller.ajax.transaction_ajax', compact('transactionHistory'));
    }
    // ======================Update 22-10-23=======================END

    /*store reseller credit*/
    public function cdtStore(Request $request)
    {
        $amount = str_replace(',','',$request->credit_ammount);
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

        if($amount>BalanceHelper::reseller_paymentable_balance(Auth::id())){
            session()->flash('type', 'danger');
            session()->flash('message', 'Warning ! You can\'t pay more then your paymentable balance....!');

            return redirect()->back();
        }

        // Employee Commission calculation Editing start
           /* $data = BalanceHelper::get_employee_commission($request->user_id, $request->credit_ammount);
               
               $add_commission = EmployeeUserCommission::create([
                   'eu_id' => $data['employee_id'],
                   'eu_ref_id' => '0',
                   'euc_credit' => $data['commission_amount'],
                   'euc_debit' => 0,
                   'euc_status' => 1,
                   ]);*/
        

        try{
            DB::transaction(function () use ($request, $target_time) {
            
            $amount = str_replace(',','',$request->credit_ammount);
                BalanceHelper::addCredit(
                    Auth::user()->id,
                    $request->user_id,
                    $request->payment_reference,
                    $amount,
                    $request->payment_method,
                    1,
                    1,
                    $target_time
                );

                $added_credit = AccSmsBalance::where('asb_pay_to', $request->user_id)
                    ->where('asb_pay_ref', $request->payment_reference)
                    ->where('asb_credit', $amount)
                    ->orderBy('id', 'desc')
                    ->first();

               $data = BalanceHelper::get_employee_commission($request->user_id, $amount);
               $comission = User::where('id',$request->user_id)
                                ->first();
                $total = ($amount* $comission->flexi_emp_comission)/100;
               if ( $data != 0 ){
                   $add_commission = EmployeeUserCommission::create([
                       'eu_id' => $comission->employee_user_id,
                       'eu_ref_id' => $added_credit ? $added_credit->id : 0,
                       'euc_credit' => $total,
                       'euc_debit' => 0,
                       'euc_status' => 1,
                       ]);
               }

            });

            if ($request->payment_method == 1) {
                $message = "Your Account is Credited with Balance: ". $amount ."\nFor the purpose of: " . $request->payment_reference . "\nPayment Method is: Cash\nIGL Web Ltd."; 
            }elseif ($request->payment_method == 2) {
                $message = "Your Account is Credited with Balance: ". $amount ."\nFor the purpose of: " . $request->payment_reference . "\nPayment Method is: Bank Deposit\nDate:" . $request->target_time . "\nIGL Web Ltd."; 
            }elseif ($request->payment_method == 3) {
                $message = "Your Account is Credited with Balance: ". $amount ."\nFor the purpose of: " . $request->payment_reference . "\nPayment Method is: Check\nDate:" . $request->target_time . "\nIGL Web Ltd."; 
            }

            
            $message = rawurlencode($message);
            $number = \OtherHelpers::userNumber($request->user_id);
            $senderId = "8801844532630";
            $apikey = "445156057064961560570649";
            $client = new Client();
            $url = "http://sms.iglweb.com/api/v1/send?api_key=".$apikey."&contacts=".$number."&senderid=".$senderId."&msg=".$message;
            // dd($url);
            $res = $client->request('GET', $url);
            $ret = $res->getBody();

            // Editing end

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


    public function dbtCreate()
    {
        $resellers = User::where(['create_by'=> Auth::id()])->get();
        return view('reseller.balance.add_fund_debit', compact('resellers'));
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

        $checkUser = User::where(['id'=>$request->user_id, 'create_by'=>Auth::id()])->first();
        if(!$checkUser){
            session()->flash('type', 'danger');
            session()->flash('message', 'Warning! You can\'t get balance from user who is not under you....!');
            return redirect()->back();
        }
        elseif ($request->debit_amount>BalanceHelper::user_available_balance($request->user_id)){
            session()->flash('type', 'danger');
            session()->flash('message', 'Warning ! You can\'t withdraw more then this user balance....!');

            return redirect()->back();
        }

        try{
            DB::transaction(function () use ($request) {
                
                BalanceHelper::addDebit(
                    Auth::user()->id,
                    $request->user_id,
                    $request->payment_reference,
                    $request->debit_amount,
                    1,
                    1,
                    2,
                    Carbon::now()
                );

                $added_debit = AccSmsBalance::where('asb_pay_to', $request->user_id)
                    ->where('asb_pay_ref', $request->payment_reference)
                    ->where('asb_debit', $request->debit_amount)
                    ->orderBy('id', 'desc')
                    ->first();

                $data = BalanceHelper::get_employee_commission($request->user_id, $request->debit_amount);
                
                if ( $data != 0 ){
                    $removed_commission = EmployeeUserCommission::create([
                    'eu_id' => $data['employee_id'],
                    'eu_ref_id' => $added_debit ? $added_debit->id : 0,
                    'euc_credit' => 0,
                    'euc_debit' => $data['commission_amount'],
                    'euc_status' => 2,
                    ]);
                }
            
            });

            session()->flash('type', 'success');
            session()->flash('message', 'successfully debited balance..');
            return redirect()->back();
        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to debit balance..'.$e->getMessage());
            return redirect()->back();
        }

    }


    public function show($id)
    {
	    $checkUser = User::where(['create_by'=>Auth::id(), 'id'=>$id])->first();
	    if($checkUser) {
            $SmsBalances = AccSmsBalance::where('asb_pay_to', $id)->orderBy('id', 'asc')->get();
            $user = User::where('id', $id)->first();
            return view('reseller.users.user_transaction_history', compact('SmsBalances', 'user'));
        }elseif ($id==Auth::id()){
            $SmsBalances = AccSmsBalance::where('asb_pay_to', $id)->orderBy('id', 'asc')->get();
            $user = User::where('id', $id)->first();
            return view('reseller.users.user_transaction_history', compact('SmsBalances', 'user'));
        }
        else{
            $users = User::with('userDetail')->where('create_by', Auth::id())->whereNotIn('status', ['3'])->get();
            session()->flash('type', 'danger');
            session()->flash('message', 'Unknown user...!');
            return redirect()->route('reseller.user.index', compact('users'));
        }
    }

    public function totalTransactionHistory()
    {
        $resellers = User::where(['create_by'=> Auth::id()])->get();
        return view('reseller.balance.transaction_history', compact('resellers'));
    }
}
