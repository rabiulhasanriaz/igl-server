<?php

namespace App\Http\Controllers\Reseller;

use App\Model\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Model\User;

class ProfileController extends Controller
{
    //
    public function showProfile(){
    	return view('reseller.profile.profile');
    }

    /*update profile*/
    public function updateProfile(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'name' => 'required',
            'company_name' => 'required',
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        try{
            $updUser = User::where('id', Auth::id())->first();
            $updUserDetail = UserDetail::where('user_id', Auth::id())->first();

            

            $updUser->company_name = $request->company_name;
            $updUserDetail->designation = $request->designation;
            $updUserDetail->facebookid = $request->facebookId;
            $updUserDetail->hotline = $request->hotline;
            $updUserDetail->domain_name = $request->website;
            $updUserDetail->logout_url = $request->logout_url;
            $updUserDetail->name = $request->name;

            /*if user set image then upload it adn save*/
            if ($request->hasFile('profile_image')) {
                $files = $request->file('profile_image');
                $name = str_random(20) . $updUser->id . '.' . $files->getClientOriginalExtension();
                $destinationPath = 'assets/uploads/User_Logo';
                $url = $destinationPath . "/" . $name;
                $files->move($destinationPath, $name);
                $updUserDetail->logo = $name;
            }

            $updUser->save();
            $updUserDetail->save();

            session()->flash('type', 'success');
            session()->flash('message', 'Successfully updated your information');
            return redirect()->back();

        }catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to update profile. please try again.....!');
            return redirect()->back();
        }
    }

	public function showChangePasswordForm(){
    	return view('reseller.profile.change_password');
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

                    $message = "Your New Password is: ". $request->new_password ."\nThanks and Greetings From\n IGL Web Ltd.";
                    $message = rawurlencode($message);
                    $number = Auth::user()->cellphone;
                    $senderId = "8801844532630";
                    $apikey = Auth::user()->userDetail->api_key;
                    $client = new Client();
                    $url = "http://sms.iglweb.com/api/v1/send?api_key=".$apikey."&contacts=".$number."&senderid=".$senderId."&msg=".$message;
                    // dd($url);
                    $res = $client->request('GET', $url);
                    $ret = $res->getBody();

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
