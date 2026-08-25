<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\EmployeeUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    public function change_password()
    {
    	return view('employee.change_password_form');
    }

    public function change_password_process(Request $request)
    {
    	$validateData = Validator::make($request->all(), [
    	    'old_password' => 'required',
    	    'new_password' => 'required',
    	    're_password' => 'required',
    	]);

    	if($validateData->fails()){
    	    return redirect()->back()->withErrors($validateData);
    	}


    	if(Hash::check($request->old_password,Auth::guard('employee')->user()->getAuthPassword())) {
    	    if($request->new_password == $request->re_password){
    	        try{
    	            $updPassword = EmployeeUser::where('id', Auth::guard('employee')->user()->id)->first();
    	            
    	            $updPassword->employee_p = $request->new_password;
    	            $updPassword->password = bcrypt($request->new_password);

    	            $updPassword->save();

    	            session()->flash('type', 'success');
    	            session()->flash('message', 'Successfully changed your password.....!');
    	            return redirect()->back();

    	        }catch (\Exception $e){
    	            session()->flash('type', 'danger');
    	            session()->flash('message', 'something went wrong to change password. please try again........!');
    	            return redirect()->back();
    	        }
    	    }
    	    else{
    	        session()->flash('type', 'danger');
    	        session()->flash('message', 'password and confirm password didn\'t matched. please try again........!');
    	        return redirect()->back();
    	    }


    	} else {
    	    session()->flash('type', 'danger');
    	    session()->flash('message', 'didn\'t matched your password with old password. please try again........!');
    	    return redirect()->back();
    	}
    }
}
