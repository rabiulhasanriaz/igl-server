<?php

namespace App\Http\Controllers\Api;

use App\Model\AccSmsBalance;
use App\Model\AccUserCreditHistory;
use App\Model\SenderIdRegister;
use App\Model\SenderIdUser;
use App\Model\SmsDesktopCampaignId;
use App\Model\SmsDesktopPending;
use App\Model\SmsDesktop24h;
use App\Model\User;
use App\Model\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Model\AccSmsRate;
use App\Model\Operator;
use Illuminate\Support\Facades\DB;

class SmsSendDesktopController extends Controller
{
    public function sendSmsDesktop(Request $request)
    {
        
        // $sender = SenderIdRegister::where('id',$request->sender_id)->first();
    //    dd($request->all());
        /*validate data*/
        if(!$request->api_key){
            return response()->json(['code'=>'445010', 'message'=>'Missing api key']);
        }
        elseif (!$request->mobileno){
            return response()->json(['code'=>'445020', 'message'=>'Missing contact numbers']);
        }
        // elseif(!$request->senderid){
        //     return response()->json(['code'=>'445030', 'message'=>'Missing sender id']);
        // }
        elseif (!$request->msg){
            return response()->json(['code'=>'445170', 'message'=>'Missing text sms']);
        }


        /*check exist data*/
        /*check api*/
        $userDetail = UserDetail::where('api_key', $request->api_key)->where('dynamic_permission', 1)->first();
        
        // dd($main_text);
        if(!$userDetail){
            return response()->json(['code'=>'445040', 'message'=>'Invalid api key or You Need API Permission']);
        }
        $user = User::where('id', $userDetail->user_id)->first();
        if($user->status==2){
            return response()->json(['code'=>'445050', 'message'=>'Your account was suspended']);
        }elseif($user->status==3){
            return response()->json(['code'=>'445060', 'message'=>'Your account was expired']);
        }elseif ($user->role!=5){
            if(!$request->for_registration){
                return response()->json(['code'=>'445070', 'message'=>'Only a user can send sms']);
            }elseif (($request->for_registration!='resellerToUser') && ($request->for_registration!='adminToReseller')){
                return response()->json(['code'=>'445071', 'message'=>'Only a user can send sms']);
            }
        }
        
        /*check and get numbers*/
        $allContacts = explode(',',$request->mobileno);
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
            return response()->json(['code'=>'445110', 'message'=>'All numbers are invalid']);
        }

        /*sms count*/
        if ($userDetail->template_permission != NULL) {
            $a = $request->msg;

            $num = null;

            if (strpos($a, 'Code is ') !== false) {
                $parts = explode('Code is ', $a);
                if (isset($parts[1])) {
                    $numberFromText = $parts[1];
                    $num = explode(' ', $numberFromText)[0] ?? null;
                }

            } elseif (strpos($a, 'HI Use ') !== false) {
                $parts = explode('HI Use ', $a);
                if (isset($parts[1])) {
                    $numberFromText = $parts[1];
                    $num = explode(' ', $numberFromText)[0] ?? null;
                }

            } elseif (strpos($a, 'Hi Use ') !== false) {
                $parts = explode('Hi Use ', $a);
                if (isset($parts[1])) {
                    $numberFromText = $parts[1];
                    $num = explode(' ', $numberFromText)[0] ?? null;
                }

            } else {
                preg_match_all('!\d+-?\d+!', $a, $matches);
                if (!empty($matches[0][0])) {
                    $num = $matches[0][0];
                }
            }

            $text = \SmsHelper::main_test_api($userDetail->user_id, $num);

            if ($userDetail->date_format != NULL) {
                $main_text = $text . ".\n" . \SmsHelper::date_format_api($userDetail->user_id);
            } else {
                $main_text = $text;
            }

            $searchReplaceArray = array(
                '0' => json_decode('"0\ufe0f\u20e3"'),
                '1' => json_decode('"1\ufe0f\u20e3"'),
                '2' => json_decode('"2\ufe0f\u20e3"'),
                '3' => json_decode('"3\ufe0f\u20e3"'),
                '4' => json_decode('"4\ufe0f\u20e3"'),
                '5' => json_decode('"5\ufe0f\u20e3"'),
                '6' => json_decode('"6\ufe0f\u20e3"'),
                '7' => json_decode('"7\ufe0f\u20e3"'),
                '8' => json_decode('"8\ufe0f\u20e3"'),
                '9' => json_decode('"9\ufe0f\u20e3"'),
            );

            if (
                strpos($main_text, '0') !== false ||
                strpos($main_text, '1') !== false ||
                strpos($main_text, '2') !== false ||
                strpos($main_text, '3') !== false ||
                strpos($main_text, '4') !== false ||
                strpos($main_text, '5') !== false ||
                strpos($main_text, '6') !== false ||
                strpos($main_text, '7') !== false ||
                strpos($main_text, '8') !== false ||
                strpos($main_text, '9') !== false
            ) {
                $main_text = str_replace(
                    array_keys($searchReplaceArray),
                    array_values($searchReplaceArray),
                    $main_text
                );
            } else {
                $main_text = $request->msg;
            }

        } else {
            $main_text = $request->msg;
        }

