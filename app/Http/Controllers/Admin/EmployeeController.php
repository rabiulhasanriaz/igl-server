<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /*show employee list*/
    public function index()
    {
        
    }

    /*show new employee create form*/
    public function create()
    {
        /*manage from route(web.php)*/
    }

    /*store new employee*/
    public function store(Request $request)
    {
  
    }

    /*update employee information*/
    public function update(Request $request)
    {
        
    }

    public function employee_limit_form_view()
    {
        $resellers = User::where('role', 4)->get();
        return view('admin.reseller.employee_limit_form', compact('resellers'));
    }
    public function employee_limit_process(Request $request) 
    {  
        $reseller_id = $request->user_id;
        $employee_limit_amount = $request->employee_limit_amount;

        $reseller = User::where('id', $reseller_id)->first();
        $reseller->employee_limit = $employee_limit_amount;
        $reseller->save();

        session()->flash('message', 'Employee Limit has been updated');
        session()->flash('type', 'success');

        return redirect()->back();

    }
   

}
