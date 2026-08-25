<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\EmployeeUser;
use Illuminate\Support\Facades\Validator;
use App\Model\SenderIdUserDefault;
use App\Model\UserDetail;
use GuzzleHttp\Client;

class EmployeeForgotPasswordController extends Controller
{
    public function recover_process(Request $request)
    {
    	$validateData = Validator::make($request->all(), [
    	    'verification_number' => 'required',
    	]);

    	if($validateData->fails()){
    	    session()->flash('message', 'require your number for reset password');
    	    return redirect()->back();
    	}
    	$employee = EmployeeUser::where('phone', $request->verification_number)->select('create_by', 'email', 'employee_p', 'phone')->first();
    	 
    	
    	if($employee){
    	    /*send sms to created user*/
    	    $message = "Your Email is ".$employee->email." and password is: ".$employee->employee_p;
    	    $message = rawurlencode($message);
    	    $number = '88'.$employee->phone;
    	    // $sender_id = "8804445604441";

    	    $user_default_sender_id = SenderIdUserDefault::where('user_id', $employee->create_by)->first();

    	    $number = '88'.$employee->phone;
    	    $sender_id = $user_default_sender_id->sender->sir_sender_id;

    	    $reseller = UserDetail::where('user_id', $employee->create_by)->select('api_key')->first();

    	    $client = new Client();
    	    $url = config('app.url')."/api/v1/send?api_key=".$reseller->api_key."&contacts=".$number."&senderid=".$sender_id."&msg=".$message."&for_registration=resellerToUser";

    	    $res = $client->request('GET', $url);
    	    // $ret = $res->getBody();

    	    session()->flash('message', 'sending password to your phone. please check it. it can take up-to 5 minutes.');
    	    return redirect()->back();

    	}else{
    	    session()->flash('message', 'can\'t find your account. please provide your mobile number to reset password');

    	    return redirect()->back();
    	}
    }
}
