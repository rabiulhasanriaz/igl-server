<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

use App\Model\SenderIdRegister;
use App\Model\SenderIdVirtualNumber;
use App\Model\SmsCampaign;
use App\Model\SmsCampaign_24h;
use App\Model\SmsCamPending;
use App\Model\UserDetail;
use App\Model\SmsDesktopCampaignId;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class ProcessSmsCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $returnError['gpError'] = "";
        $returnData['gp'] = "";
        $returnError['blError'] = "";
        $returnData['banglalink'] = "";
        $returnError['robiAirtelError'] = "";
        $returnData['robi_airtel'] = "";
        $returnData['teletalk'] = "";
        $returnError['ttError'] = "";
        $returnData['message'] = "";
        $returnError['errorNotify'] = "";

        $non_masking_return_text = "";
        $retTextRobi = "No content found.";
        $retTextGp = "No content found.";
        $retTextBl = "No content found.";
        $retTextRankstel = "No content found.";
        $retTextFusion = "No content found.";
        $retTextIptsp  = "No content found.";

        $getNonMaskingSmsCampaigns = SmsCamPending::where('scp_sms_type', '1')
            ->where('scp_target_time', '<=', Carbon::now())
            ->where('scp_campaign_status', 1)
            ->groupBy('sender_id')
            ->groupBy('scp_message')
            ->take(10)
            ->orderBy('id', 'asc')
            ->get();

        $invalidResponse = ['1001', '1002', '1003', '1004', '1005', '1006', '1007', '1008', '1009', '1010', '1011', '1012', '1013', '1014', '1015', '1016', '1017', '1018', '1019', '1020', '1050', '1051', '1052', '1053', '1054'];

        $apiCentral = 'myQ1uzu3mRVWdjVq4A1mV5GscebslZ4y';
        $transType = 'T';

        // ======================================
        // ======Non-Masking SMS Send START======
        // ======================================
        if (count($getNonMaskingSmsCampaigns) > 0) {
            $smsLoop = 1;
            foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

                $limitSms = 100;
                $sms = array();
                $transferredSmsId = array();
                $getSms50OfSameCampaignIds = SmsCamPending::where([
                    'campaign_id' => $nonMaskingSmsCampaign->campaign_id,
                    'scp_campaign_status' => 1,
                    'scp_message' => $nonMaskingSmsCampaign->scp_message,
                    'sender_id' => $nonMaskingSmsCampaign->sender_id])
                    ->take($limitSms)
                    ->get();
                    
                $numbers = array();

                foreach ($getSms50OfSameCampaignIds as $sms50Details) {
                    $countTSms = 0;
                    $sender_id = $nonMaskingSmsCampaign->sender->sir_sender_id;
                    $numbers = [];
                    $transferredSmsId = [];
                    
                    $numbers[] = $sms50Details->scp_cell_no;
                    $transferredSmsId[] = $sms50Details->id;

                    if ($sms50Details->scp_sms_text_type= 'unicode') {
                        $msgType = '3';
                    }elseif ($sms50Details->scp_sms_text_type= 'text') {
                        $msgType = '1';
                    }else {
                        $msgType = '1';
                    }
                    $operators = DB::table('operators')->get();
                    $matchedOperator = null;
                   
                    foreach ($operators as $operator) {
                        $prefixLength = $operator->ope_count;
                        $prefixToCheck = substr($sender_id, 0, $prefixLength);
                        
                        if ($prefixToCheck == $operator->ope_number) {
                            $matchedOperator = $operator;
                            break;
                        }
                    }

                    // =======================
                    // ===Robi/Airtel START===
                    // =======================
                    if (substr($sender_id, 0, 3) == '018') {

                        $senderIdDet = SenderIdRegister::with('robi_virtual_number')->where('sir_sender_id', $sender_id)->first();
                        if (empty($senderIdDet)) {
                            $retText = "Something was missing1";
                            continue;
                        }

                        $xml_response = \SmsHelper::ra_non_masking_iptsp_sms('felna', 'Felna123', $nonMaskingSmsCampaign->scp_message, $numbers, '8809648903075', '8809648903075', $apiCentral, $transType, $msgType);

                        $responseCode = $xml_response['serverResponseCode'] ?? null;
                        $mnoTxnId = $xml_response['mnoTxnId'] ?? null;
                        $mnoResponseCode = $xml_response['mnoResponseCode'] ?? null;
                        $mnoResponseMessage = $xml_response['mnoResponseMessage'] ?? null;

                        if ($responseCode == '9001') {
                            $retText = "Required field missing!";
                        } elseif ($responseCode == '9002') {
                            $retText = "Client credentials mismatch!";
                        } elseif ($responseCode == '9003') {
                            $retText = "Out of balance!";
                        } elseif ($responseCode == '9004') {
                            $retText = "Insufficient balance!";
                        } elseif ($responseCode == '9005') {
                            $retText = "Client not found!";
                        } elseif ($responseCode == '9006') {
                            $retText = "Invalid source IP!";
                        } elseif ($responseCode == '9007') {
                            $retText = "Invalid bill MSISDN!";
                        } elseif ($responseCode == '9008') {
                            $retText = "Invalid CLI!";
                        } elseif ($responseCode == '9009') {
                            $retText = "Missing destination MSISDN!";
                        } elseif ($responseCode == '9010') {
                            $retText = "Max limit for destination MSISDN exceeds!";
                        } elseif ($responseCode == '9011') {
                            $retText = "MNO server failure!";
                        } elseif ($responseCode == '9012') {
                            $retText = "Dipping failure!";
                        } elseif ($responseCode == '9014') {
                            $retText = "Invalid keyword!";
                        } elseif ($responseCode == '9015') {
                            $retText = "DND server failure!";
                        } elseif ($responseCode == '9016') {
                            $retText = "Invalid check delivery request!";
                        } elseif ($responseCode == '9019') {
                            $retText = "Invalid transaction type!";
                        } elseif ($responseCode == '9020') {
                            $retText = "Invalid message type!";
                        } elseif ($responseCode == '9099') {
                            $retText = "Server failure!";
                        } elseif ($xml_response == '0150') {
                            $retText = "Something was missing";
                        } elseif ($xml_response == '0160') {
                            $retText = "Something Went Wrong to call robi non-masking api";
                        } elseif ($responseCode == '9000' && in_array($mnoResponseCode, $invalidResponse)) {
                            $retText = $mnoResponseMessage;
                        } elseif ($responseCode == '9000' && $mnoResponseCode == '1000') {
                            $dataForInsert = array();

                            $smsId = $transferredSmsId[$countTSms];
                            $checkedSms = SmsCamPending::where('id', $smsId)->first();

                            $dataForInsert = [
                                'user_id' => $checkedSms->user_id,
                                'sender_id' => $checkedSms->sender_id,
                                'campaign_id' => $checkedSms->campaign_id,
                                'sct_cell_no' => $checkedSms->scp_cell_no,
                                'sct_message' => $checkedSms->scp_message,
                                'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                'operator_id' => $checkedSms->operator_id,
                                'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                'sct_deal_type' => $checkedSms->scp_deal_type,
                                'sct_sms_type' => $checkedSms->scp_sms_type,
                                'sct_sms_id' => $mnoTxnId,
                                'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                'sct_target_time' => $checkedSms->scp_target_time,
                                'created_at' => $checkedSms->created_at,
                                'updated_at' => $checkedSms->updated_at,
                                'sct_delivery_report' => 'DELIVERED',
                                'sct_status' => $mnoResponseCode,
                            ];
                            $countTSms++;
                            
                            try {
                                SmsCampaign_24h::insert($dataForInsert);
                                $dataForInsert = [];
                                SmsCamPending::where('id', $smsId)->delete();
                                $retText = "Working..." . $smsLoop++;

                            } catch (\Exception $e) {
                                $retText = "Something went wrong";
                            }
                        } else {
                            $retText = "No Condition Matched - Something went wrong";
                        }
                        $retTextRobi = $retText;
                    }
                    elseif ($matchedOperator) {
                        $virtualNumber = SenderIdVirtualNumber::where('operator_id', $matchedOperator->id)
                            ->where('sivn_status', 1)
                            ->first();

                        if ($virtualNumber) {
                            $username = $virtualNumber->sivn_api_user_name;
                            $password = $virtualNumber->sivn_api_password;
       
                            $billMSISDN = $sender_id;
                            $cli = $sender_id;

                            $xml_response = \SmsHelper::ra_non_masking_iptsp_sms(
                                $username,
                                $password,
                                $nonMaskingSmsCampaign->scp_message,
                                $numbers,
                                $billMSISDN,
                                $cli,
                                $apiCentral,
                                $transType,
                                $msgType
                            );

                            $responseCode = $xml_response['serverResponseCode'] ?? null;
                            $mnoTxnId = $xml_response['mnoTxnId'] ?? null;
                            $mnoResponseCode = $xml_response['mnoResponseCode'] ?? null;
                            $mnoResponseMessage = $xml_response['mnoResponseMessage'] ?? null;

                            if ($responseCode == '9001') {
                                $retText = "Required field missing!";
                            } elseif ($responseCode == '9002') {
                                $retText = "Client credentials mismatch!";
                            } elseif ($responseCode == '9003') {
                                $retText = "Out of balance!";
                            } elseif ($responseCode == '9004') {
                                $retText = "Insufficient balance!";
                            } elseif ($responseCode == '9005') {
                                $retText = "Client not found!";
                            } elseif ($responseCode == '9006') {
                                $retText = "Invalid source IP!";
                            } elseif ($responseCode == '9007') {
                                $retText = "Invalid bill MSISDN!";
                            } elseif ($responseCode == '9008') {
                                $retText = "Invalid CLI!";
                            } elseif ($responseCode == '9009') {
                                $retText = "Missing destination MSISDN!";
                            } elseif ($responseCode == '9010') {
                                $retText = "Max limit for destination MSISDN exceeds!";
                            } elseif ($responseCode == '9011') {
                                $retText = "MNO server failure!";
                            } elseif ($responseCode == '9012') {
                                $retText = "Dipping failure!";
                            } elseif ($responseCode == '9014') {
                                $retText = "Invalid keyword!";
                            } elseif ($responseCode == '9015') {
                                $retText = "DND server failure!";
                            } elseif ($responseCode == '9016') {
                                $retText = "Invalid check delivery request!";
                            } elseif ($responseCode == '9019') {
                                $retText = "Invalid transaction type!";
                            } elseif ($responseCode == '9020') {
                                $retText = "Invalid message type!";
                            } elseif ($responseCode == '9099') {
                                $retText = "Server failure!";
                            } elseif ($xml_response == '0150') {
                                $retText = "Something was missing";
                            } elseif ($xml_response == '0160') {
                                $retText = "Something Went Wrong to call API";
                            } elseif ($responseCode == '9000' && in_array($mnoResponseCode, $invalidResponse)) {
                                $retText = $mnoResponseMessage;
                            } elseif ($responseCode == '9000' && $mnoResponseCode == '1000') {
                                $dataForInsert = [];
                                $countTSms = 0;

                                foreach ($transferredSmsId as $smsId) {
                                    $checkedSms = SmsCamPending::where('id', $smsId)->first();
                                    
                                    $dataForInsert[] = [
                                        'user_id' => $checkedSms->user_id,
                                        'sender_id' => $checkedSms->sender_id,
                                        'campaign_id' => $checkedSms->campaign_id,
                                        'sct_cell_no' => $checkedSms->scp_cell_no,
                                        'sct_message' => $checkedSms->scp_message,
                                        'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                        'operator_id' => $checkedSms->operator_id,
                                        'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                        'sct_deal_type' => $checkedSms->scp_deal_type,
                                        'sct_sms_type' => $checkedSms->scp_sms_type,
                                        'sct_sms_id' => $mnoTxnId,
                                        'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                        'sct_target_time' => $checkedSms->scp_target_time,
                                        'created_at' => $checkedSms->created_at,
                                        'updated_at' => $checkedSms->updated_at,
                                        'sct_delivery_report' => 'DELIVERED',
                                        'sct_status' => $mnoResponseCode,
                                    ];
                                    $countTSms++;
                                }

                                try {
                                    SmsCampaign_24h::insert($dataForInsert);
                                    SmsCamPending::whereIn('id', $transferredSmsId)->delete();
                                    $retText = "Working..." . $smsLoop++;
                                } catch (\Exception $e) {
                                    $retText = "Database error: " . $e->getMessage();
                                }
                            } else {
                                $retText = "No Condition Matched - Something went wrong";
                            }

                            $retTextIptsp = $retText;
                        } else {
                            $retTextIptsp = "No active virtual number found for operator: " . $matchedOperator->ope_operator_name;
                        }
                    }
                    elseif ((substr($sender_id, 0, 5) == '017') || (substr($sender_id, 0, 5) == '013')) {

                        $senderIdDet = SenderIdRegister::with('gp_virtual_number')->where('sir_sender_id', $sender_id)->first();
                        if (empty($senderIdDet)) {
                            $retTextGp = "Something was missing1";
                            continue;
                        }

                        $user_name = "IGLWAdmin";
                        $password = "qazXSW11!!";
                        $xml_response = \SmsHelper::update_send_masking_gp_sms($user_name, $password, $sender_id, $nonMaskingSmsCampaign->scp_message, $numbers);

                        $gpDataForInsert = array();

                        $response_array = explode("\n", $xml_response);
                        $status_code = explode(',', $response_array[0]);

                        if ($responseCode == '9001') {
                            $retTextGp = "Required field missing!";
                        } elseif ($responseCode == '9002') {
                            $retTextGp = "Client credentials mismatch!";
                        } elseif ($responseCode == '9003') {
                            $retTextGp = "Out of balance!";
                        } elseif ($responseCode == '9004') {
                            $retTextGp = "Insufficient balance!";
                        } elseif ($responseCode == '9005') {
                            $retTextGp = "Client not found!";
                        } elseif ($responseCode == '9006') {
                            $retTextGp = "Invalid source IP!";
                        } elseif ($responseCode == '9007') {
                            $retTextGp = "Invalid bill MSISDN!";
                        } elseif ($responseCode == '9008') {
                            $retTextGp = "Invalid CLI!";
                        } elseif ($responseCode == '9009') {
                            $retTextGp = "Missing destination MSISDN!";
                        } elseif ($responseCode == '9010') {
                            $retTextGp = "Max limit for destination MSISDN exceeds!";
                        } elseif ($responseCode == '9011') {
                            $retTextGp = "MNO server failure!";
                        } elseif ($responseCode == '9012') {
                            $retTextGp = "Dipping failure!";
                        } elseif ($responseCode == '9014') {
                            $retTextGp = "Invalid keyword!";
                        } elseif ($responseCode == '9015') {
                            $retTextGp = "DND server failure!";
                        } elseif ($responseCode == '9016') {
                            $retTextGp = "Invalid check delivery request!";
                        } elseif ($responseCode == '9019') {
                            $retTextGp = "Invalid transaction type!";
                        } elseif ($responseCode == '9020') {
                            $retTextGp = "Invalid message type!";
                        } elseif ($responseCode == '9099') {
                            $retTextGp = "Server failure!";
                        } elseif ($xml_response == '0150') {
                            $retTextGp = "Something was missing";
                        } elseif ($xml_response == '0160') {
                            $retTextGp = "Something Went Wrong to call GP non-masking api";
                        } elseif ($responseCode == '9000' && in_array($mnoResponseCode, $invalidResponse)) {
                            $retTextGp = $mnoResponseMessage;
                        }
                        elseif ($responseCode == '9000' && $mnoResponseCode == '1000') {
                            foreach ($response_array as $oneReport){
                                $report_details = explode(',', $oneReport);

                                $aaa = isset($report_details[2])?@$report_details[2]:@$report_details[1];
                                if($aaa == "TPS limit Exceeded")
                                    continue;

                                if (isset($report_details[1])) {
                                    $gpSmsDetail = SmsCamPending::where('id', $transferredSmsId[$countTSms])->first();
                                    $gpTransferredForDelete[] = $gpSmsDetail->id;

                                    $gpDataForInsert[] = array(
                                        'user_id' => $gpSmsDetail->user_id,
                                        'sender_id' => $gpSmsDetail->sender_id,
                                        'campaign_id' => $gpSmsDetail->campaign_id,
                                        'sct_cell_no' => $gpSmsDetail->scp_cell_no,
                                        'sct_message' => $gpSmsDetail->scp_message,
                                        'sct_sms_cost' => $gpSmsDetail->scp_sms_cost,
                                        'operator_id' => $gpSmsDetail->operator_id,
                                        'sct_campaign_type' => $gpSmsDetail->scp_campaign_type,
                                        'sct_deal_type' => $gpSmsDetail->scp_deal_type,
                                        'sct_sms_type' => $gpSmsDetail->scp_sms_type,
                                        'sct_sms_id' => isset($report_details[2])?@$report_details[2]:@$report_details[1],
                                        'sct_sms_text_type' => $gpSmsDetail->scp_sms_text_type,
                                        'sct_target_time' => $gpSmsDetail->scp_target_time,
                                        'created_at' => $gpSmsDetail->created_at,
                                        'updated_at' => $gpSmsDetail->updated_at,
                                        'sct_delivery_report' => 'DELIVERED',
                                        'sct_status' => isset($report_details[2])?@$report_details[1]:@$report_details[0],
                                    );
                                }
                                $countTSms++;
                            }
                            try {
                                SmsCampaign_24h::insert($gpDataForInsert);
                                $gpDataForInsert = array();

                                SmsCamPending::whereIn('id', $gpTransferredForDelete)->delete();
                                $retTextGp = "GP Non-Masking is Working...";
                            } catch (\Exception $e) {
                                $retTextGp = "something went wrong" . $e->getMessage();
                            }
                        } else {
                            $retTextGp = "No Condition Matched - Something went wrong";
                        }
                    }
                    elseif ((substr($sender_id, 0, 5) == '019') || (substr($sender_id, 0, 5) == '014')) {

                        $senderIdDet = SenderIdRegister::with('banglalink_virtual_number')->where('sir_sender_id', $sender_id)->first();
                        if (empty($senderIdDet)) {
                            $retTextBl = "Something was missing1";
                            continue;
                        }

                        $user_name = "igl";
                        $password = "397c6678b1ca033edbeba622573ed000";
                        $xml_response = \SmsHelper::send_masking_banglalink_sms($user_name, $password, $nonMaskingSmsCampaign->scp_message, $numbers, $sender_id);
                        
                        if ($xml_response == '0150') {
                            $retTextBl = "something was missing for banglalink masking sms";
                        } elseif ($xml_response == 'bl_error') {
                            $retTextBl = "something went wrong to send banglalink masking sms";
                        } elseif ($responseCode == '9000' && $mnoResponseCode == '1000') {
                            $blDataForInsert = array();
                            foreach ($numbers as $blNumber) {

                                $checkedSms = SmsCamPending::where('id', $transferredSmsId[$countTSms])->first();
                                $blTransferred[] = $checkedSms->id;

                                $blDataForInsert[] = array(
                                    'user_id' => $checkedSms->user_id,
                                    'sender_id' => $checkedSms->sender_id,
                                    'campaign_id' => $checkedSms->campaign_id,
                                    'sct_cell_no' => $checkedSms->scp_cell_no,
                                    'sct_message' => $checkedSms->scp_message,
                                    'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                    'operator_id' => $checkedSms->operator_id,
                                    'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                    'sct_deal_type' => $checkedSms->scp_deal_type,
                                    'sct_sms_type' => $checkedSms->scp_sms_type,
                                    'sct_sms_id' => '0',
                                    'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                    'sct_target_time' => $checkedSms->scp_target_time,
                                    'created_at' => $checkedSms->created_at,
                                    'updated_at' => $checkedSms->updated_at,
                                    'sct_delivery_report' => 'DELIVERED',
                                    'sct_status' => '0',
                                );

                                $countTSms++;
                            }
                            try {
                                SmsCampaign_24h::insert($blDataForInsert);
                                $blDataForInsert = array();

                                SmsCamPending::whereIn('id', $blTransferred)->delete();

                                $retTextBl = "Banglalink Non Masking Working...";

                            } catch (\Exception $e) {
                                $retTextBl = "something went wrong" . $e->getMessage();
                            }
                        }
                    } else {
                        $non_masking_return_text = 'Something went wrong!';
                    }
                }
            }

        } else {
            $retTextRobi = "No content found.";
            $retTextGp = "No content found.";
            $retTextBl = "No content found.";
            $retTextRankstel = "No content found.";
            $retTextFusion = "No content found.";
        }

        // ***************************************
        // ***********Non-Masking END*************
        // ***************************************

        // ======================================
        // ========Masking SMS Send START========
        // ======================================

        for ($loopNo = 1; $loopNo <= 5; $loopNo++) {

            $getMaskingSmsCampaigns = SmsCamPending::where('scp_sms_type', '2')
                ->where('scp_target_time', '<=', Carbon::now())
                ->where('scp_campaign_status', 1)
                ->where('scp_deal_type', 1)
                ->groupBy('sender_id')
                ->groupBy('scp_message')
                ->groupBy('campaign_id')
                ->orderBy('id', 'asc')
                ->take(10)
                ->get();

            if (count($getMaskingSmsCampaigns) > 0) {

                $limitSms = 60;

                foreach ($getMaskingSmsCampaigns as $maskingSmsCampaign) {
                    $ttNumbers = array();
                    $raTransferred = array();
                    $ttTransferred = array();

                    $getSms50OfSameCampaignIds = SmsCamPending::where([
                        'campaign_id' => $maskingSmsCampaign->campaign_id,
                        'scp_campaign_status' => 1,
                        'scp_message' => $maskingSmsCampaign->scp_message,
                        'sender_id' => $maskingSmsCampaign->sender_id])
                        ->take($limitSms)
                        ->get();

                    foreach ($getSms50OfSameCampaignIds as $smsCamp) {

                        $virtualNumber = SenderIdRegister::with('robi_virtual_number', 'airtel_virtual_number', 'banglalink_virtual_number', 'teletalk_virtual_number', 'gp_virtual_number')
                        ->where('id', $smsCamp->sender_id)
                        ->first();

                        // =======================
                        // ========GP START=======
                        // =======================
                        try {
                            $gpSmsOfThisCampaigns = SmsCamPending::where([
                                'campaign_id' => $smsCamp->campaign_id,
                                'scp_campaign_status' => 1,
                                'scp_message' => $smsCamp->scp_message,
                                'sender_id' => $smsCamp->sender_id,
                                'operator_id' => '3'])
                                ->orderBy('id', 'desc')
                                ->take($limitSms)
                                ->get();

                            if (count($gpSmsOfThisCampaigns) > 0) {

                                foreach ($gpSmsOfThisCampaigns as $gpSmsDetails) {
                                    $countTSms = 0;
                                    $gpNumbers = [];
                                    $gpTransferred = [];

                                    $gpNumbers[] = $gpSmsDetails->scp_cell_no;
                                    $gpTransferred[] = $gpSmsDetails->id;

                                    if ($gpSmsDetails->scp_sms_text_type= 'unicode') {
                                        $msgType = '3';
                                    }elseif ($gpSmsDetails->scp_sms_text_type= 'text') {
                                        $msgType = '1';
                                    }else {
                                        $msgType = '1';
                                    }

                                    $user_name = $virtualNumber->gp_virtual_number->sivn_api_user_name;
                                    $billMsi = $virtualNumber->gp_virtual_number->sivn_number;
                                    $password = $virtualNumber->gp_virtual_number->sivn_api_password;
                                    $sender = $smsCamp->sender->sir_sender_id;
                                    $sms_text = $smsCamp->scp_message;
                                    
                                    $xml_response = \SmsHelper::gp_masking_infozillion_sms($user_name, $password, $sms_text, $gpNumbers, $sender, $billMsi, $apiCentral, $transType, $msgType);

                                    $responseCode = $xml_response['serverResponseCode'] ?? null;
                                    $mnoTxnId = $xml_response['mnoTxnId'] ?? null;
                                    $mnoResponseCode = $xml_response['mnoResponseCode'] ?? null;
                                    $mnoResponseMessage = $xml_response['mnoResponseMessage'] ?? null;

                                    if ($mnoResponseCode == '1008') {
                                        $check_v_n = "88".$billMsi;
                                        $exists_load = SenderIdVirtualNumber::where('sivn_number', $billMsi)
                                            ->where(function ($query) {
                                                $query->where('last_load_time' , '>', Carbon::now()->subMinutes(20) );

                                            })
                                            ->first();
                                        if ( !$exists_load ) {
                                            $load = SenderIdVirtualNumber::where('sivn_number', $billMsi)
                                                ->first();
                                            if (!empty($load)) {
                                                if ($load->sivn_load_amount >= 10) {
                                                    try {
                                                        $load_number = $check_v_n;

                                                        $flexi_api_key = "445015812203613491581220361";
                                                        $pin = "3799";
                                                        $amount = $load->sivn_load_amount;
                                                        $number_type = "2";
                                                        $operator = "gp";
                                                        $url = "http://127.0.0.1/api/v1/send-load?api_key=$flexi_api_key&pin=$pin&number=$load_number&amount=$amount&number_type=$number_type&operator=$operator";

                                                        $client = new Client();
                                                        $res = $client->request('GET', $url);
                                                        $ret = $res->getBody();

                                                        $load->last_load_time = Carbon::now();
                                                        $load->save();
                                                        echo "laod requested";
                                                    } catch (\Exception $e) {
                                                        echo 'Auto Load Problem. ' . $e->getMessage();
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    if ($responseCode == '9001') {
                                        $returnError['gpError'] = "Required field missing!";
                                    } elseif ($responseCode == '9002') {
                                        $returnError['gpError'] = "Client credentials mismatch!";
                                    } elseif ($responseCode == '9003') {
                                        $returnError['gpError'] = "Out of balance!";
                                    } elseif ($responseCode == '9004') {
                                        $returnError['gpError'] = "Insufficient balance!";
                                    } elseif ($responseCode == '9005') {
                                        $returnError['gpError'] = "Client not found!";
                                    } elseif ($responseCode == '9006') {
                                        $returnError['gpError'] = "Invalid source IP!";
                                    } elseif ($responseCode == '9007') {
                                        $returnError['gpError'] = "Invalid bill MSISDN!";
                                    } elseif ($responseCode == '9008') {
                                        $returnError['gpError'] = "Invalid CLI!";
                                    } elseif ($responseCode == '9009') {
                                        $returnError['gpError'] = "Missing destination MSISDN!";
                                    } elseif ($responseCode == '9010') {
                                        $returnError['gpError'] = "Max limit for destination MSISDN exceeds!";
                                    } elseif ($responseCode == '9011') {
                                        $returnError['gpError'] = "MNO server failure!";
                                    } elseif ($responseCode == '9012') {
                                        $returnError['gpError'] = "Dipping failure!";
                                    } elseif ($responseCode == '9014') {
                                        $returnError['gpError'] = "Invalid keyword!";
                                    } elseif ($responseCode == '9015') {
                                        $returnError['gpError'] = "DND server failure!";
                                    } elseif ($responseCode == '9016') {
                                        $returnError['gpError'] = "Invalid check delivery request!";
                                    } elseif ($responseCode == '9019') {
                                        $returnError['gpError'] = "Invalid transaction type!";
                                    } elseif ($responseCode == '9020') {
                                        $returnError['gpError'] = "Invalid message type!";
                                    } elseif ($responseCode == '9099') {
                                        $returnError['gpError'] = "Server failure!";
                                    } elseif ($xml_response == '0150') {
                                        $returnError['gpError'] = "Something was missing";
                                    } elseif ($xml_response == '0160') {
                                        $returnError['gpError'] = "Something Went Wrong to call GP masking API";
                                    } elseif ($responseCode == '9000' && in_array($mnoResponseCode, $invalidResponse)) {
                                        $returnError['gpError'] = $mnoResponseMessage;
                                    } elseif ($responseCode == '9000' && $mnoResponseCode == '1000') {
                                        $gpDataForInsert = [];

                                        $smsId = $gpTransferred[$countTSms];

                                        $gpSmsDetail = SmsCamPending::where('id', $smsId)->first();

                                        $gpDataForInsert[] = array(
                                            'user_id' => $gpSmsDetail->user_id,
                                            'sender_id' => $gpSmsDetail->sender_id,
                                            'campaign_id' => $gpSmsDetail->campaign_id,
                                            'sct_cell_no' => $gpSmsDetail->scp_cell_no,
                                            'sct_message' => $gpSmsDetail->scp_message,
                                            'sct_sms_cost' => $gpSmsDetail->scp_sms_cost,
                                            'operator_id' => $gpSmsDetail->operator_id,
                                            'sct_campaign_type' => $gpSmsDetail->scp_campaign_type,
                                            'sct_deal_type' => $gpSmsDetail->scp_deal_type,
                                            'sct_sms_type' => $gpSmsDetail->scp_sms_type,
                                            'sct_sms_id' => $mnoTxnId,
                                            'sct_sms_text_type' => $gpSmsDetail->scp_sms_text_type,
                                            'sct_target_time' => $gpSmsDetail->scp_target_time,
                                            'created_at' => $gpSmsDetail->created_at,
                                            'updated_at' => $gpSmsDetail->updated_at,
                                            'sct_delivery_report' => 'DELIVERED',
                                            'sct_status' => $mnoResponseCode,
                                        );
                                        $countTSms++;
                                        
                                        try {
                                            SmsCampaign_24h::insert($gpDataForInsert);
                                            $gpDataForInsert = [];

                                            SmsCamPending::where('id', $smsId)->delete();

                                            $returnData['gp'] = "Working...";

                                        } catch (\Exception $e) {
                                            $returnError['gpError'] = "Something went wrong!" . $e->getMessage();
                                        }
                                    }
                                    else {
                                        $returnError['gpError'] = "No Condition Matched - Something went wrong";
                                    }
                                }
                            }
                            }catch (\Exception $e) {
                                $returnError['gpError'] = "Something went wrong" . $e->getMessage();
                            }

                            // ========================
                            // ====Banglalink START====
                            // ========================
                            
                            $blSmsOfThisCampaigns = SmsCamPending::where([
                                'campaign_id' => $smsCamp->campaign_id,
                                'scp_campaign_status' => 1,
                                'scp_message' => $smsCamp->scp_message,
                                'sender_id' => $smsCamp->sender_id,
                                'operator_id' => '2'
                            ])
                            ->orderBy('id', 'desc')
                            ->take($limitSms)
                            ->get();

                            if (count($blSmsOfThisCampaigns) > 0) {

                                foreach ($blSmsOfThisCampaigns as $blSmsDetails) {
                                    $countTSms = 0;
                                    $blNumbers = [];
                                    $blTransferred = [];

                                    $blNumbers[] = $blSmsDetails->scp_cell_no;
                                    $blTransferred[] = $blSmsDetails->id;

                                    if ($blSmsDetails->scp_sms_text_type == 'unicode') {
                                        $msgType = '3';
                                    } elseif ($blSmsDetails->scp_sms_text_type == 'text') {
                                        $msgType = '1';
                                    } else {
                                        $msgType = '1';
                                    }

                                    $xml_response = \SmsHelper::bl_masking_infozillion_sms(
                                        $virtualNumber->banglalink_virtual_number->sivn_api_user_name,
                                        $virtualNumber->banglalink_virtual_number->sivn_api_password,
                                        $smsCamp->scp_message,
                                        $blNumbers,
                                        $smsCamp->sender->sir_sender_id,
                                        $virtualNumber->banglalink_virtual_number->sivn_number,
                                        $apiCentral,
                                        $msgType,
                                        $transType
                                    );

                                    $responseCode = $xml_response['serverResponseCode'] ?? null;
                                    $mnoTxnId = $xml_response['mnoTxnId'] ?? null;
                                    $mnoResponseCode = $xml_response['mnoResponseCode'] ?? null;
                                    $mnoResponseMessage = $xml_response['mnoResponseMessage'] ?? null;

                                    if ($responseCode == '9001') {
                                        $returnError['blError'] = "Required field missing!";
                                    } elseif ($responseCode == '9002') {
                                        $returnError['blError'] = "Client credentials mismatch!";
                                    } elseif ($responseCode == '9003') {
                                        $returnError['blError'] = "Out of balance!";
                                    } elseif ($responseCode == '9004') {
                                        $returnError['blError'] = "Insufficient balance!";
                                    } elseif ($responseCode == '9005') {
                                        $returnError['blError'] = "Client not found!";
                                    } elseif ($responseCode == '9006') {
                                        $returnError['blError'] = "Invalid source IP!";
                                    } elseif ($responseCode == '9007') {
                                        $returnError['blError'] = "Invalid bill MSISDN!";
                                    } elseif ($responseCode == '9008') {
                                        $returnError['blError'] = "Invalid CLI!";
                                    } elseif ($responseCode == '9009') {
                                        $returnError['blError'] = "Missing destination MSISDN!";
                                    } elseif ($responseCode == '9010') {
                                        $returnError['blError'] = "Max limit for destination MSISDN exceeds!";
                                    } elseif ($responseCode == '9011') {
                                        $returnError['blError'] = "MNO server failure!";
                                    } elseif ($responseCode == '9012') {
                                        $returnError['blError'] = "Dipping failure!";
                                    } elseif ($responseCode == '9014') {
                                        $returnError['blError'] = "Invalid keyword!";
                                    } elseif ($responseCode == '9015') {
                                        $returnError['blError'] = "DND server failure!";
                                    } elseif ($responseCode == '9016') {
                                        $returnError['blError'] = "Invalid check delivery request!";
                                    } elseif ($responseCode == '9019') {
                                        $returnError['blError'] = "Invalid transaction type!";
                                    } elseif ($responseCode == '9020') {
                                        $returnError['blError'] = "Invalid message type!";
                                    } elseif ($responseCode == '9099') {
                                        $returnError['blError'] = "Server failure!";
                                    } elseif ($xml_response == '0150') {
                                        $returnError['blError'] = "Something was missing";
                                    } elseif ($xml_response == '0160') {
                                        $returnError['blError'] = "Something Went Wrong to call banglalink masking API";
                                    } elseif ($responseCode == '9000' && in_array($mnoResponseCode, $invalidResponse)) {
                                        $returnError['blError'] = $mnoResponseMessage;
                                    } elseif ($responseCode == '9000' && $mnoResponseCode == '1000') {
                                        $blDataForInsert = [];

                                        $smsId = $blTransferred[$countTSms];
                                        $checkedSms = SmsCamPending::where('id', $smsId)->first();

                                        $blDataForInsert = [
                                            'user_id' => $checkedSms->user_id,
                                            'sender_id' => $checkedSms->sender_id,
                                            'campaign_id' => $checkedSms->campaign_id,
                                            'sct_cell_no' => $checkedSms->scp_cell_no,
                                            'sct_message' => $checkedSms->scp_message,
                                            'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                            'operator_id' => $checkedSms->operator_id,
                                            'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                            'sct_deal_type' => $checkedSms->scp_deal_type,
                                            'sct_sms_type' => $checkedSms->scp_sms_type,
                                            'sct_sms_id' => $mnoTxnId,
                                            'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                            'sct_target_time' => $checkedSms->scp_target_time,
                                            'created_at' => $checkedSms->created_at,
                                            'updated_at' => $checkedSms->updated_at,
                                            'sct_delivery_report' => 'DELIVERED',
                                            'sct_status' => $mnoResponseCode,
                                        ];
                                        $countTSms++;

                                        try {
                                            SmsCampaign_24h::insert($blDataForInsert);
                                            $blDataForInsert = [];

                                            SmsCamPending::where('id', $smsId)->delete();

                                            $returnData['banglalink'] = "Working...";
                                        } catch (\Exception $e) {
                                            $returnError['blError'] = "Something went wrong!" . $e->getMessage();
                                        }
                                    }else {
                                        $returnError['blError'] = "No Condition Matched - Something went wrong";
                                    }
                                }
                            }

                            // =======================
                            // ===Robi/Airtel START===
                            // =======================

                            $robiAirtelSmsOfThisCampaigns = SmsCamPending::where([
                            'campaign_id' => $smsCamp->campaign_id,
                            'scp_message' => $smsCamp->scp_message,
                            'scp_campaign_status' => 1,
                            'sender_id' => $smsCamp->sender_id])
                            ->whereIn('operator_id', ['1', '4'])
                            ->orderBy('id', 'desc')
                            ->take($limitSms)
                            ->get();

                            if (count($robiAirtelSmsOfThisCampaigns) > 0) {

                                foreach ($robiAirtelSmsOfThisCampaigns as $raSmsDetails) {
                                    $countTSms = 0;
                                    $raNumbers = [];
                                    $raTransferred = [];

                                    $raNumbers[] = $raSmsDetails->scp_cell_no;
                                    $raTransferred[] = $raSmsDetails->id;

                                    if ($raSmsDetails->scp_sms_text_type= 'unicode') {
                                        $msgType = '3';
                                    }elseif ($raSmsDetails->scp_sms_text_type= 'text') {
                                        $msgType = '1';
                                    }else {
                                        $msgType = '1';
                                    }

                                    $xml_response = \SmsHelper::ra_masking_infozillion_sms(
                                        $virtualNumber->robi_virtual_number->sivn_api_user_name,
                                        $virtualNumber->robi_virtual_number->sivn_api_password,
                                        $smsCamp->scp_message,
                                        $raNumbers,
                                        $smsCamp->sender->sir_sender_id,
                                        $virtualNumber->robi_virtual_number->sivn_number,
                                        $apiCentral,
                                        $msgType,
                                        $transType
                                    );

                                    $responseCode = $xml_response['serverResponseCode'] ?? null;
                                    $mnoTxnId = $xml_response['mnoTxnId'] ?? null;
                                    $mnoResponseCode = $xml_response['mnoResponseCode'] ?? null;
                                    $mnoResponseMessage = $xml_response['mnoResponseMessage'] ?? null;

                                    if ($responseCode == '9001') {
                                        $returnError['robiAirtelError'] = "Required field missing!";
                                    } elseif ($responseCode == '9002') {
                                        $returnError['robiAirtelError'] = "Client credentials mismatch!";
                                    } elseif ($responseCode == '9003') {
                                        $returnError['robiAirtelError'] = "Out of balance!";
                                    } elseif ($responseCode == '9004') {
                                        $returnError['robiAirtelError'] = "Insufficient balance!";
                                    } elseif ($responseCode == '9005') {
                                        $returnError['robiAirtelError'] = "Client not found!";
                                    } elseif ($responseCode == '9006') {
                                        $returnError['robiAirtelError'] = "Invalid source IP!";
                                    } elseif ($responseCode == '9007') {
                                        $returnError['robiAirtelError'] = "Invalid bill MSISDN!";
                                    } elseif ($responseCode == '9008') {
                                        $returnError['robiAirtelError'] = "Invalid CLI!";
                                    } elseif ($responseCode == '9009') {
                                        $returnError['robiAirtelError'] = "Missing destination MSISDN!";
                                    } elseif ($responseCode == '9010') {
                                        $returnError['robiAirtelError'] = "Max limit for destination MSISDN exceeds!";
                                    } elseif ($responseCode == '9011') {
                                        $returnError['robiAirtelError'] = "MNO server failure!";
                                    } elseif ($responseCode == '9012') {
                                        $returnError['robiAirtelError'] = "Dipping failure!";
                                    } elseif ($responseCode == '9014') {
                                        $returnError['robiAirtelError'] = "Invalid keyword!";
                                    } elseif ($responseCode == '9015') {
                                        $returnError['robiAirtelError'] = "DND server failure!";
                                    } elseif ($responseCode == '9016') {
                                        $returnError['robiAirtelError'] = "Invalid check delivery request!";
                                    } elseif ($responseCode == '9019') {
                                        $returnError['robiAirtelError'] = "Invalid transaction type!";
                                    } elseif ($responseCode == '9020') {
                                        $returnError['robiAirtelError'] = "Invalid message type!";
                                    } elseif ($responseCode == '9099') {
                                        $returnError['robiAirtelError'] = "Server failure!";
                                    } elseif ($xml_response == '0150') {
                                        $returnError['robiAirtelError'] = "Something was missing";
                                    } elseif ($xml_response == '0160') {
                                        $returnError['robiAirtelError'] = "Something Went Wrong to call robi masking api";
                                    } elseif ($responseCode == '9000' && in_array($mnoResponseCode, $invalidResponse)) {
                                        $returnError['robiAirtelError'] = $mnoResponseMessage;
                                    } elseif ($responseCode == '9000' && $mnoResponseCode == '1000') {
                                        $raDataForInsert = array();

                                        $smsId = $raTransferred[$countTSms];
                                        $checkedSms = SmsCamPending::where('id', $smsId)->first();

                                        $raDataForInsert = [
                                            'user_id' => $checkedSms->user_id,
                                            'sender_id' => $checkedSms->sender_id,
                                            'campaign_id' => $checkedSms->campaign_id,
                                            'sct_cell_no' => $checkedSms->scp_cell_no,
                                            'sct_message' => $checkedSms->scp_message,
                                            'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                            'operator_id' => $checkedSms->operator_id,
                                            'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                            'sct_deal_type' => $checkedSms->scp_deal_type,
                                            'sct_sms_type' => $checkedSms->scp_sms_type,
                                            'sct_sms_id' => $mnoTxnId,
                                            'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                            'sct_target_time' => $checkedSms->scp_target_time,
                                            'created_at' => $checkedSms->created_at,
                                            'updated_at' => $checkedSms->updated_at,
                                            'sct_delivery_report' => 'DELIVERED',
                                            'sct_status' => $mnoResponseCode,
                                        ];
                                        $countTSms++;
                                        
                                        try {
                                            SmsCampaign_24h::insert($raDataForInsert);
                                            $raDataForInsert = [];
                                            SmsCamPending::where('id', $smsId)->delete();
                                            $returnData['robi_airtel'] = "Working...";
                                        } catch (\Exception $e) {
                                            $returnError['robiAirtelError'] = "Something went wrong!" . $e->getMessage();
                                        }
                                    } else {
                                        $retText = "No Condition Matched - Something went wrong";
                                    }
                                }
                            }

                            // ======================
                            // ====Teletalk START====
                            // ======================
                            $teletalkSmsOfThisCampaigns = SmsCamPending::where([
                            'campaign_id' => $maskingSmsCampaign->campaign_id,
                            'scp_message' => $maskingSmsCampaign->scp_message,
                            'scp_campaign_status' => 1,
                            'sender_id' => $maskingSmsCampaign->sender_id,
                            'operator_id' => '5'])
                            ->orderBy('id', 'desc')
                            ->take($limitSms)
                            ->get();

                            if (count($teletalkSmsOfThisCampaigns) > 0) {
                            foreach ($teletalkSmsOfThisCampaigns as $ttSmsDetails) {
                                $ttSmsDetails->scp_cell_no;
                                $ttTransferred[] = $ttSmsDetails->id;
                                $countTSms = 0;

                                $senderIdDetails = SenderIdRegister::find($ttSmsDetails->sender_id);

                                $xml_response = \SmsHelper::send_masking_teletalk_sms(
                                    $senderIdDetails->sir_teletalk_user_name,
                                    $senderIdDetails->sir_teletalk_user_password,
                                    $maskingSmsCampaign->scp_message,
                                    $ttSmsDetails->scp_cell_no,
                                    $maskingSmsCampaign->sender->sir_sender_id);
                                
                                if ($xml_response == '0150') {
                                    $returnError['ttError'] = "something was missing for teletalk masking sms";

                                    $sms_ret_id = "";
                                    $sms_status = "SOMETHING MISSING";
                                    $sms_report = "PENDING";

                                    $ttDataForInsert = array();
                                    $ttDataForInsert[] = array(
                                        'user_id' => $ttSmsDetails->user_id,
                                        'sender_id' => $ttSmsDetails->sender_id,
                                        'campaign_id' => $ttSmsDetails->campaign_id,
                                        'sct_cell_no' => $ttSmsDetails->scp_cell_no,
                                        'sct_message' => $ttSmsDetails->scp_message,
                                        'sct_sms_cost' => $ttSmsDetails->scp_sms_cost,
                                        'operator_id' => $ttSmsDetails->operator_id,
                                        'sct_campaign_type' => $ttSmsDetails->scp_campaign_type,
                                        'sct_deal_type' => $ttSmsDetails->scp_deal_type,
                                        'sct_sms_type' => $ttSmsDetails->scp_sms_type,
                                        'sct_sms_id' => $sms_ret_id,
                                        'sct_sms_text_type' => $ttSmsDetails->scp_sms_text_type,
                                        'sct_target_time' => $ttSmsDetails->scp_target_time,
                                        'created_at' => $ttSmsDetails->created_at,
                                        'updated_at' => $ttSmsDetails->updated_at,
                                        'sct_delivery_report' => $sms_report,
                                        'sct_status' => $sms_status
                                    );

                                    $countTSms++;

                                    try {
                                        SmsCampaign_24h::insert($ttDataForInsert);
                                        $ttDataForInsert = array();

                                        SmsCamPending::whereIn('id', $ttTransferred)->delete();
                                        $returnData['teletalk'] = "Teletalk Working...";
                                    } catch (\Exception $e) {
                                        $returnError['ttError'] = "something went wrong" . $e->getMessage();
                                    }

                                } elseif ($xml_response == '0160') {
                                    $returnError['ttError'] = "something went wrong to send teletalk number";
                                } else {

                                    preg_match_all('/>(.*?)</', $xml_response, $matches);
                                    $full_ret_text = $matches[1][0];
                                    $exp_ret_text = explode(',', $full_ret_text);

                                    if ($exp_ret_text[0] == "SUCCESS") {
                                        $exp_sms_id = explode('=', $exp_ret_text[1]);
                                        $sms_ret_id = $exp_sms_id[1];
                                        $sms_status = "SUCCESS";
                                        $sms_report = "PENDING";
                                    } else {
                                        $sms_ret_id = 0;
                                        $sms_status = $exp_ret_text[0];
                                        $sms_report = $exp_ret_text[1];
                                    }

                                    $ttDataForInsert = array();
                                    $ttDataForInsert[] = array(
                                        'user_id' => $ttSmsDetails->user_id,
                                        'sender_id' => $ttSmsDetails->sender_id,
                                        'campaign_id' => $ttSmsDetails->campaign_id,
                                        'sct_cell_no' => $ttSmsDetails->scp_cell_no,
                                        'sct_message' => $ttSmsDetails->scp_message,
                                        'sct_sms_cost' => $ttSmsDetails->scp_sms_cost,
                                        'operator_id' => $ttSmsDetails->operator_id,
                                        'sct_campaign_type' => $ttSmsDetails->scp_campaign_type,
                                        'sct_deal_type' => $ttSmsDetails->scp_deal_type,
                                        'sct_sms_type' => $ttSmsDetails->scp_sms_type,
                                        'sct_sms_id' => $sms_ret_id,
                                        'sct_sms_text_type' => $ttSmsDetails->scp_sms_text_type,
                                        'sct_target_time' => $ttSmsDetails->scp_target_time,
                                        'created_at' => $ttSmsDetails->created_at,
                                        'updated_at' => $ttSmsDetails->updated_at,
                                        'sct_delivery_report' => $sms_report,
                                        'sct_status' => $sms_status
                                    );

                                    $countTSms++;

                                    try {
                                        SmsCampaign_24h::insert($ttDataForInsert);
                                        $ttDataForInsert = array();

                                        SmsCamPending::whereIn('id', $ttTransferred)->delete();
                                        $returnData['teletalk'] = "Teletalk Working...";
                                    } catch (\Exception $e) {
                                        $returnError['ttError'] = "something went wrong" . $e->getMessage();
                                    }

                                }
                            }
                        }
                    }
                }
            } else {
                $returnData['message'] = "No Content Found";
            }
        }
    }
}