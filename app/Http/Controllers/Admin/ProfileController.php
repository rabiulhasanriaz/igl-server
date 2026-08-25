<?php

namespace App\Http\Controllers\Admin;

use App\Model\User;
use App\Model\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    //
    /*show password change form*/
    public function showChangePasswordForm(){
    	return view('admin.profile.change_password');
    }


    /*update password*/
    public function updatePassword(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
            're_password' => 'required',
        ]);

        if($validateData->fails()){
            return redirect()->back()->withErrors($validateData);
        }


        if(Hash::check($request->old_password,Auth::user()->getAuthPassword())) {
            if($request->new_password == $request->re_password){
                try{
                    $updPassword = User::where('id', Auth::user()->id)->first();
                    
                    $updPasswordDet = UserDetail::where('user_id', Auth::id())->first();
                    $updPasswordDet->user_p = $request->new_password;
                    $updPasswordDet->save();

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
