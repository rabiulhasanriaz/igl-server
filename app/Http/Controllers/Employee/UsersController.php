<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Model\User;
use App\Model\AccSmsBalance;
use App\Http\Controllers\Controller;
use Auth;

class UsersController extends Controller
{
    public function all_users_info() {
    	$data['allUsers'] = User::where('employee_user_id', Auth::guard('employee')->id() )->get();
    	return view('employee.users.user_list', compact('data', $data));
    }

    

    public function low_balance_users_list() {
    	$data = User::where('employee_user_id', Auth::guard('employee')->id() )->get();

    	$low_users_id = array();

    	foreach( $data as $user ){
    		if ( \BalanceHelper::user_available_balance($user->id) < 2000 ){
    			 $low_users_id[] = $user->id;

    		}

    	}
    	$low_users = User::whereIn('id', $low_users_id)->get();
    	return view('employee.users.low_balance_users', compact('low_users', $low_users) );
    }

    public function transaction_history_particular($user_id) {
    	$transactions =  AccSmsBalance::where('asb_pay_to', $user_id)->whereIn('asb_pay_mode', [1,2,3])->orderBy('asb_submit_time', 'desc')->get();

    	return view('employee.users.transaction_history', compact('transactions', $transactions));
    }
}
