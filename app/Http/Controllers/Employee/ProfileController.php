<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Model\EmployeeUser;
use Auth;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
    	$profile_infos = EmployeeUser::where('id', Auth::guard('employee')->id())->first();
    	
    	$total_debit = \BalanceHelper::getDebit( Auth::guard('employee')->id() );
    	$total_credit = \BalanceHelper::getCredit( Auth::guard('employee')->id() );

    	$total_balance = \BalanceHelper::getEmployeeBalance( Auth::guard('employee')->id() );

    	return view('employee.profile.viewProfile', compact('profile_infos', 'total_debit', 'total_credit', 'total_balance') );
    }

    public function profileUpdate(Request $request)
    {	

        $validatedData = $request->validate([
            'name' => 'required',
            ]);

    	$name = $request->name;
    	$avatar = $request->profile_image;

    	$profile = EmployeeUser::find( Auth::guard('employee')->id() );
    	$profile->name = $name;

    	if ($request->hasFile('profile_image')) {
    	    $files = $request->file('profile_image');
    	    $logo_name = str_random(20) . $profile->id . '.' . $files->getClientOriginalExtension();
    	    $destinationPath = 'assets/uploads/User_Logo';
    	    $url = $destinationPath . "/" . $logo_name;
    	    $files->move($destinationPath, $logo_name);
    	    $profile->avatar = $logo_name;
    	}
    	

    	$profile->save();

    	session()->flash('message', 'Profile Updated successfully');
    	session()->flash('type', 'success');
    	return redirect()->back();
    }
}
