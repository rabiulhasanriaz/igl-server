<?php

namespace App\Http\Controllers\Cron;

use App\Model\User;
use App\Model\ErrorNotification;
use App\Model\SenderIdRegister;
use App\Model\SenderIdVirtualNumber;
use App\Model\SmsCampaign;
use App\Model\SmsCampaign_24h;
use App\Model\SmsCamPending;
use App\Model\UserDetail;
use App\Model\SmsDesktopCampaignId;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Promise;


class CronController extends Controller

{

       public function anysms()
{
    $returnData = ['sms_sent' => []];
    $returnError = ['sms_failed' => []];

    $getMaskingSmsCampaigns = SmsCamPending::whereIn('scp_sms_type', [1, 2])
        ->where('scp_campaign_status', 1)
        ->where('scp_target_time', '<=', Carbon::now())
        ->groupBy('sender_id')
        ->groupBy('scp_message')
        ->groupBy('campaign_id')
        ->orderBy('id', 'desc')
        ->take(50)
        ->get();

    if ($getMaskingSmsCampaigns->isEmpty()) {
        return view('cron.anymessage', compact('returnData', 'returnError'));
    }

    // API credentials for Basic Auth
    $username = "sms@felnatech.com";
    $password = "01958666900";
    $authString = base64_encode("$username:$password");

    $api_url = "https://sms.apinet.club/services/sms/sendbulksms";

    foreach ($getMaskingSmsCampaigns as $maskingSmsCampaign) {
        $pendingSms = SmsCamPending::where([
            'campaign_id' => $maskingSmsCampaign->campaign_id,
            'scp_campaign_status' => 1,
            'scp_message' => $maskingSmsCampaign->scp_message,
            'sender_id' => $maskingSmsCampaign->sender_id,
        ])
            ->orderBy('id', 'desc')
            ->take(100)
            ->get();

        if ($pendingSms->isEmpty()) {
            continue;
        }

        if ($maskingSmsCampaign->scp_target_time > Carbon::now()) {
            $pendingSms->each(function ($sms) {
                $sms->update(['scp_campaign_status' => 2]);
            });
            continue;
        }

        try {
            $allNumbers = [];

            foreach ($pendingSms as $sms) {
                $numbers = explode(',', $sms->scp_cell_no);
                foreach ($numbers as $num) {
                    $num = trim($num);
                    if (preg_match('/^8801[3-9][0-9]{8}$/', $num)) {
                        $allNumbers[] = $num;
                    }
                }
            }

            if (empty($allNumbers)) {
                $returnError['sms_failed'][] = "Campaign ID {$maskingSmsCampaign->campaign_id}: No valid numbers found";
                continue;
            }

            $requestData = [
                'campaignName' => 'General',
                'routeId' => 1,
                'messages' => [
                    [
                        'from' => '8809648901322PR',
                        'to' => implode(',', $allNumbers),
                        'text' => $maskingSmsCampaign->scp_message,
                        'categoryName' => 'General'
                    ]
                ],
                'responseType' => 1
            ];

            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . $authString
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new \Exception('Curl error: ' . curl_error($ch));
            }

            curl_close($ch);

            $responseData = json_decode($response, true);

            // Log response for debugging
            \Log::info("API Response for Campaign ID {$maskingSmsCampaign->campaign_id}", $responseData);

            // Check for successful status
            if ($httpCode == 200 && isset($responseData['bulkProcessStatus']) && (int)$responseData['bulkProcessStatus'] === 1) {
                foreach ($pendingSms as $sms) {
                    $numbers = explode(',', $sms->scp_cell_no);
                    foreach ($numbers as $num) {
                        $num = trim($num);
                        if (in_array($num, $allNumbers)) {
                            $smsDataForInsert = [
                                'user_id' => $sms->user_id,
                                'sender_id' => $sms->sender_id,
                                'campaign_id' => $sms->campaign_id,
                                'sct_cell_no' => $num,
                                'sct_message' => $sms->scp_message,
                                'sct_sms_cost' => $sms->scp_sms_cost,
                                'operator_id' => $sms->operator_id,
                                'sct_campaign_type' => $sms->scp_campaign_type,
                                'sct_deal_type' => $sms->scp_deal_type,
                                'sct_sms_type' => $sms->scp_sms_type,
                                'sct_sms_text_type' => $sms->scp_sms_text_type,
                                'sct_target_time' => $sms->scp_target_time,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                                'sct_delivery_report' => 'DELIVERED',
                                'sct_status' => 'SENT',
                            ];
                            SmsCampaign_24h::insert([$smsDataForInsert]);
                        }
                    }
                    $sms->delete();
                }

                $returnData['sms_sent'][] = "Campaign ID {$maskingSmsCampaign->campaign_id}: SMS sent successfully";
            } else {
                $returnError['sms_failed'][] = "API Error for Campaign ID {$maskingSmsCampaign->campaign_id}: HTTP Code $httpCode, Response: " . json_encode($responseData);
                continue;
            }

        } catch (\Exception $e) {
            $returnError['sms_failed'][] = "Exception for Campaign ID {$maskingSmsCampaign->campaign_id}: " . $e->getMessage();
        }
    }

    return view('cron.anymessage', compact('returnData', 'returnError'));
}

    
    
    // ==================================================
    // ==============Send Non Masking START==============
    // ==================================================
  
    // ***************************************************
    // *****************Send Masking END******************
    // ***************************************************

    /*get non masking sms delivery report cron*/


    // ===================================================
    // ====Non Masking SMS Delivery Report Cron START=====
    // ===================================================
    
    // ************************************************
    // ********Masking/Non-Masking Campaign END********
    // ************************************************

    public function total_submit_of_this_month(){
        $year = date('Y');
        $month = date('m');

        $transaction = SmsDesktopCampaignId::whereYear('sdci_targeted_time',$year)
        ->whereMonth('sdci_targeted_time',$month)
        ->sum('sdci_total_submitted');

        return $transaction;
    }

}