        if (\SmsHelper::is_unicode($main_text)) {
            $smsType = 'unicode'; //unicode
            $sms_number = \SmsHelper::unicode_sms_count($main_text);
            // $sms_number_main = \SmsHelper::unicode_sms_count($main_text);

        } else {
            $smsType = 'text'; //text
            $sms_number = \SmsHelper::text_sms_count($request->msg);
        }

        // $isMasking = \SmsHelper::isMasking($sender->id);
        $total_cost = \App\Helpers\BalanceHelper::campaignDesktopTotalCost($sms_number, $validUniqueNumbers, $user->id);


        if (\App\Helpers\BalanceHelper::user_available_balance($user->id) < $total_cost) {
            return response()->json(['code'=>'445120', 'message'=>'You haven\'t enough balance . please recharge first...']);
        } elseif (\App\Helpers\BalanceHelper::check_parents_Desktop_available_balance($user->id, $sms_number, $validUniqueNumbers) == false) {
            return response()->json(['code'=>'445130', 'message'=>'Your reseller don\'t have enough balance . told him to recharge first...']);
        } else {
            try {
                $campaign_id = $user->id . time() . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9);
                // if ($isMasking == true) {
                //     $sms_masking_type = '2';
                // } else {
                    $sms_masking_type = '1';
                // }
               
                    
                $current_date = Carbon::now()->toDateTimeString();

                $sms_sender_op = null;
                    
                
                    
                $insertCampaign = SmsDesktopCampaignId::create([
                    'user_id' => $user->id,
                    // 'sender_id' => $sender->id,
                    'sdci_campaign_id' => $campaign_id,
                    'sdci_total_submitted' => count($validUniqueNumbers),
                    'sdci_total_cost' => $total_cost,
                    'sdci_campaign_type' => '1', /*1=instant, 2=Schedule */
                    'sdci_deal_type' => '1', /* 1=SMS, 2=Campaign */
                    'sdci_sms_type' => $sms_masking_type, /*1=NonMasking, 2=Masking*/
                    'sdci_sender_operator' => $sms_sender_op, /*1=NonMasking, 2=Masking*/
                    'sdci_dynamic_type' => '0',/*1=dynamic, 0=general*/
                    'sdci_targeted_time' => $current_date,
                    'sdci_browser' => $request->header('User-Agent'),
                    'sdci_mac_address' => null,
                    'sdci_ip_address' => $request->ip(),
                    'sdci_from_api' => 4,
                ]);

                
                // Load operator prefixes and the user's dynamic rates once.
                $operatorByPrefix = [];
                foreach (Operator::select('id', 'ope_number')->get() as $operatorRow) {
                    foreach (explode(',', $operatorRow->ope_number) as $prefix) {
                        $operatorByPrefix[trim($prefix)] = $operatorRow->id;
                    }
                }

                $rateByOperator = AccSmsRate::where('user_id', $user->id)
                    ->pluck('asr_dynamic', 'operator_id')
                    ->toArray();

                $pendingRows = [];

                foreach ($validUniqueNumbers as $number) {
                    $prefix = substr($number, 0, 5);
                    $operatorId = $operatorByPrefix[$prefix] ?? null;

                    if (!$operatorId || !array_key_exists($operatorId, $rateByOperator)) {
                        throw new \RuntimeException(
                            'Operator or SMS rate is not configured for ' . $number
                        );
                    }

                    $pendingRows[] = [
                        'user_id' => $user->id,
                        'campaign_id' => $insertCampaign->id,
                        'sdp_cell_no' => $number,
                        'sdp_message' => $main_text,
                        'sdp_customer_message' => $request->msg,
                        'sdp_sms_cost' => round(
                            ((float) $rateByOperator[$operatorId]) * $sms_number,
                            4
                        ),
                        'operator_id' => $operatorId,
                        'sdp_campaign_type' => '1',
                        'sdp_deal_type' => '1',
                        'sdp_sms_type' => $sms_masking_type,
                        'sdp_sms_id' => '0',
                        'sdp_tried' => '0',
                        'sdp_picked' => '0',
                        'sdp_sms_text_type' => $smsType,
                        'sdp_campaign_status' => '0',
                        'sdp_target_time' => $current_date,
                        'sdp_status' => '4',
                        'created_at' => $current_date,
                        'updated_at' => $current_date,
                    ];
                }

                DB::transaction(function () use (
                    $pendingRows,
                    $user,
                    $sms_number,
                    $validUniqueNumbers,
                    $campaign_id,
                    $current_date,
                    $insertCampaign,
                    $total_cost
                ) {
                    foreach (array_chunk($pendingRows, 1000) as $chunk) {
                        SmsDesktopPending::insert($chunk);
                    }

                    /*debit user balance*/
                    $user_det = $user;
                    $user_position = $user_det->position;

                    while ($user_det && $user_position >= 1) {
                        $campaign_cost = \App\Helpers\BalanceHelper::campaignDesktopTotalCost(
                            $sms_number,
                            $validUniqueNumbers,
                            $user_det->id
                        );

                        \App\Helpers\BalanceHelper::addDebit(
                            $user_det->create_by,
                            $user_det->id,
                            $campaign_id,
                            $campaign_cost,
                            4,
                            1,
                            2,
                            $current_date
                        );

                        $user_det = User::find($user_det->create_by);
                        $user_position = $user_det ? $user_det->position : 0;
                    }

                    /*add user credit history*/
                    AccUserCreditHistory::create([
                        'campaign_id' => $insertCampaign->id,
                        'user_id' => $user->id,
                        'uch_sms_count' => count($validUniqueNumbers),
                        'uch_sms_cost' => $total_cost,
                    ]);
                }, 3);

                return response()->json(['code'=>'445000', 'message'=>'Message has been sent...']);

            } catch (\Exception $e) {

                return response()->json(['code'=>'445150', 'message'=>'Something was wrong to sent sms. please contact with admin!!! ..'.$e->getMessage()]);
            }
        }

    }

    public function showBalance(Request $request)
    {
        /*validate data*/
        if(!$request->api_key){
            return response()->json(['code'=>'445010', 'message'=>'Missing api key']);
        }
        /*check api*/
        $userDetail = UserDetail::where('api_key', $request->api_key)->first();
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
            $userAvailableBalance = \App\Helpers\BalanceHelper::user_available_balance($user->id);
            return response()->json(['code'=>'445000', 'balance'=>number_format($userAvailableBalance, 2)." tk"]);
        }catch (Exception $e){
            return response()->json(['code'=>'445160', 'message'=>'Something was wrong to check balance. please contact with admin!!! ..']);
        }
    }


