<?php

namespace App\Http\Controllers\User;

use App\Jobs\InsertSms;
use App\Model\AccSmsBalance;
use App\Model\AccUserCreditHistory;
use App\Model\LoadFlexibook;
use App\Model\LoadFlexibooksData;
use App\Model\PhonebookCampaignCategory;
use App\Model\PhonebookCampaignContact;
use App\Model\PhonebookCategory;
use App\Model\PhonebookContact;
use App\Model\SenderIdRegister;
use App\Model\SenderIdUser;
use App\Model\SenderIdUserDefault;
use App\Model\SmsCampaignId;
use App\Model\SmsCamPending;
use App\Model\SystemConfiguration;
use App\Model\User;
use App\Model\UserDetail;
use Carbon\Carbon;
//use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
//use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Importer;
use stdClass;

class SmsSendController extends Controller
{

    private $campaign_permission;

    public function __construct()
    {
        $system_configuration = SystemConfiguration::first();
        if (!empty($system_configuration)) {
            $this->campaign_permission = ($system_configuration->campaign_permission == '1')? 0 : 1;
        } else {
            $this->campaign_permission = 1;
        }
    }

    /*show send sms page*/
    public function create()
    {
//        dd(Auth::user()->senderIds);
        $defaultSenderId = SenderIdUserDefault::where('user_id', Auth::id())->first();
        /*$phonebook = PhonebookContact::where('user_id', Auth::id())->groupBy('category_id')->get();
        foreach ($phonebook as $book){
            echo $book->Category->Contacts->count()."<br>";
        }*/
    //    die();
        $phonebookCategories = PhonebookCategory::where('user_id', Auth::id())->orderBy('id', 'asc')->get();

        $employeeBookCategories = LoadFlexibook::where('user_id', Auth::id())->orderBy('name', 'asc')->get();
        //        dd($phonebookCategories);
        return view('user.messaging.send_sms', compact('defaultSenderId',
            'phonebookCategories',
            'employeeBookCategories'
        ));
//        return view('user.messaging.send_sms', compact('defaultSenderId'));

    }

