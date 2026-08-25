<?php

namespace App\Http\Controllers\User;

use App\Model\User;
use App\Model\UserDetail;
use App\Model\SenderIdUserDefault;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ProfileController extends Controller
{
    /*show user profile*/
    public function showProfile(){
    	return view('user.profile.profile');
    }

    /*update user profile*/
    public function updateProfile(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'name' => 'required',
            'company_name' => 'required',
            'designation' => 'required'
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        try{
            $updUser = User::where('id', Auth::id())->first();
            $updUserDetail = UserDetail::where('user_id', Auth::id())->first();
            $updUserDetail->otp_check = $request->has('otp_check') ? 1 : 0;

            $updUser->company_name = $request->company_name;
            $updUserDetail->designation = $request->designation;
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

    public function updateFlexipinForm()
    {
        return view('user.profile.updateFlexipinForm');
    }
    public function updateFlexipin(Request $request)
    {
        $validated_data = $request->validate([
            'new_pin' => "required|min:4|numeric|confirmed",
        ]);

        if ( isset($request->old_pin) and $request->old_pin != auth()->user()->flexipin ){
                return redirect()->back()->with(['type'=>'danger', 'message'=>'Incorrect Pin! Please contact with your resseler.']);
        }
        try{
            $user = User::find(auth()->user()->id);
            $user->flexipin = $request->new_pin;
            $user->save();

            return redirect()->back()->with(['type'=>'success', 'message'=>'Flexiload pin updated successfully']);
        }catch(\Exception $e){
            return redirect()->back()->with(['type'=>'danger', 'message'=>'Something Wrong']);
        }
        dd($request);
    }

    /*show change password form*/
	public function showChangePasswordForm(){
    	return view('user.profile.change_password');
    }

    /*update user password*/
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
                    
                    $defaultSenderId = SenderIdUserDefault::where('user_id', Auth::id())->first();
                    $message = "Your New Password is: ". $request->new_password ."\nThanks and Greetings From\n IGL Web Ltd.";
                    $message = rawurlencode($message);
                    $number = Auth::user()->cellphone;
                    $senderId = $defaultSenderId->sender->sir_sender_id;
                    $apikey = Auth::user()->userDetail->api_key;
                    $client = new Client();
                    $url = "http://sms.iglweb.com/api/v1/send?api_key=".$apikey."&contacts=".$number."&senderid=".$senderId."&msg=".$message;
                    // dd($url);
                    $res = $client->request('GET', $url);
                    $ret = $res->getBody();

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
    // In app/Http/Controllers/User/ProfileController.php

public function updateOTP(Request $request)
{
    try {
        $request->validate([
            'otp_check' => 'required|in:0,1'
        ]);
        
        $user = Auth::user();
        
        // Update or create user detail
        $userDetail = $user->userDetail;
        if (!$userDetail) {
            $userDetail = new \App\Models\UserDetail();
            $userDetail->user_id = $user->id;
        }
        
        $userDetail->otp_check = $request->otp_check;
        $userDetail->save();
        
        return response()->json([
            'success' => true,
            'message' => 'OTP setting updated successfully',
            'otp_check' => $request->otp_check
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function password_for_pin(Request $request)
{
    // Check if input password matches user's stored password
    if (Auth::user()->userDetail['user_p'] == $request->password) {
        $mobile_number = Auth::user()->cellphone;

        // Get user's default sender ID
        $senderDefault = \App\Model\SenderIdUserDefault::where('user_id', Auth::user()->id)->first();
        if (!$senderDefault || !$senderDefault->sender) {
            return redirect()->back()->with(['error' => 'Sender ID not configured for this user.']);
        }

        $sender_id = $senderDefault->sender->sir_sender_id;

        // Prepare SMS
        $message = "Your Flexipin is: ". Auth::user()->flexipin . "\nThanks For using our Service.";
        $message = urlencode($message);

        $api_key = Auth::user()->userDetail->api_key ?? 'default_api_key'; // use user's API key if exists

        $client = new \GuzzleHttp\Client();
        $api_url = "http://sms.iglweb.com/api/v1/send?api_key="
            . $api_key
            . "&contacts=" . $mobile_number
            . "&senderid=" . $sender_id
            . "&msg=" . $message;

        try {
            $response = $client->request('GET', $api_url);
            $json_response = $response->getBody()->getContents();
            $api_response = json_decode($json_response);

            if (isset($api_response->code) && $api_response->code == "445000") {
                return redirect()->back()->with(['success' => 'Pin Number Successfully Sent to your number.']);
            } else {
                return redirect()->back()->with(['error' => 'Something went wrong, please contact your admin.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'SMS sending failed: ' . $e->getMessage()]);
        }

    } else {
        return redirect()->back()->with(['err' => "Password Doesn't Match"]);
    }
}
  public function developerApi() // Changed from developer_api to developerApi
{

        $creator = User::where('id', Auth::user()->create_by)->first();
        if(!empty($creator)){
            $domain_url = $creator->userDetail->domain_name;
        }else{
            $domain_url = Auth::user()->userDetail->domain_name;
        }
    return view('user.developer_api',compact('domain_url'));
}
}
