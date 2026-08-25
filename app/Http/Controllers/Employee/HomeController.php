<?php

namespace App\Http\Controllers\Employee;

use App\Model\EmployeeUser;
use App\Model\EmployeeUserCommission;
use App\Model\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Auth;

class HomeController extends Controller
{
    public function index() {

    	$data['users'] = User::where('employee_user_id', Auth::guard('employee')->id() )->count();
    	$data['balance'] = \BalanceHelper::getEmployeeBalance(Auth::guard('employee')->id());

    	$data['debit'] = \BalanceHelper::getDebit(Auth::guard('employee')->id());
    	$data['credit'] = \BalanceHelper::getCredit(Auth::guard('employee')->id());

    	$data['transactions'] = EmployeeUserCommission::where('eu_id', Auth::guard('employee')->id())->take(4)->get();


    	return view('employee.index', compact('data'));
    }
}