    /*insert single sms content*/
    public function storeSingleSms(Request $request)
    {
        $sender = SenderIdRegister::where('id',$request->sender_id)->first();

        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'sender_id' => 'required',
            'cell_phone' => 'required',
            'message' => 'required',
            'schedule' => 'required',
        ]);
        $request->message = trim($request->message);

        if ($validateData->fails()) {
            $res = new stdClass();
            $res->errors = $validateData->errors()->all();
            die(json_encode($res));
        }
        if ($request->schedule == '2') {
            $validateData1 = Validator::make($request->all(), [
                'target_time' => 'required',
            ]);

            if ($validateData1->fails()) {
                $res = new stdClass();
                $res->errors = $validateData1->errors()->all();
                die(json_encode($res));
            }

            $target_time = date('Y-m-d H:i:s', strtotime($request->target_time));

        } else {
            $target_time = Carbon::now();
        }

        /*check requested sender id is registered or not for this user*/
        $checkSenderId = SenderIdUser::where(['user_id' => Auth::id(), 'sender_id' => $request->sender_id])->first();
        if (!$checkSenderId) {
            $res = new stdClass();
            $res->error = 'Warning! can\'t find your sender id . please try again...';
            die(json_encode($res));
        } else {

            /*get all number in a array*/
            $allContacts = explode(PHP_EOL, $request->cell_phone);
            $validNumbers = array();
            foreach ($allContacts as $contact) {
                $number = \PhoneNumber::addNumberPrefix($contact);
                if (\PhoneNumber::isValid($number)) {
                    $validNumbers[] = $number;
                }
            }
            /*get unique number*/
            $validUniqueNumbers = array_unique($validNumbers);
            if (count($validUniqueNumbers) < 1) {
                $res = new stdClass();
                $res->error = 'All number are invalid...';
                die(json_encode($res));
            }

            /*count how many numbers in an operator*/
            /*sms count*/
            $final_text = preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $request->message);
            if (\SmsHelper::is_unicode($request->message)) {
                $smsType = 'unicode'; //unicode
                $sms_number = \SmsHelper::unicode_sms_count($final_text);

            } else {
                $smsType = 'text'; //text
                $sms_number = \SmsHelper::text_sms_count($final_text);
                // dd($sms_number);
            }

            $isMasking = \SmsHelper::isMasking($request->sender_id);
            $total_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, Auth::id());

            if(($smsType=='unicode') && ($isMasking==true)){
                $strLength = \SmsHelper::unicode_sms_count($final_text);
                if($strLength>315){
                    $res = new stdClass();
                    $res->error = 'Warning! Masking-Unicode sms can\'t be more then 315 character...';
                    die(json_encode($res));
                }
            }

            if (\App\Helpers\BalanceHelper::user_available_balance(Auth::id()) < $total_cost) {
                $res = new stdClass();
                $res->error = 'Warning! insufficient Balance. please recharge first...';
                die(json_encode($res));

            } elseif (\App\Helpers\BalanceHelper::check_parents_available_balance(Auth::id(), $sms_number, $validUniqueNumbers, $isMasking) == false) {
                $res = new stdClass();
                $res->error = 'Warning! your reseller don\'t have enough balance . told him to recharge first...';
                die(json_encode($res));
            } elseif ($isMasking == true && mb_strlen($request->message) > 500) { 
                $res = new stdClass();
                $res->error = 'Message character limit exceeded. Maximum 500 characters allowed.';
                die(json_encode($res));
            } else {

                try {
                    $campaign_id = Auth::id() . time() . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9);
                               if ($isMasking === true) {
                                    $sms_masking_type = '2'; // Masking
                                } elseif ($isMasking === 'iptsp') {
                                    $sms_masking_type = '1'; // Still non-masking
                        
                                } else {
                                    $sms_masking_type = '1'; // Non-masking
                                }

                    $total_sms_number = $sms_number*count($validUniqueNumbers);

                    $browser_info = \SmsHelper::getBrowser();
                    $br = $browser_info['name']." | ".$browser_info['version'];
                    $os = \SmsHelper::os_info($_SERVER['HTTP_USER_AGENT']);

                    $br = $br.' | '.$os;

                    $campaign_permission = UserDetail::where('user_id',Auth::id())->first();

                    if (count($validUniqueNumbers) >= 10 && $campaign_permission->campaign_permission == 1) {
                        $campaign_accept_status = 1;
                    }elseif ($smsType = 'unicode') {
                        $campaign_accept_status = 1;
                    }elseif (count($validUniqueNumbers) >= 10) {
                        $campaign_accept_status = $this->campaign_permission;
                    } else {
                        $campaign_accept_status = 1;
                    }

                    $sms_sender_op = null;
                    
                    if ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88018' || substr($sender->sir_sender_id,0,5) == '88016')) {
                        $sms_sender_op = 1; // Robi and airtel
                    }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88017' || substr($sender->sir_sender_id,0,5) == '88013')) {
                        $sms_sender_op = 2; // GP
                    }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88019' || substr($sender->sir_sender_id,0,5) == '88014')){
                        $sms_sender_op = 3; // Banglalink
                    }elseif ($sms_masking_type == '1' && substr($sender->sir_sender_id,0,5) == '88015'){
                        $sms_sender_op = 4; // Teletalk
                    }

                    foreach ($validUniqueNumbers as $value) {
                        $op = \PhoneNumber::checkOperator($value);
                    }

                    $insertCampaign = SmsCampaignId::create([
                        'user_id' => Auth::id(),
                        'sender_id' => $request->sender_id,
                        'sci_campaign_title' => $request->campaign_title ?? $campaign_id,
                        'sci_campaign_id' => $campaign_id,
                        'sci_total_submitted' => $total_sms_number,
                        'sci_total_cost' => $total_cost,
                        'sci_campaign_type' => $request->schedule, /*1=instant, 2=Schedule */
                        'sci_deal_type' => '1', /* 1=SMS, 2=Campaign */
                        'sci_sms_type' => $op->id == 2 ? 1 : $sms_masking_type, /*1=NonMasking, 2=Masking*/
                        'sci_sender_operator' => $sms_sender_op, 
                        'sci_dynamic_type' => '0',/*1=dynamic, 0=general*/
                        'sci_targeted_time' => $target_time,
                        'sci_campaign_status' => $campaign_accept_status,
                        'sci_browser' => $br,
                        'sci_mac_address' => null,
                        'sci_ip_address' => $request->ip()

                    ]);

                    foreach ($validUniqueNumbers as $number) {
                        $operator = \PhoneNumber::checkOperator($number);

                        $messageWithoutEmojis = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', '', $request->message);

                        SmsCamPending::create([
                            'user_id' => Auth::id(),
                            'sender_id' =>  $request->sender_id,
                            'campaign_id' => $insertCampaign->id,
                            'scp_cell_no' => $number,
                            'scp_message' => preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $messageWithoutEmojis),
                            'scp_sms_cost' => \App\Helpers\BalanceHelper::singleSmsCost($sms_number, $number, $isMasking, Auth::id()),
                            'operator_id' => $operator['id'],
                            'scp_campaign_type' => $request->schedule, /*1=instant, 2=Schedule */
                            'scp_deal_type' => '1', /* 1=SMS, 2=Campaign */
                            'scp_sms_type' => $sms_masking_type, /*1=NonMasking, 2=Masking*/
                            'scp_sms_id' => '0',
                            'scp_tried' => '0', /*Try For Send */
                            'scp_picked' => '0', /*0=not try, 1= try */
                            'scp_sms_text_type' => $smsType, /*SMS type=text/unicode*/
                            'scp_target_time' => $target_time,
                            'scp_campaign_status' => $campaign_accept_status,
                            'scp_status' => '1',

                        ]);
                    }

                    /*debit user balance*/
                    $user_position = Auth::user()->position;
                    $user_id = Auth::id();
                    $user_det = User::where('id', $user_id)->first();

                    while ($user_position >= 1) {
                        /*get total cost*/
                        foreach ($validUniqueNumbers as $value) {
                            $op = \PhoneNumber::checkOperator($value);
                        }

                        
                            $campaign_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $user_det->id);
                        
                        
                        \App\Helpers\BalanceHelper::addDebit(
                            $user_det->create_by,
                            $user_det->id,
                            $campaign_id,
                            $campaign_cost,
                            4,
                            1,
                            2,
                            $target_time
                        );

                        $user_det = User::where('id', $user_det->create_by)->first();
                        $user_position = $user_det->position;
                    }

                    /*add user credit history*/
                    AccUserCreditHistory::create([
                        'campaign_id' => $insertCampaign->id,
                        'user_id' => Auth::id(),
                        'uch_sms_count' => $total_sms_number,
                        'uch_sms_cost' => $total_cost,
                    ]);
                    
                    $res = new stdClass();
                    $res->success = 'Message has been sent';
                    $res->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                    die(json_encode($res));

                } catch (\Exception $e) {
                    $res = new stdClass();
                    $res->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                    $res->error = 'Something was wrong to sent sms. please contact with admin!!!'.$e->getMessage();
                    die(json_encode($res));
                }
            }
        }
    }


    /*store upload file sms*/
    public function storeUploadFileSms(Request $request)
    {
        $sender = SenderIdRegister::where('id',$request->sender_id)->first();
        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'sender_id' => 'required',
            'sms_file' => 'required',
            'message' => 'required',
            'schedule' => 'required',
        ]);

        $request->message = trim($request->message);

        if ($validateData->fails()) {
            $res = new stdClass();
            $res->errors = $validateData->errors()->all();
            die(json_encode($res));
        }
        if ($request->schedule == '2') {
            $validateData1 = Validator::make($request->all(), [
                'target_time' => 'required',
            ]);

            if ($validateData1->fails()) {
                $res = new stdClass();
                $res->errors = $validateData1->errors()->all();
                die(json_encode($res));
            }

            $target_time = date('Y-m-d H:i:s', strtotime($request->target_time));

        } else {
            $target_time = Carbon::now()->toDateTimeString();
        }

        /*check requested sender id is registered or not for this user*/
        $checkSenderId = SenderIdUser::where(['user_id' => Auth::id(), 'sender_id' => $request->sender_id])->first();
        if (!$checkSenderId) {

            $res = new stdClass();
            $res->error = 'Warning! can\'t find your sender id . please try again...';
            die(json_encode($res));
        } else {

            $file = Input::file('sms_file');
            $filename = $request->file('sms_file')->getClientOriginalName();

            $fileType = \FileRead::getFileType($filename);
            $allContacts = array();
            if ($fileType == "Excel") {
                $fileContents = Importer::make('Excel')->load($file)->getCollection();
                foreach ($fileContents as $fileContent) {
                    $allContacts[] = $fileContent[0];
                }
            } elseif ($fileType == "Csv") {
                $fileContents = Importer::make('Csv')->load($file)->getCollection();
                foreach ($fileContents as $fileContent) {
                    $allContacts[] = $fileContent[0];
                }
            } elseif ($fileType == "Text") {
                $fileContent = File::get($file);
                $allContacts = explode(PHP_EOL, $fileContent);
            } else {
                $res = new stdClass();
                $res->error = 'Invalid file...';
                die(json_encode($res));
            }

            $validNumbers = array();
            foreach ($allContacts as $contact) {
                $number = \PhoneNumber::addNumberPrefix($contact);
                if (\PhoneNumber::isValid($number)) {
                    $validNumbers[] = $number;
                }
            }

            /*get unique number*/
            $validUniqueNumbers = array_unique($validNumbers);

            if (count($validUniqueNumbers) < 1) {
                $res = new stdClass();
                $res->error = 'All number are invalid...';
                die(json_encode($res));
            }

            /*sms count*/
            $final_text = preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $request->message);
            if (\SmsHelper::is_unicode($request->message)) {
                $smsType = 'unicode'; //unicode
                $sms_number = \SmsHelper::unicode_sms_count($final_text);

            } else {
                $smsType = 'text'; //text
                $sms_number = \SmsHelper::text_sms_count($final_text);
            }

            $isMasking = \SmsHelper::isMasking($request->sender_id);

            if(($smsType=='unicode') && ($isMasking==true)){
                $strLength = \SmsHelper::unicode_sms_count($final_text);
                if($strLength>315){
                    $res = new stdClass();
                    $res->error = 'Warning! Masking-Unicode sms can\'t be more then 315 character...';
                    die(json_encode($res));
                }
            }

            $total_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, Auth::id());


            if (\App\Helpers\BalanceHelper::user_available_balance(Auth::id()) < $total_cost) {
                $res = new stdClass();
                $res->error = 'Warning! you haven\'t enough balance . please recharge first...';
                die(json_encode($res));

            } elseif (\App\Helpers\BalanceHelper::check_parents_available_balance(Auth::id(), $sms_number, $validUniqueNumbers, $isMasking) == false) {
                $res = new stdClass();
                $res->error = 'Warning! your reseller don\'t have enough balance . told him to recharge first...';
                die(json_encode($res));

            } elseif ($isMasking == true && mb_strlen($request->message) > 500) { 
                $res = new stdClass();
                $res->error = 'Message character limit exceeded. Maximum 500 characters allowed.';
                die(json_encode($res));
            } else {

                try {

                    $redisInfo = \Redis::info();

                    $total_sms_number = $sms_number*count($validUniqueNumbers);
                    $campaign_id = Auth::id() . time() . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9);

                                 if ($isMasking === true) {
                                    $sms_masking_type = '2'; // Masking
                                } elseif ($isMasking === 'iptsp') {
                                    $sms_masking_type = '1'; // Still non-masking
                        
                                } else {
                                    $sms_masking_type = '1'; // Non-masking
                                }
                    $campaign_permission = UserDetail::where('user_id',Auth::id())->first();

                    if (count($validUniqueNumbers) >= 10 && $campaign_permission->campaign_permission == 1) {
                        $campaign_accept_status = 1;
                    }elseif ($smsType == 'unicode') {
                        $campaign_accept_status = 1;
                    }elseif (count($validUniqueNumbers) >= 10) {
                        $campaign_accept_status = $this->campaign_permission;
                    } else {
                        $campaign_accept_status = 1;
                    }

                    $sms_sender_op = null;
                    
                    if ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88018' || substr($sender->sir_sender_id,0,5) == '88016')) {
                        $sms_sender_op = 1; // Robi and airtel
                    }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88017' || substr($sender->sir_sender_id,0,5) == '88013')) {
                        $sms_sender_op = 2; // GP
                    }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88019' || substr($sender->sir_sender_id,0,5) == '88014')){
                        $sms_sender_op = 3; // Banglalink
                    }elseif ($sms_masking_type == '1' && substr($sender->sir_sender_id,0,5) == '88015'){
                        $sms_sender_op = 4; // Teletalk
                    }
                     $message = "You Have Pending SMS Campaign for ". Auth::user()->company_name ." in your DB.\nAccept it ASAP.";
                    $message = rawurlencode($message);
                    $number = '01958666900';
                    $senderId = "8801844532630";
                    $apikey = "445156057064961560570649";
                    $client = new Client();
                    $url = "http://sms.iglweb.com/api/v1/send?api_key=".$apikey."&contacts=".$number."&senderid=".$senderId."&msg=".$message;
                    $res = $client->request('GET', $url);
                    $ret = $res->getBody();

                    $insertCampaign = SmsCampaignId::create([
                        'user_id' => Auth::id(),
                        'sender_id' => $request['sender_id'],
                        'sci_campaign_title' => $request->campaign_title ?? $campaign_id,
                        'sci_campaign_id' => $campaign_id,
                        'sci_total_submitted' => $total_sms_number,
                        'sci_total_cost' => $total_cost,
                        'sci_campaign_type' => $request['schedule'], /*1=instant, 2=Schedule */
                        'sci_deal_type' => '1', /* 1=SMS, 2=Campaign */
                        'sci_sms_type' => $sms_masking_type, /*1=NonMasking, 2=Masking*/
                        'sci_sender_operator' => $sms_sender_op, /*1=NonMasking, 2=Masking*/
                        'sci_dynamic_type' => '0',/*1=dynamic, 0=general*/
                        'sci_targeted_time' => $target_time,
                        'sci_campaign_status' => $campaign_accept_status,
                        'sci_browser' => $request->header('User-Agent'),
                        'sci_mac_address' => null,
                        'sci_ip_address' => $request->ip()
                    ]);

                    /*debit user balance*/
                    $user_position = Auth::user()->position;
                    $user_id = Auth::id();
                    $user_det = User::where('id', $user_id)->first();

                    while ($user_position >= 1) {
                        /*get total cost*/
                        $campaign_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $user_det->id);
                        \App\Helpers\BalanceHelper::addDebit(
                            $user_det->create_by,
                            $user_det->id,
                            $campaign_id,
                            $campaign_cost,
                            4,
                            1,
                            2,
                            $target_time
                        );

                        $user_det = User::where('id', $user_det->create_by)->first();
                        $user_position = $user_det->position;
                    }

                    /*add user credit history*/
                    AccUserCreditHistory::create([
                        'campaign_id' => $insertCampaign->id,
                        'user_id' => Auth::id(),
                        'uch_sms_count' => $total_sms_number,
                        'uch_sms_cost' => $total_cost,
                    ]);

                    $requestVal = $request->except('sms_file');
			    if ($isMasking === true) {
			    $masking = 'masking';
			} elseif ($isMasking === 'iptsp') {
			    $masking = 'iptsp';
			} else {
			    $masking = 'nonmasking';
			}
					

                    $insertJob = new InsertSms($masking,$requestVal,$validUniqueNumbers,$total_cost,$target_time,$sms_number,$smsType,Auth::id(),$insertCampaign->id);
                    dispatch($insertJob->onQueue('insertSms'));
                    /*$insertJob = (new InsertSms($masking,$requestVal,$validUniqueNumbers,$total_cost,$target_time,$sms_number,$smsType,Auth::id()))
                        ->delay(Carbon::now()->addSeconds(5));
                    dispatch($insertJob);*/
                    $ret = new stdClass();
                    $ret->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                    $ret->success = 'Message has been sent...';
                    die(json_encode($ret));
                } catch (\Exception $e) {
                    $res = new stdClass();
                    $res->error = 'Something was wrong to sent sms. please contact with admin!!! ...'.$e->getMessage();
                    $res->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                    die(json_encode($res));
                }
            }
        }
    }


    /*send group contact sms*/
    public function storeGroupContactSms(Request $request)
    {
        $sender = SenderIdRegister::where('id',$request->sender_id)->first();

        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'sender_id' => 'required',
            'group_name' => 'required',
            'message' => 'required',
            'schedule' => 'required',
        ]);

        $request->message = trim($request->message);

        if ($validateData->fails()) {
//            return redirect()->back()->withInput()->withErrors($validateData);
            $res = new stdClass();
            $res->errors = $validateData->errors()->all();
            die(json_encode($res));
        }
        if ($request->schedule == '2') {
            $validateData1 = Validator::make($request->all(), [
                'target_time' => 'required',
            ]);

            if ($validateData1->fails()) {
                /*return redirect()->back()->withInput()->withErrors($validateData1);*/
                $res = new stdClass();
                $res->errors = $validateData1->errors()->all();
                die(json_encode($res));
            }

            $target_time = date('Y-m-d H:i:s', strtotime($request->target_time));

        } else {
            $target_time = Carbon::now()->toDateTimeString();
        }

        /*check requested sender id is registered or not for this user*/
        $checkSenderId = SenderIdUser::where(['user_id' => Auth::id(), 'sender_id' => $request->sender_id])->first();
        if (!$checkSenderId) {
            $res = new stdClass();
            $res->error = 'Warning! can\'t find your sender id . please try again...';
            die(json_encode($res));

        } else {
            /*check requested phonebook group is available for this user or not*/
            $checkPhonebookCategory = PhonebookCategory::where(['user_id' => Auth::id(), 'id' => $request->group_name])->first();
            if (!$checkPhonebookCategory) {
                $res = new stdClass();
                $res->error = 'Warning! can\'t find your phonebook group. please try again...';
                die(json_encode($res));
            } else {

                $validNumbers = PhonebookContact::where('category_id', $request->group_name)->get();
                $validUniqueNumbers = array();
                foreach ($validNumbers as $validNumber) {
                    $validUniqueNumbers[] = $validNumber->phone_number;
                }

                if (count($validUniqueNumbers) < 1) {

                    $res = new stdClass();
                    $res->error = 'All number are invalid...';
                    die(json_encode($res));
                }

                /*sms count*/
                $full_text = preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $request->message);
                
                if (\SmsHelper::is_unicode($full_text)) {
                    $smsType = 'unicode'; //unicode
                    $sms_number = \SmsHelper::unicode_sms_count($full_text);

                } else {
                    $smsType = 'text'; //text
                    $sms_number = \SmsHelper::text_sms_count($full_text);
                }

                $isMasking = \SmsHelper::isMasking($request->sender_id);
                $total_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, Auth::id());

                if(($smsType=='unicode') && ($isMasking==true)){
                    $strLength = \SmsHelper::unicode_sms_count($full_text);
                    if($strLength>315){
                        $res = new stdClass();
                        $res->error = 'Warning! Masking-Unicode sms can\'t be more then 315 character...';
                        die(json_encode($res));
                    }
                }

                if (\App\Helpers\BalanceHelper::user_available_balance(Auth::id()) < $total_cost) {
                    $res = new stdClass();
                    $res->error = 'Warning! you haven\'t enough balance . please recharge first...';
                    die(json_encode($res));

                } elseif (\App\Helpers\BalanceHelper::check_parents_available_balance(Auth::id(), $sms_number, $validUniqueNumbers, $isMasking) == false) {
                    $res = new stdClass();
                    $res->error = 'Warning! your reseller don\'t have enough balance . told him to recharge first...';
                    die(json_encode($res));

                } elseif ($isMasking == true && mb_strlen($request->message) > 500) { 
                    $res = new stdClass();
                    $res->error = 'Message character limit exceeded. Maximum 500 characters allowed.';
                    die(json_encode($res));
                } else {
                    try {
                        $redisInfo = \Redis::info();

                        $total_sms_number = $sms_number*count($validUniqueNumbers);
                        $campaign_id = Auth::id() . time() . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9);
                         if ($isMasking === true) {
                                    $sms_masking_type = '2'; // Masking
                                } elseif ($isMasking === 'iptsp') {
                                    $sms_masking_type = '1'; // Still non-masking
                        
                                } else {
                                    $sms_masking_type = '1'; // Non-masking
                                }
                        $current_date = Carbon::now()->toDateTimeString();

                        $campaign_permission = UserDetail::where('user_id',Auth::id())->first();

                        if (count($validUniqueNumbers) >= 10 && $campaign_permission->campaign_permission == 1) {
                            $campaign_accept_status = 1;
                        }elseif ($smsType == 'unicode') {
                            $campaign_accept_status = 1;
                        }elseif (count($validUniqueNumbers) >= 10) {
                            $campaign_accept_status = $this->campaign_permission;
                        } else {
                            $campaign_accept_status = 1;
                        }

                        $sms_sender_op = null;
                    
                        if ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88018' || substr($sender->sir_sender_id,0,5) == '88016')) {
                            $sms_sender_op = 1; // Robi and airtel
                        }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88017' || substr($sender->sir_sender_id,0,5) == '88013')) {
                            $sms_sender_op = 2; // GP
                        }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88019' || substr($sender->sir_sender_id,0,5) == '88014')){
                            $sms_sender_op = 3; // Banglalink
                        }elseif ($sms_masking_type == '1' && substr($sender->sir_sender_id,0,5) == '88015'){
                            $sms_sender_op = 4; // Teletalk
                        }

                        $insertCampaign = SmsCampaignId::create([
                            'user_id' => Auth::id(),
                            'sender_id' => $request->sender_id,
                            'sci_campaign_title' => $request->campaign_title ?? $campaign_id,
                            'sci_campaign_id' => $campaign_id,
                            'sci_total_submitted' => $total_sms_number,
                            'sci_total_cost' => $total_cost,
                            'sci_campaign_type' => $request->schedule, /*1=instant, 2=Schedule */
                            'sci_deal_type' => '1', /* 1=SMS, 2=Campaign */
                            'sci_sms_type' => $sms_masking_type, /*1=NonMasking, 2=Masking*/
                            'sci_sender_operator' => $sms_sender_op, /*1=NonMasking, 2=Masking*/
                            'sci_dynamic_type' => '0',/*1=dynamic, 0=general*/
                            'sci_targeted_time' => $target_time,
                            'sci_campaign_status' => $campaign_accept_status,
                            'sci_browser' => $request->header('User-Agent'),
                            'sci_mac_address' => null,
                            'sci_ip_address' => $request->ip()
                        ]);

                        $message = "You Have Pending SMS Campaign for ". Auth::user()->company_name ." in your DB.\nAccept it ASAP.";
                        $message = rawurlencode($message);
                        $number = '01958666900';
                        $senderId = "8801844532630";
                        $apikey = "445156057064961560570649";
                        $client = new Client();
                        $url = "http://sms.iglweb.com/api/v1/send?api_key=".$apikey."&contacts=".$number."&senderid=".$senderId."&msg=".$message;
                        $res = $client->request('GET', $url);
                        $ret = $res->getBody();

                        /*debit user balance*/
                        $user_position = Auth::user()->position;
                        $user_id = Auth::id();
                        $user_det = User::where('id', $user_id)->first();

                        while ($user_position >= 1) {
                            /*get total cost*/
                            $campaign_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $user_det->id);
                            \App\Helpers\BalanceHelper::addDebit(
                                $user_det->create_by,
                                $user_det->id,
                                $campaign_id,
                                $campaign_cost,
                                4,
                                1,
                                2,
                                $target_time
                            );

                            $user_det = User::where('id', $user_det->create_by)->first();
                            $user_position = $user_det->position;
                        }

                        /*add user credit history*/
                        AccUserCreditHistory::create([
                            'campaign_id' => $insertCampaign->id,
                            'user_id' => Auth::id(),
                            'uch_sms_count' => $total_sms_number,
                            'uch_sms_cost' => $total_cost,
                        ]);

                        $requestVal = $request->except('sms_file');
                                    if ($isMasking === true) {
				    $masking = 'masking';
				} elseif ($isMasking === 'iptsp') {
				    $masking = 'iptsp';
				} else {
				    $masking = 'nonmasking';
				}
	

                        $insertJob = new InsertSms($masking,$requestVal,$validUniqueNumbers,$total_cost,$target_time,$sms_number,$smsType,Auth::id(),$insertCampaign->id);
                        dispatch($insertJob->onQueue('insertSms'));

                        $ret = new stdClass();
                        $ret->success = 'Message has been sent...';
                        $ret->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                        die(json_encode($ret));

                    } catch (\Exception $e) {

                        $res = new stdClass();
                        $res->error = 'Something was wrong to sent sms. please contact with admin!!! ...'.$e->getMessage();
                        $res->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                        die(json_encode($res));
                    }
                }
            }

        }
    }


    /*store dynamic sms*/
    public function storeDynamicSms(Request $request)
    {
        $sender = SenderIdRegister::where('id',$request->sender_id)->first();
//        dd($request);
        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'sender_id' => 'required',
            'sms_file' => 'required',
            'schedule' => 'required',
            'dynamic_number_column' => 'required',
            'message' => 'required',
        ]);

        if ($validateData->fails()) {
            $res = new stdClass();
            $res->errors = $validateData->errors()->all();
            die(json_encode($res));
        }


        if ($request->schedule == '2') {
            $validateData1 = Validator::make($request->all(), [
                'target_time' => 'required',
            ]);

            if ($validateData1->fails()) {
                $res = new stdClass();
                $res->errors = $validateData1->errors()->all();
                die(json_encode($res));
            }

            $target_time = date('Y-m-d H:i:s', strtotime($request->target_time));

        } else {
            $target_time = Carbon::now()->toDateTimeString();
        }

        /*check requested sender id is registered or not for this user*/
        $checkSenderId = SenderIdUser::where(['user_id' => Auth::id(), 'sender_id' => $request->sender_id])->first();
        if (!$checkSenderId) {

            $res = new stdClass();
            $res->error = 'Warning! can\'t find your sender id . please try again...';
            die(json_encode($res));
        } else {
            $file = Input::file('sms_file');
            $filename = $request->file('sms_file')->getClientOriginalName();

            $fileType = \FileRead::getFileType($filename);
            $allContacts = array();
            $allMessages = array();
            try {

                /*$res->error = $request->sender_id;
                die(json_encode($res));*/
                if ($fileType == "Excel") {
                    $fileContents = Importer::make('Excel')->load($file)->getCollection();

                    $columns=array();
                    $message = $request->message;
                    preg_match_all("/\\[\#(.*?)\\#\]/", $message, $matches);
                    foreach ($matches[1] as $key => $value) {
                        $columns[] = $value;
                    }

                    foreach ($columns as $column) {
                        foreach ($fileContents as $fileContent) {
                            $i=0;
                            foreach ($fileContent as $cc){
                                if($column==$cc){
                                    $columnNumber[$column] = $i;
                                }
                                $i++;
                            }
                            break;
                        }

                    }


                    foreach ($fileContents as $fileContent) {
                        $allContacts[] = $fileContent[$request->dynamic_number_column-1];
                        $message = $request->message;
                        $i=0;
                        if(isset($columns) && (count($columns)>0)) {
                            foreach ($columns as $column) {
                                $search = '[#'.$column.'#]';
                                $message = str_replace($search, $fileContent[$columnNumber[$column]], $message);
                            }
                        }
                        $allMessages[] = trim($message);
                    }


                } else {
                    $res = new stdClass();
                    $res->error = 'Invalid file...1';
                    die(json_encode($res));
                }
            } catch (\Exception $e) {
                $res = new stdClass();
                $res->error = 'Invalid file...2'.$e->getMessage();
                die(json_encode($res));
            }

            $validNumbers = array();
            $validMessages = array();
            $getValidMessageSerial = 0;
            foreach ($allContacts as $contact) {

                $number = \PhoneNumber::addNumberPrefix($contact);
                if (\PhoneNumber::isValid($number)) {
                    $validNumbers[] = $number;
                    $validMessages[] = $allMessages[$getValidMessageSerial];
                }
                $getValidMessageSerial++;
            }

            if (count($validNumbers) < 1) {

                $res = new stdClass();
                $res->error = 'All number are invalid...';
                die(json_encode($res));
            }

            $isMasking = \SmsHelper::isMasking($request->sender_id);
            $total_cost = 0;
            $total_sms_number = 0;
            for ($i = 0; $i < count($validNumbers); $i++) {
                $full_text = preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $validMessages[$i]);
                if (\SmsHelper::is_unicode($validMessages[$i])) {
                    $smsType = 'unicode'; //unicode
                    $sms_number = \SmsHelper::unicode_sms_count($full_text);

                } else {
                    $smsType = 'text'; //text
                    $sms_number = \SmsHelper::text_sms_count($full_text);
                }
                $smsCost = \App\Helpers\BalanceHelper::singleSmsCost($sms_number, $validNumbers[$i], $isMasking, Auth::id());
                $total_cost = $total_cost + $smsCost;
                $total_sms_number = $total_sms_number + $sms_number;
                /*echo $i.". ".$validNumbers[$i].". ".$validMessages[$i].". ".$smsCost."<br>";*/
            }

            if (\App\Helpers\BalanceHelper::user_available_balance(Auth::id()) < $total_cost) {

                $res = new stdClass();
                $res->error = 'Warning! you haven\'t enough balance . please recharge first...';
                die(json_encode($res));

            } elseif (\App\Helpers\BalanceHelper::check_dynamic_parents_available_balance(Auth::id(), $validNumbers, $validMessages, $isMasking) == false) {

                $res = new stdClass();
                $res->error = 'Warning! your reseller don\'t have enough balance . told him to recharge first...';
                die(json_encode($res));

            } elseif ($isMasking == true && mb_strlen($request->message) > 500) { 
                $res = new stdClass();
                $res->error = 'Message character limit exceeded. Maximum 500 characters allowed.';
                die(json_encode($res));
            } else {
                try {
                    $campaign_id = Auth::id() . time() . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9);
                    if ($isMasking === true) {
                                    $sms_masking_type = '2'; // Masking
                                } elseif ($isMasking === 'iptsp') {
                                    $sms_masking_type = '1'; // Still non-masking
                        
                                } else {
                                    $sms_masking_type = '1'; // Non-masking
                                }

                    $current_date = Carbon::now()->toDateTimeString();

                    $sms_sender_op = null;
                    
                    if ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88018' || substr($sender->sir_sender_id,0,5) == '88016')) {
                        $sms_sender_op = 1; // Robi and airtel
                    }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88017' || substr($sender->sir_sender_id,0,5) == '88013')) {
                        $sms_sender_op = 2; // GP
                    }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88019' || substr($sender->sir_sender_id,0,5) == '88014')){
                        $sms_sender_op = 3; // Banglalink
                    }elseif ($sms_masking_type == '1' && substr($sender->sir_sender_id,0,5) == '88015'){
                        $sms_sender_op = 4; // Teletalk
                    }

                    $insertCampaign = SmsCampaignId::create([
                        'user_id' => Auth::id(),
                        'sender_id' => $request->sender_id,
                        'sci_campaign_title' => $request->campaign_title ?? $campaign_id,
                        'sci_campaign_id' => $campaign_id,
                        'sci_total_submitted' => $total_sms_number,
                        'sci_total_cost' => $total_cost,
                        'sci_campaign_type' => $request->schedule, /*1=instant, 2=Schedule */
                        'sci_deal_type' => '1', /* 1=SMS, 2=Campaign */
                        'sci_sms_type' => $sms_masking_type, /*1=NonMasking, 2=Masking*/
                        'sci_sender_operator' => $sms_sender_op, /*1=NonMasking, 2=Masking*/
                        'sci_dynamic_type' => '1',/*1=dynamic, 0=general*/
                        'sci_targeted_time' => $target_time,
                        'sci_browser' => $request->header('User-Agent'),
                        'sci_mac_address' => null,
                        'sci_ip_address' => $request->ip()
                    ]);


                    $insertCount = 0;
                    $dataForInsert = array();
                    $serial = 0;
                    for ($j = 0; $j < count($validNumbers); $j++) {
                        $operator = \PhoneNumber::checkOperator($validNumbers[$j]);
                        $full_text = preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $validMessages[$j]);
                        if (\SmsHelper::is_unicode($full_text)) {
                            $smsType = 'unicode'; //unicode
                            $sms_number = \SmsHelper::unicode_sms_count($full_text);

                        } else {
                            $smsType = 'text'; //text
                            $sms_number = \SmsHelper::text_sms_count($full_text);
                        }

                        $dataForInsert[] = array(
                            'user_id' => Auth::id(),
                            'sender_id' => $request->sender_id,
                            'campaign_id' => $insertCampaign->id,
                            'scp_cell_no' => $validNumbers[$j],
                            'scp_message' => $validMessages[$j],
                            'scp_sms_cost' => \App\Helpers\BalanceHelper::singleSmsCost($sms_number, $validNumbers[$j], $isMasking, Auth::id()),
                            'operator_id' => $operator['id'],
                            'scp_campaign_type' => $request->schedule, //*1=instant, 2=Schedule *
                            'scp_deal_type' => '1', //* 1=SMS, 2=Campaign *
                            'scp_sms_type' => $sms_masking_type, //*1=NonMasking, 2=Masking*
                            'scp_sms_id' => '0',
                            'scp_tried' => '0', //*Try For Send *
                            'scp_picked' => '0', //*0=not try, 1= try *
                            'scp_sms_text_type' => $smsType, //*SMS type=text/unicode*
                            'scp_target_time' => $target_time,
                            'scp_status' => '1',
                            'created_at' => $current_date,
                            'updated_at' => $current_date,
                        );
                        if ($insertCount < 20) {
                            $insertCount++;
                        } else {
                            SmsCamPending::insert($dataForInsert);
                            $dataForInsert = array();
                            $insertCount = 0;
                        }
                    }
                    SmsCamPending::insert($dataForInsert);


                    /*debit user balance*/
                    $user_position = Auth::user()->position;
                    $user_id = Auth::id();
                    $user_det = User::where('id', $user_id)->first();

                    while ($user_position >= 1) {
                        /*get total cost*/
                        /*$campaign_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $user_det->id);*/
                        $campaign_cost = 0;
                        for ($i = 0; $i < count($validNumbers); $i++) {

                            if (\SmsHelper::is_unicode($validMessages[$i])) {
                                $smsType = 'unicode'; //unicode
                                $sms_number = \SmsHelper::unicode_sms_count($validMessages[$i]);

                            } else {
                                $smsType = 'text'; //text
                                $sms_number = \SmsHelper::text_sms_count($validMessages[$i]);
                            }
                            $smsCost = \App\Helpers\BalanceHelper::singleSmsCost($sms_number, $validNumbers[$i], $isMasking, $user_det->id);
                            $campaign_cost = $campaign_cost + $smsCost;
                            /*echo $i.". ".$validNumbers[$i].". ".$validMessages[$i].". ".$smsCost."<br>";*/
                        }


                        \App\Helpers\BalanceHelper::addDebit(
                            $user_det->create_by,
                            $user_det->id,
                            $campaign_id,
                            $campaign_cost,
                            4,
                            1,
                            2,
                            $target_time
                        );

                        $user_det = User::where('id', $user_det->create_by)->first();
                        $user_position = $user_det->position;
                    }

                    /*add user credit history*/
                    AccUserCreditHistory::create([
                        'campaign_id' => $insertCampaign->id,
                        'user_id' => Auth::id(),
                        'uch_sms_count' => $total_sms_number,
                        'uch_sms_cost' => $total_cost,
                    ]);

                    $ret = new stdClass();
                    $ret->success = 'Message has been sent...';
                    $ret->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                    die(json_encode($ret));


                } catch (\Exception $e) {
                    $res = new stdClass();
                    $res->error = 'Something was wrong to sent sms. please contact with admin!!! ...';
                    $res->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                    die(json_encode($res));
                }
            }

        }
    }


    /*show send campaign sms form*/
    public function campaignCreate()
    {
        $phonebookCampaignCategories = PhonebookCampaignCategory::where('status', '1')->get();
        return view('user.messaging.campaign_sms', compact('phonebookCampaignCategories'));
    }


    /*store campaign sms*/
    public function storeCampaignSms(Request $request)
    {
        $sender = SenderIdRegister::where('id',$request->sender_id)->first();

        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'sender_id' => 'required',
            'group_name' => 'required',
            'message' => 'required',
            'schedule' => 'required',
        ]);

        $request->message = trim($request->message);

        if ($validateData->fails()) {
            $res = new stdClass();
            $res->errors = $validateData->errors()->all();
            die(json_encode($res));
        }
        if ($request->schedule == '2') {
            $validateData1 = Validator::make($request->all(), [
                'target_time' => 'required',
            ]);

            if ($validateData1->fails()) {
                $res = new stdClass();
                $res->errors = $validateData1->errors()->all();
                die(json_encode($res));
            }

            $target_time = date('Y-m-d H:i:s', strtotime($request->target_time));

        } else {
            $target_time = Carbon::now()->toDateTimeString();
        }

        /*check requested sender id is registered or not for this user*/
        $checkSenderId = SenderIdUser::where(['user_id' => Auth::id(), 'sender_id' => $request->sender_id])->first();
        if (!$checkSenderId) {

            $res = new stdClass();
            $res->error = 'Warning! can\'t find your sender id . please try again...';
            die(json_encode($res));

        } else {
            /*check requested phonebook group is available for this user or not*/
            if(($request->sl_from_number == null) && ($request->sl_to_number ==null)){
                $validNumbers = PhonebookCampaignContact::where('category_id', $request->group_name)->get();
            }elseif(($request->sl_from_number != null) && ($request->sl_to_number ==null)){
                $skip = $request->sl_from_number-1;
                $validNumbers = PhonebookCampaignContact::where('category_id', $request->group_name)->skip($skip)->get();
            }elseif(($request->sl_from_number == null) && ($request->sl_to_number !=null)){
                $validNumbers = PhonebookCampaignContact::where('category_id', $request->group_name)->take($request->sl_to_number)->get();
            }else{
                $skip = $request->sl_from_number-1;
                $take = $request->sl_to_number-$skip;
                $validNumbers = PhonebookCampaignContact::where('category_id', $request->group_name)->skip($skip)->take($take)->get();
            }

            $validUniqueNumbers = array();
            foreach ($validNumbers as $validNumber) {
                $validUniqueNumbers[] = $validNumber->phone_number;
            }

            if (count($validUniqueNumbers) < 1) {
                $res = new stdClass();
                $res->error = 'All number are invalid...';
                die(json_encode($res));
            }

            /*count how many numbers in an operator*/
            /*$countOperator = \PhoneNumber::countOperator($validUniqueNumbers);*/

            /*sms count*/
            $full_text = preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $request->message);
            if (\SmsHelper::is_unicode($request->message)) {
                $smsType = 'unicode'; //unicode
                $sms_number = \SmsHelper::unicode_sms_count($full_text);

            } else {
                $smsType = 'text'; //text
                $sms_number = \SmsHelper::text_sms_count($full_text);
            }

            $isMasking = \SmsHelper::isMasking($request->sender_id);
            $total_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, Auth::id());

            if (\App\Helpers\BalanceHelper::user_available_balance(Auth::id()) < $total_cost) {
                $res = new stdClass();
                $res->error = 'Warning! you haven\'t enough balance . please recharge first...';
                die(json_encode($res));

            } elseif (\App\Helpers\BalanceHelper::check_parents_available_balance(Auth::id(), $sms_number, $validUniqueNumbers, $isMasking) == false) {
                $res = new stdClass();
                $res->error = 'Warning! your reseller don\'t have enough balance . told him to recharge first...';
                die(json_encode($res));

            } else {
                try {

                    $redisInfo = \Redis::info();

                    $total_sms_number = $sms_number*count($validUniqueNumbers);
                    $campaign_id = Auth::id() . time() . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9);
                     if ($isMasking === true) {
                                    $sms_masking_type = '2'; // Masking
                                } elseif ($isMasking === 'iptsp') {
                                    $sms_masking_type = '1'; // Still non-masking
                        
                                } else {
                                    $sms_masking_type = '1'; // Non-masking
                                }
                    $current_date = Carbon::now()->toDateTimeString();

                    if (count($validUniqueNumbers) >= 10) {
                        $campaign_accept_status = $this->campaign_permission;
                    }elseif ($smsType == 'unicode') {
                        $campaign_accept_status = 1;
                    }else {
                        $campaign_accept_status = 1;
                    }

                    $sms_sender_op = null;
                    
                    if ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88018' || substr($sender->sir_sender_id,0,5) == '88016')) {
                        $sms_sender_op = 1; // Robi and airtel
                    }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88017' || substr($sender->sir_sender_id,0,5) == '88013')) {
                        $sms_sender_op = 2; // GP
                    }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88019' || substr($sender->sir_sender_id,0,5) == '88014')){
                        $sms_sender_op = 3; // Banglalink
                    }elseif ($sms_masking_type == '1' && substr($sender->sir_sender_id,0,5) == '88015'){
                        $sms_sender_op = 4; // Teletalk
                    }

                    $insertCampaign = SmsCampaignId::create([
                        'user_id' => Auth::id(),
                        'sender_id' => $request->sender_id,
                        'sci_campaign_title' => $request->campaign_title ?? $campaign_id,
                        'sci_campaign_id' => $campaign_id,
                        'sci_total_submitted' => $total_sms_number,
                        'sci_total_cost' => $total_cost,
                        'sci_campaign_type' => $request->schedule, /*1=instant, 2=Schedule */
                        'sci_deal_type' => '2', /* 1=SMS, 2=Campaign */
                        'sci_sms_type' => $sms_masking_type, /*1=NonMasking, 2=Masking*/
                        'sci_sender_operator' => $sms_sender_op, /*1=NonMasking, 2=Masking*/
                        'sci_campaign_status' => $campaign_accept_status, /*1=NonMasking, 2=Masking*/
                        'sci_dynamic_type' => '0',/*1=dynamic, 0=general*/
                        'sci_targeted_time' => $target_time,
                        'sci_browser' => $request->header('User-Agent'),
                        'sci_mac_address' => null,
                        'sci_ip_address' => $request->ip()
                    ]);
                    
                    $message = "You Have Pending SMS Campaign for ". Auth::user()->company_name ." in your DB.\nAccept it ASAP.";
                    $message = rawurlencode($message);
                    $number = '01958666900';
                    $senderId = "8801844532630";
                    $apikey = "445156057064961560570649";
                    $client = new Client();
                    $url = "http://sms.iglweb.com/api/v1/send?api_key=".$apikey."&contacts=".$number."&senderid=".$senderId."&msg=".$message;
                    $res = $client->request('GET', $url);
                    $ret = $res->getBody();

                    /*debit user balance*/
                    $user_position = Auth::user()->position;
                    $user_id = Auth::id();
                    $user_det = User::where('id', $user_id)->first();

                    while ($user_position >= 1) {
                        /*get total cost*/
                        $campaign_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $user_det->id);
                        \App\Helpers\BalanceHelper::addDebit(
                            $user_det->create_by,
                            $user_det->id,
                            $campaign_id,
                            $campaign_cost,
                            4,
                            1,
                            2,
                            $target_time
                        );

                        $user_det = User::where('id', $user_det->create_by)->first();
                        $user_position = $user_det->position;
                    }

                    /*add user credit history*/
                    AccUserCreditHistory::create([
                        'campaign_id' => $insertCampaign->id,
                        'user_id' => Auth::id(),
                        'uch_sms_count' => $total_sms_number,
                        'uch_sms_cost' => $total_cost,
                    ]);

                    $requestVal = $request->except('sms_file');
                   if ($isMasking === true) {
			    $masking = 'masking';
			} elseif ($isMasking === 'iptsp') {
			    $masking = 'iptsp';
			} else {
			    $masking = 'nonmasking';
			}
			

                    $insertJob = new InsertSms($masking,$requestVal,$validUniqueNumbers,$total_cost,$target_time,$sms_number,$smsType,Auth::id(),$insertCampaign->id);
                    dispatch($insertJob->onQueue('insertSms'));


                    $ret = new stdClass();
                    $ret->success = 'Message has been sent...';
                    $ret->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                    die(json_encode($ret));

                } catch (\Exception $e) {

                    $res = new stdClass();
                    $res->error = $e->getMessage().'Something was wrong to sent sms. please contact with admin!!! ...';
                    $res->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                    die(json_encode($res));
                }
            }

        }
    }


    /*check dynamic file*/
    public function checkDynamicFile(Request $request)
    {

        $file = $request->file('sms_file');
        if (empty($file)) {
            $res = new stdClass();
            $res->error = 'Please select a file...!';
            die(json_encode($res));
        } else {
            $filename = $request->file('sms_file')->getClientOriginalName();

            $fileType = \FileRead::getFileType($filename);
            $allContacts = array();
            try {
                if ($fileType == "Excel") {
                    $fileContents = Importer::make('Excel')->load($file)->getCollection();
                    foreach ($fileContents as $fileContent) {
                        $allContacts[] = $fileContent[0];
                    }
                } else {
                    $res = new stdClass();
                    $res->error = 'Only excel file are allowed for dynamic sms...!';
                    die(json_encode($res));
                }
            } catch (\Exception $e) {
                $res = new stdClass();
                $res->error = 'Something went wrong to upload file. contact with admin';
                die(json_encode($res));
            }

            /*$validNumbers = array();
            foreach ($allContacts as $contact) {
                $number = \PhoneNumber::addNumberPrefix($contact);
                if (\PhoneNumber::isValid($number)) {
                    $validNumbers[] = $number;
                }
            }*/

            foreach ($fileContents as $fileContent) {
                foreach ($fileContent as $content) {
                    $columns[] = $content;
                }
                break;
            }

            $res = new stdClass();
            $res->columns = $columns;
            die(json_encode($res));

        }
    }



    /*check upload file*/
    public function checkUploadFile(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'sender_id' => 'required',
            'sms_file' => 'required',
            'message' => 'required',
            'schedule' => 'required',
        ]);
        // return urlencode($request->message);
        // return strlen(urldecode($request->message));
        // return $request->message;
        $request->message = trim($request->message);

        if ($validateData->fails()) {
            $res = new stdClass();
            $res->errors = $validateData->errors()->all();
            die(json_encode($res));
        }

        $file = $request->file('sms_file');
        if (empty($file)) {
            $res = new stdClass();
            $res->error = 'Please select a file...!';
            die(json_encode($res));
        } else {

            $file = Input::file('sms_file');
            $filename = $request->file('sms_file')->getClientOriginalName();

            $fileType = \FileRead::getFileType($filename);
            $allContacts = array();
            if ($fileType == "Excel") {
                $fileContents = Importer::make('Excel')->load($file)->getCollection();
                foreach ($fileContents as $fileContent) {
                    $allContacts[] = $fileContent[0];
                }
            } elseif ($fileType == "Csv") {
                $fileContents = Importer::make('Csv')->load($file)->getCollection();
                foreach ($fileContents as $fileContent) {
                    $allContacts[] = $fileContent[0];
                }
            } elseif ($fileType == "Text") {
                $fileContent = File::get($file);
                $allContacts = explode(PHP_EOL, $fileContent);
            } else {
                $res = new stdClass();
                $res->error = 'Invalid file...';
                die(json_encode($res));
            }
            $total_submitted_number = count($allContacts);

            $validNumbers = array();
            foreach ($allContacts as $contact) {
                $number = \PhoneNumber::addNumberPrefix($contact);
                if (\PhoneNumber::isValid($number)) {
                    $validNumbers[] = $number;
                }
            }


            $total_valid_number = count($validNumbers);

            /*get unique number*/
            $validUniqueNumbers = array_unique($validNumbers);

            if (count($validUniqueNumbers) < 1) {
                $res = new stdClass();
                $res->error = 'All number are invalid...';
                die(json_encode($res));
            }

            $message = preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $request->message);
            /*sms count*/
            if (\SmsHelper::is_unicode($request->message)) {
                $smsType = 'unicode'; //unicode
                $sms_number = \SmsHelper::unicode_sms_count($message);

            } else {
                $smsType = 'text'; //text
                $sms_number = \SmsHelper::text_sms_count($message);
            }

            $isMasking = \SmsHelper::isMasking($request->sender_id);

            if(($smsType=='unicode') && ($isMasking==true)){
                $strLength = \SmsHelper::unicode_sms_count($message);
                if($strLength>315){
                    $res = new stdClass();
                    $res->error = 'Warning! Masking-Unicode sms can\'t be more then 315 character...';
                    die(json_encode($res));
                }
            }

            $total_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, Auth::id());


            $res = new stdClass();
            $res->success = 'Go';
            $res->total_submitted_number = $total_submitted_number;
            $res->total_valid_number = $total_valid_number;
            $res->total_duplicate_number = $total_valid_number - count($validUniqueNumbers);
            $res->total_valid_unique_number = count($validUniqueNumbers);
            $res->sms_count = $sms_number;
            $res->total_cost = number_format($total_cost, 2);
            $res->sms_content = $message;

            die(json_encode($res));
        }
    }


    /*send group contact sms*/
    public function storeEmployeeGroupContactSms(Request $request)
    {
        $sender = SenderIdRegister::where('id',$request->sender_id)->first();
        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'sender_id' => 'required',
            'group_name' => 'required',
            'message' => 'required',
            'schedule' => 'required',
        ]);

        $request->message = trim($request->message);

        if ($validateData->fails()) {
//            return redirect()->back()->withInput()->withErrors($validateData);
            $res = new stdClass();
            $res->errors = $validateData->errors()->all();
            die(json_encode($res));
        }
        if ($request->schedule == '2') {
            $validateData1 = Validator::make($request->all(), [
                'target_time' => 'required',
            ]);

            if ($validateData1->fails()) {
                /*return redirect()->back()->withInput()->withErrors($validateData1);*/
                $res = new stdClass();
                $res->errors = $validateData1->errors()->all();
                die(json_encode($res));
            }

            $target_time = date('Y-m-d H:i:s', strtotime($request->target_time));

        } else {
            $target_time = Carbon::now()->toDateTimeString();
        }

        /*check requested sender id is registered or not for this user*/
        $checkSenderId = SenderIdUser::where(['user_id' => Auth::id(), 'sender_id' => $request->sender_id])->first();
        if (!$checkSenderId) {
            /*session()->flash('type', 'danger');
            session()->flash('message', 'Warning! can\'t find your sender id. please try again...');
            return redirect()->back()->withInput();*/
            $res = new stdClass();
            $res->error = 'Warning! can\'t find your sender id . please try again...';
            die(json_encode($res));

        } else {
            /*check requested phonebook group is available for this user or not*/
            $checkPhonebookCategory = LoadFlexibook::where(['user_id' => Auth::id(), 'id' => $request->group_name])->first();
            if (!$checkPhonebookCategory) {
                $res = new stdClass();
                $res->error = 'Warning! can\'t find your flexibook. please try again...';
                die(json_encode($res));
            } else {

                $validNumbers = LoadFlexibooksData::where('load_flexibooks_id', $request->group_name)->get();
                $validUniqueNumbers = array();
                foreach ($validNumbers as $validNumber) {
                    $validUniqueNumbers[] = $validNumber->number;
                }

                if (count($validUniqueNumbers) < 1) {

                    $res = new stdClass();
                    $res->error = 'All number are invalid...';
                    die(json_encode($res));
                }

                /*count how many numbers in an operator*/
                /*$countOperator = \PhoneNumber::countOperator($validUniqueNumbers);*/

                /*sms count*/
                $message = preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $request->message);
                if (\SmsHelper::is_unicode($request->message)) {
                    $smsType = 'unicode'; //unicode
                    $sms_number = \SmsHelper::unicode_sms_count($message);

                } else {
                    $smsType = 'text'; //text
                    $sms_number = \SmsHelper::text_sms_count($message);
                }

                $isMasking = \SmsHelper::isMasking($request->sender_id);
                $total_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, Auth::id());

                if(($smsType=='unicode') && ($isMasking==true)){
                    $strLength = \SmsHelper::unicode_sms_count($message);
                    if($strLength>315){
                        $res = new stdClass();
                        $res->error = 'Warning! Masking-Unicode sms can\'t be more then 315 character...';
                        die(json_encode($res));
                    }
                }

                if (\App\Helpers\BalanceHelper::user_available_balance(Auth::id()) < $total_cost) {
                    $res = new stdClass();
                    $res->error = 'Warning! you haven\'t enough balance . please recharge first...';
                    die(json_encode($res));

                } elseif (\App\Helpers\BalanceHelper::check_parents_available_balance(Auth::id(), $sms_number, $validUniqueNumbers, $isMasking) == false) {
                    $res = new stdClass();
                    $res->error = 'Warning! your reseller don\'t have enough balance . told him to recharge first...';
                    die(json_encode($res));

                } else {
                    try {
                        $redisInfo = \Redis::info();

                        $total_sms_number = $sms_number*count($validUniqueNumbers);
                        $campaign_id = Auth::id() . time() . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9);
                         if ($isMasking === true) {
                                    $sms_masking_type = '2'; // Masking
                                } elseif ($isMasking === 'iptsp') {
                                    $sms_masking_type = '1'; // Still non-masking
                        
                                } else {
                                    $sms_masking_type = '1'; // Non-masking
                                }

                        $current_date = Carbon::now()->toDateTimeString();


                        if (count($validUniqueNumbers) >= 10) {
                            $campaign_accept_status = $this->campaign_permission;
                        }elseif ($smsType == 'unicode') {
                            $campaign_accept_status = 1;
                        }else {
                            $campaign_accept_status = 1;
                        }

                        $sms_sender_op = null;
                    
                        if ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88018' || substr($sender->sir_sender_id,0,5) == '88016')) {
                            $sms_sender_op = 1; // Robi and airtel
                        }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88017' || substr($sender->sir_sender_id,0,5) == '88013')) {
                            $sms_sender_op = 2; // GP
                        }elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id,0,5) == '88019' || substr($sender->sir_sender_id,0,5) == '88014')){
                            $sms_sender_op = 3; // Banglalink
                        }elseif ($sms_masking_type == '1' && substr($sender->sir_sender_id,0,5) == '88015'){
                            $sms_sender_op = 4; // Teletalk
                        }

                        $insertCampaign = SmsCampaignId::create([
                            'user_id' => Auth::id(),
                            'sender_id' => $request->sender_id,
                            'sci_campaign_title' => $request->campaign_title ?? $campaign_id,
                            'sci_campaign_id' => $campaign_id,
                            'sci_total_submitted' => $total_sms_number,
                            'sci_total_cost' => $total_cost,
                            'sci_campaign_type' => $request->schedule, /*1=instant, 2=Schedule */
                            'sci_deal_type' => '1', /* 1=SMS, 2=Campaign */
                            'sci_sms_type' => $sms_masking_type, /*1=NonMasking, 2=Masking*/
                            'sci_ssender_operator' => $sms_sender_op, /*1=NonMasking, 2=Masking*/
                            'sci_dynamic_type' => '0',/*1=dynamic, 0=general*/
                            'sci_targeted_time' => $target_time,
                            'sci_campaign_status' => $campaign_accept_status,
                            'sci_browser' => $request->header('User-Agent'),
                            'sci_mac_address' => null,
                            'sci_ip_address' => $request->ip()
                        ]);

                        /*debit user balance*/
                        $user_position = Auth::user()->position;
                        $user_id = Auth::id();
                        $user_det = User::where('id', $user_id)->first();

                        while ($user_position >= 1) {
                            /*get total cost*/
                            $campaign_cost = \App\Helpers\BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $user_det->id);
                            \App\Helpers\BalanceHelper::addDebit(
                                $user_det->create_by,
                                $user_det->id,
                                $campaign_id,
                                $campaign_cost,
                                4,
                                1,
                                2,
                                $target_time
                            );

                            $user_det = User::where('id', $user_det->create_by)->first();
                            $user_position = $user_det->position;
                        }

                        /*add user credit history*/
                        AccUserCreditHistory::create([
                            'campaign_id' => $insertCampaign->id,
                            'user_id' => Auth::id(),
                            'uch_sms_count' => $total_sms_number,
                            'uch_sms_cost' => $total_cost,
                        ]);

                        $requestVal = $request->except('sms_file');
                        if ($isMasking === true) {
			    $masking = 'masking';
			} elseif ($isMasking === 'iptsp') {
			    $masking = 'iptsp';
			} else {
			    $masking = 'nonmasking';
			}
	
                        $insertJob = new InsertSms($masking,$requestVal,$validUniqueNumbers,$total_cost,$target_time,$sms_number,$smsType,Auth::id(),$insertCampaign->id);
                        dispatch($insertJob->onQueue('insertSms'));

                        $ret = new stdClass();
                        $ret->success = 'Message has been sent...';
                        $ret->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                        die(json_encode($ret));

                    } catch (\Exception $e) {

                        $res = new stdClass();
                        $res->error = 'Something was wrong to sent sms. please contact with admin!!! ...';
                        $res->current_balance = number_format(\App\Helpers\BalanceHelper::user_available_balance(Auth::id()), 2);
                        die(json_encode($res));
                    }
                }
            }

        }
    }


    public function checkApi()
    {
//        $user_name = 'IGLWebLTD';
//        $password = 'qazXSW11!!!!';
//        $sms_text = 'আমি বাংলায় গান
//গাই আমি বাংলায় গান
//গাই আমি বাংলায় গান গাই
//আমি';
//        dd(mb_strlen(urldecode(($sms_text))));
//        $number[] = '8801854718880';
//        $number[] = '01719134842';
//        $sender = 'RamBD';
//        $xml_response = \SmsHelper::send_masking_gp_sms($user_name, $password, $sms_text, $number, $sender);
//        dd($xml_response);
//        if ($xml_response == '0150') {
//            echo "Something was missing";
//        } else {
            /*foreach ($xml_response as $smsReport) {
                echo "message id: " . $smsReport->MessageId . "<br>";
                echo "status: " . $smsReport->Status . "<br>";
                echo "SMSCount: " . $smsReport->SMSCount . "<br>";
                echo "destination: " . $smsReport->destination . "<br>";
                dd($smsReport);
            }*/
//            dd($xml_response);
//            echo $xml_response;
//            die();
//        }
    }
}