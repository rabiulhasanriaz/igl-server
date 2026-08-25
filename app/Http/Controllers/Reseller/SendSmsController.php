<?php

namespace App\Http\Controllers\Reseller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Model\SenderIdUserDefault;
use App\Model\User;
use App\Model\UserDetail;
use GuzzleHttp\Client;

class SendSmsController extends Controller
{
    public function send_sms_to_all_view() 
    {
    	$total_reseller = User::where('create_by', Auth::id())->where('role', 4)->count();
		$total_user = User::where('create_by', Auth::id())->where('role', 5)->count();

		$total_reseller_list = User::where('create_by', Auth::id())->where('role', 4)->pluck('cellphone')->toArray();
		$numbers_reseller = implode(',', $total_reseller_list);

		$total_user_list = User::where('create_by', Auth::id())->where('role', 5)->pluck('cellphone')->toArray();
		$numbers_user = implode(',', $total_user_list);

		$total_user_reseller = User::where('create_by', Auth::id())->whereIn('role', [4,5])->pluck('cellphone')->toArray();
		$numbers_total = implode(',', $total_user_reseller);

		// dd($numbers_total);
    	
    	return view('reseller.others.sendSmsToAll', compact('total_user', 'total_reseller','numbers_user','numbers_reseller','numbers_total'));
    }

    public function send_sms_to_all_process(Request $request) 
    {
		// dd($request->all());
    	$request->validate([
    			'message' => 'required',
    		]);
		$num = $request->reseller_user;
		// dd($num);
    	$message = $request->message;
        $message = urlencode($message);


    	$user_default_sender_id = SenderIdUserDefault::where('user_id', Auth::id())->first();
    	$sender_id = $user_default_sender_id->sender->sir_sender_id;

    	$reseller = UserDetail::where('user_id', Auth::id())->select('api_key')->first();


    	$client = new Client();
    	$url = "sms.iglweb.com"."/api/v1/send?api_key=".$reseller->api_key."&contacts=".$num."&senderid=".$sender_id."&msg=".$message."&for_registration=resellerToUser";
		
    	$res = $client->request('GET', $url);
    	$ret = $res->getBody()->getContents();
    	
    	$response = json_decode($ret);

    	$message = 'Message has been sent to all users and reseller under you.';
    	$type = 'success';

    	if ( $response->code == '445120' ){ //insufficient balance
    		$message = $response->message;
    		$type = 'danger';
    	}

    	session()->flash('message', $message);
    	session()->flash('type', $type);
    	return redirect()->back();
    }
}
