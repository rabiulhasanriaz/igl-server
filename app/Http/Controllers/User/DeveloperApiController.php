<?php

namespace App\Http\Controllers\User;

use App\Model\User;
use App\Model\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeveloperApiController extends Controller
{
    //
    public function index(){
        $creator = User::where('id', Auth::user()->create_by)->first();
        if(!empty($creator)){
            $domain_url = $creator->userDetail->domain_name;
        }else{
            $domain_url = Auth::user()->userDetail->domain_name;
        }
    	return view('user.developerApi.developer_api', compact('domain_url'));
    }

    /*change api key*/
  public function changeApi()
{
    $randid = time(); // This gives 10-digit timestamp like 1777979236
    // Generate 12 random alphabets
    $randomAlphabets = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 12);
    $api_key = '445' . $randid . Auth::id() . $randomAlphabets;
    
    try {
        $updateApiKey = UserDetail::where('user_id', Auth::id())->first();
        if (!$updateApiKey) {
            session()->flash('type', 'danger');
            session()->flash('message', 'can\'t find this user. try again');
            return redirect()->back();
        }
        $updateApiKey->api_key = $api_key;
        $updateApiKey->save();

        session()->flash('type', 'success');
        session()->flash('message', 'Your api key was changed successfully');
        return redirect()->back();

    }catch (\Exception $e){
        session()->flash('type', 'danger');
        session()->flash('message', 'something was wrong to update api key..');
        return redirect()->back();
    }
}
    public function updateWhiteListIp(Request $request)
{
    try {

        $userDetail = UserDetail::where('user_id', Auth::id())->first();

        if (!$userDetail) {
            session()->flash('type', 'danger');
            session()->flash('message', 'User not found');
            return redirect()->back();
        }

        $userDetail->white_listed_ip = $request->white_listed_ip;
        $userDetail->save();

        session()->flash('type', 'success');
        session()->flash('message', 'White listed IP updated successfully');

        return redirect()->back();

    } catch (\Exception $e) {

        session()->flash('type', 'danger');
        session()->flash('message', 'Something went wrong');

        return redirect()->back();
    }
}
}
