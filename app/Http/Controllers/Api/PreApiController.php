<?php

namespace App\Http\Controllers\Api;

use App\Model\AccSmsBalance;
use App\Model\AccUserCreditHistory;
use App\Model\SenderIdRegister;
use App\Model\SenderIdUser;
use App\Model\SmsCampaignId;
use App\Model\SmsCamPending;
use App\Model\User;
use App\Model\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PreApiController extends Controller
{
    //send sms and show balance
    public function sendSms(Request $request)
    {
//        dd($request);
        /*check balance*/
        if(isset($request->balance_for) && ($request->balance_for != null)){

            $userDetail = UserDetail::where('api_key', $request->balance_for)->first();
            if(!$userDetail){
                return response()->json(['code'=>'445040', 'message'=>'Invalid api key']);
            }

            $user = User::where('id', $userDetail->user_id)->first();
            if($user->status==2){
                return response()->json(['code'=>'445050', 'message'=>'Your account was suspended']);
            }elseif($user->status==3){
                return response()->json(['code'=>'445060', 'message'=>'Your account was expired']);
            }

            try{
                $userAvailableBalance = \BalanceHelper::user_available_balance($user->id);
                return response()->json(['code'=>'0', 'balance'=>number_format($userAvailableBalance, 2)." tk"]);
            }catch (Exception $e){
                return response()->json(['code'=>'445160', 'message'=>'Something was wrong to check balance. please contact with admin!!! ..']);
            }
        }
        else {
            /*validate data*/
            if (!$request->api_key) {
                return response()->json(['code' => '445010', 'message' => 'Missing api key']);
            } elseif (!$request->contacts) {
                return response()->json(['code' => '445020', 'message' => 'Missing contact numbers']);
            } elseif (!$request->senderid) {
                return response()->json(['code' => '445030', 'message' => 'Missing sender id']);
            } elseif (!$request->msg) {
                return response()->json(['code' => '445170', 'message' => 'Missing text sms']);
            }


            /*check exist data*/
            /*check api*/
            $userDetail = UserDetail::where('api_key', $request->api_key)->first();
            if (!$userDetail) {
                return response()->json(['code' => '445040', 'message' => 'Invalid api key']);
            }
            $user = User::where('id', $userDetail->user_id)->first();
            if ($user->status == 2) {
                return response()->json(['code' => '445050', 'message' => 'Your account was suspended']);
            } elseif ($user->status == 3) {
                return response()->json(['code' => '445060', 'message' => 'Your account was expired']);
            } elseif ($user->role != 5) {
                if (!$request->for_registration) {
                    return response()->json(['code' => '445070', 'message' => 'Only a user can send sms']);
                } elseif (($request->for_registration != 'resellerToUser') && ($request->for_registration != 'adminToReseller')) {
                    return response()->json(['code' => '445071', 'message' => 'Only a user can send sms']);
                }
            }
            /*check sender id*/
            $sender = SenderIdRegister::where('sir_sender_id', $request->senderid)->first();
            if (!$sender) {
                return response()->json(['code' => '445080', 'message' => 'Invalid sender id']);
            }
            $checkSenderUser = SenderIdUser::where(['user_id' => $user->id, 'sender_id' => $sender->id])->first();
            if (!$checkSenderUser) {
                return response()->json(['code' => '445090', 'message' => 'You have no access to this sender id']);
            }

            /*check and get numbers*/
            $allContacts = explode(',', $request->contacts);
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
                return response()->json(['code' => '445110', 'message' => 'All numbers are invalid']);
            }

            /*sms count*/
            if (\SmsHelper::is_unicode($request->msg)) {
                $smsType = 'unicode'; //unicode
                $sms_number = \SmsHelper::unicode_sms_count($request->msg);

            } else {
                $smsType = 'text'; //text
                $sms_number = \SmsHelper::text_sms_count($request->msg);
            }

            $isMasking = \SmsHelper::isMasking($sender->id);
            $total_cost = \BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $user->id);

            if (\BalanceHelper::user_available_balance($user->id) < $total_cost) {
                return response()->json(['code' => '445120', 'message' => 'You haven\'t enough balance . please recharge first...']);
            } elseif (\BalanceHelper::check_parents_available_balance($user->id, $sms_number, $validUniqueNumbers, $isMasking) == false) {
                return response()->json(['code' => '445130', 'message' => 'Your reseller don\'t have enough balance . told him to recharge first...']);
            } else {
                try {
                    $campaign_id = $user->id . time();
                    if ($isMasking == true) {
                        $sms_masking_type = '2';
                    } else {
                        $sms_masking_type = '1';
                    }

                    $current_date = Carbon::now()->toDateTimeString();

                    $insertCampaign = SmsCampaignId::create([
                        'user_id' => $user->id,
                        'sender_id' => $sender->id,
                        'sci_campaign_id' => $campaign_id,
                        'sci_total_submitted' => count($validUniqueNumbers),
                        'sci_total_cost' => $total_cost,
                        'sci_campaign_type' => '1', /*1=instant, 2=Schedule */
                        'sci_deal_type' => '1', /* 1=SMS, 2=Campaign */
                        'sci_sms_type' => $sms_masking_type, /*1=NonMasking, 2=Masking*/
                        'sci_dynamic_type' => '0',/*1=dynamic, 0=general*/
                        'sci_targeted_time' => $current_date,
                        'sci_browser' => $request->header('User-Agent'),
                        'sci_mac_address' => null,
                        'sci_ip_address' => $request->ip(),
                        'sci_from_api' => 1,
                    ]);


                    $insertCount = 0;
                    $dataForInsert = array();
                    $serial = 0;
                    foreach ($validUniqueNumbers as $number) {
                        $operator = \PhoneNumber::checkOperator($number);

                        $dataForInsert[] = array(
                            'user_id' => $user->id,
                            'sender_id' => $sender->id,
                            'campaign_id' => $insertCampaign->id,
                            'scp_cell_no' => $number,
                            'scp_message' => $request->msg,
                            'scp_sms_cost' => \BalanceHelper::singleSmsCost($sms_number, $number, $isMasking, $user->id),
                            'operator_id' => $operator['id'],
                            'scp_campaign_type' => '1', //*1=instant, 2=Schedule *
                            'scp_deal_type' => '1', //* 1=SMS, 2=Campaign *
                            'scp_sms_type' => $sms_masking_type, //*1=NonMasking, 2=Masking*
                            'scp_sms_id' => '0',
                            'scp_tried' => '0', //*Try For Send *
                            'scp_picked' => '0', //*0=not try, 1= try *
                            'scp_sms_text_type' => $smsType, //*SMS type=text/unicode*
                            'scp_target_time' => $current_date,
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
                    $user_position = $user->position;
                    $user_id = $user->id;

                    $user_det = User::where('id', $user_id)->first();

                    while ($user_position >= 1) {
                        /*get total cost*/
                        $campaign_cost = \BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $user_det->id);

                        AccSmsBalance::create([
                            'asb_paid_by' => $user_det->create_by,
                            'asb_pay_to' => $user_det->id,
                            'asb_pay_ref' => $campaign_id,
                            'asb_credit' => '0',
                            'asb_debit' => $campaign_cost,
                            'asb_submit_time' => $current_date,
                            'asb_target_time' => $current_date,
                            'asb_pay_mode' => '4', //*campaign*
                            'asb_payment_status' => '1', //*1=paid, 2=checking*
                            'asb_deal_type' => '2', //*1=deposit, 2=campaign*
                            'credit_return_type' => '0',
                        ]);

                        $user_det = User::where('id', $user_det->create_by)->first();
                        $user_position = $user_det->position;
                    }

                    /*add user credit history*/
                    AccUserCreditHistory::create([
                        'campaign_id' => $insertCampaign->id,
                        'user_id' => $user->id,
                        'uch_sms_count' => count($validUniqueNumbers),
                        'uch_sms_cost' => $total_cost,
                    ]);

                    return response()->json(['code' => '0', 'message' => 'Message has been sent...']);

                } catch (\Exception $e) {

                    return response()->json(['code' => '445150', 'message' => 'Something was wrong to sent sms. please contact with admin!!! ..']);
                }
            }
        }
    }
}