//     public function sms_pending(Request $request)
//     {
        
//         $pendingdatacount = SmsDesktopPending::where('sdp_campaign_status', 0)->get();
//         if (count($pendingdatacount) > 0) {
//             foreach ($pendingdatacount as $pendingData) {
//                 $limitSms = 50;
//                 $sms = array();
//                 $result = array(); 
//                 $transferredSmsId = array();
//                 $getCampaignIds = SmsDesktopPending::where([
//                     'campaign_id' => $pendingData->campaign_id,
//                     'sdp_campaign_status' => 0,
//                     'sdp_message' => $pendingData->sdp_message])
//                     ->take($limitSms)
//                     ->get();
//                 // dd($getSms50OfSameCampaignIds);
//                 $numbers = array();
//                 $msg = array();
//                 $messageType = array();
//                 $status = array();
//                 $id = array();
                
//                 foreach ($getCampaignIds as $smsDetails) {

//                     $response = array( 
//                         'id' => $smsDetails->id,
//                         'msg_type' => $smsDetails->sdp_sms_text_type,
//                         'mobileno' => $smsDetails->sdp_cell_no, 
//                         'smsbody' => $smsDetails->sdp_message, 
//                     ); 
//                 array_push($result, $response); 

//                  $pendingdataupdate = SmsDesktopPending::where('id', $smsDetails->id)->update(['sdp_campaign_status'=> 1]);

