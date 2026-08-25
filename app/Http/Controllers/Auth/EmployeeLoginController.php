<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Model\EmployeeUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class EmployeeLoginController extends Controller
{
    public function index(){
    	if( Auth::guard('employee')->check() ){
    		return redirect()->route('employee.index');
    	}
    	return view('employee.login');
    }

    public function login_process(Request $request){

        // if(is_numeric($request->email)){
        //     $getEmail = EmployeeUser::where('phone', $request->email)->first();

        //     if($getEmail){
        //         $email = $getEmail->email;
        //     }else{
        //         session()->flash('message', 'login credential was wrong...');
        //         return redirect()->back();
        //     }
        // }else{
        //     $email = $request->email;
        // }

    	if(Auth::guard('employee')->attempt(['phone'=>$request->email, 'password'=>$request->password ])){

    		$data['employee_info'] = Auth::guard('employee')->user();

    		
    		return redirect()->route('employee.index');
    	}else{
            session()->flash('message', 'Wrong Credentials');
            session()->flash('type', 'danger');
            return view('employee.login');       
        }
    }

    public function logout() {
    	if (Auth::guard('employee')->check()){
    		Auth::guard('employee')->logout();
    		return redirect()->route('auth.employeeLogin');
    	}
    }
}