//                     // dd($sms50Details);
                   
//                 }

//                 return response()->json(['response' => 3,'data'=>$result], 200);
        
        

        


//         //
//     }
// }else{
//     return response()->json([
//             'data' => 'Not Found',
//             'status' => 2,
//             'response' => 2
//         ], 200);
// }
// }


    // public function sms_message_store(Request $request)
    // {

    //     /*$request->validate([
    //         'sim' => 'required',
    //         'op' => 'required',
    //         'msg' => 'required',
    //         'phone' => 'required',
    //         'st' => 'nullable'
    //     ]);*/
    //     // if (empty($request->msg)) {
    //     //     return response()->json([
    //     //         'msg' => 'message empty',
    //     //         'status' => 2
    //     //     ], 200);
    //     // }

        


    //     try {
            
    //         $checkedSms = SmsDesktopPending::where('id', $request->id)->where('sdp_campaign_status',1)->first();
    //                             // dd($checkedSms);
    //         if (empty($checkedSms)) {
    //             return response()->json([
    //                 'msg' => 'message empty',
    //                 'status' => 2
    //             ], 200);
    //         }
    //         $blDataForInsert[] = array(
    //             'user_id' => $checkedSms->user_id,
    //             // 'sender_id' => $checkedSms->sender_id,
    //             'campaign_id' => $checkedSms->campaign_id,
    //             'sdt_cell_no' => $checkedSms->sdp_cell_no,
    //             'sdt_message' => $checkedSms->sdp_message,
    //             'sdt_sms_cost' => $checkedSms->sdp_sms_cost,
    //             'operator_id' => $checkedSms->operator_id,
    //             'sdt_campaign_type' => $checkedSms->sdp_campaign_type,
    //             'sdt_deal_type' => $checkedSms->sdp_deal_type,
    //             'sdt_sms_type' => $checkedSms->sdp_sms_type,
    //             'sdt_sms_id' => '0',
    //             'sdt_sms_text_type' => $checkedSms->sdp_sms_text_type,
    //             'sdt_target_time' => $checkedSms->sdp_target_time,
    //             'created_at' => $checkedSms->created_at,
    //             'updated_at' => $checkedSms->updated_at,
    //             'sdt_delivery_report' => 'DELIVERED',
    //             'sdt_status' => '0',
    //         );

    //         SmsDesktop24h::insert($blDataForInsert);
    //         $blDataForInsert = array();

    //         SmsDesktopPending::where('id', $request->id)->delete();

            

            
    //     } catch (\Exception $exception) {

    //     }


    //     return response()->json([
    //         'data' => 'message insert successfully',
    //         'status' => 1,
    //         'insert' => 1
    //     ], 200);

    // }
}