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
use App\Model\SmsLanding;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class ProcessMaskingSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_DELIVERED = 1;
    const STATUS_FAILED = 2;

    public function __construct()
    {
        $this->queue = 'masking';
    }

    public function handle()
    {
        $invalidResponse = ['1001', '1002', '1003', '1004', '1005', '1006', '1007', '1008', '1009', '1010', '1011', '1012', '1013', '1014', '1015', '1016', '1017', '1018', '1019', '1020', '1050', '1051', '1052', '1053', '1054'];
        
        $apiCentral = 'myQ1uzu3mRVWdjVq4A1mV5GscebslZ4y';
        $transType = 'T';

        // Get ALL pending masking SMS (status = 0) - NO LIMITS
        $getMaskingSmsCampaigns = SmsLanding::where('scp_sms_type', '2')
            ->where('scp_target_time', '<=', Carbon::now())
            ->where('scp_campaign_status', 1)
            ->where('scp_deal_type', 1)
            ->where('scp_status', self::STATUS_PENDING)
            ->groupBy('sender_id')
            ->groupBy('scp_message')
            ->groupBy('campaign_id')
            ->orderBy('id', 'asc')
            ->get();

        if (count($getMaskingSmsCampaigns) > 0) {
            foreach ($getMaskingSmsCampaigns as $maskingSmsCampaign) {
                // Get ALL SMS for this campaign (status = 0) - NO LIMITS
                $getSmsList = SmsLanding::where([
                    'campaign_id' => $maskingSmsCampaign->campaign_id,
                    'scp_campaign_status' => 1,
                    'scp_message' => $maskingSmsCampaign->scp_message,
                    'sender_id' => $maskingSmsCampaign->sender_id,
                    'scp_sms_type' => '2',
                    'scp_status' => self::STATUS_PENDING
                ])->get();

                if (count($getSmsList) > 0) {
                    // Get virtual number for this sender
                    $virtualNumber = SenderIdRegister::with('robi_virtual_number', 'airtel_virtual_number', 'banglalink_virtual_number', 'teletalk_virtual_number', 'gp_virtual_number')
                        ->where('id', $maskingSmsCampaign->sender_id)
                        ->first();

                    // Group SMS by operator for batch processing
                    $gpNumbers = [];
                    $gpIds = [];
                    $blNumbers = [];
                    $blIds = [];
                    $raNumbers = [];
                    $raIds = [];
                    $ttNumbers = [];
                    $ttIds = [];

                    foreach ($getSmsList as $sms) {
                        switch ($sms->operator_id) {
                            case '3': // GP
                                $gpNumbers[] = $sms->scp_cell_no;
                                $gpIds[] = $sms->id;
                                break;
                            case '2': // Banglalink
                                $blNumbers[] = $sms->scp_cell_no;
                                $blIds[] = $sms->id;
                                break;
                            case '1': // Robi
                            case '4': // Airtel
                                $raNumbers[] = $sms->scp_cell_no;
                                $raIds[] = $sms->id;
                                break;
                            case '5': // Teletalk
                                $ttNumbers[] = $sms->scp_cell_no;
                                $ttIds[] = $sms->id;
                                break;
                        }
                    }

                    $messageType = $getSmsList->first()->scp_sms_text_type;
                    $msgType = ($messageType == 'unicode') ? '3' : '1';

                    // Process each operator in batch
                    if (!empty($gpNumbers)) {
                        $this->processBatchGP($maskingSmsCampaign, $gpNumbers, $gpIds, $virtualNumber, $msgType, $apiCentral, $transType, $invalidResponse);
                    }

                    if (!empty($blNumbers)) {
                        $this->processBatchBanglalink($maskingSmsCampaign, $blNumbers, $blIds, $virtualNumber, $msgType, $apiCentral, $transType, $invalidResponse);
                    }

                    if (!empty($raNumbers)) {
                        $this->processBatchRobiAirtel($maskingSmsCampaign, $raNumbers, $raIds, $virtualNumber, $msgType, $apiCentral, $transType, $invalidResponse);
                    }

                    if (!empty($ttNumbers)) {
                        $this->processBatchTeletalk($maskingSmsCampaign, $ttNumbers, $ttIds);
                    }
                }
            }
        }
    }

    private function processBatchGP($campaign, $numbers, $smsIds, $virtualNumber, $msgType, $apiCentral, $transType, $invalidResponse)
    {
        try {
            $user_name = $virtualNumber->gp_virtual_number->sivn_api_user_name ?? null;
            $billMsi = $virtualNumber->gp_virtual_number->sivn_number ?? null;
            $password = $virtualNumber->gp_virtual_number->sivn_api_password ?? null;
            $sender = $campaign->sender->sir_sender_id;
            $sms_text = $campaign->scp_message;
            
            if (!$user_name || !$password) {
                $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, 'Missing credentials');
                return;
            }
            
            // Send ALL numbers in ONE API call
            $xml_response = \SmsHelper::gp_masking_infozillion_sms(
                $user_name, 
                $password, 
                $sms_text, 
                $numbers,  // All numbers at once
                $sender, 
                $billMsi, 
                $apiCentral, 
                $transType, 
                $msgType
            );

            $responseCode = $xml_response['serverResponseCode'] ?? null;
            $mnoTxnId = $xml_response['mnoTxnId'] ?? null;
            $mnoResponseCode = $xml_response['mnoResponseCode'] ?? null;

            if ($mnoResponseCode == '1008') {
                $this->handleAutoLoad($billMsi);
            }

            if ($responseCode == '9000' && $mnoResponseCode == '1000') {
                $this->batchUpdateStatus($smsIds, self::STATUS_DELIVERED, $mnoTxnId);
            } else {
                $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, json_encode($xml_response));
            }
        } catch (\Exception $e) {
            $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, $e->getMessage());
            \Log::error('GP Masking Batch Error: ' . $e->getMessage());
        }
    }

    private function processBatchBanglalink($campaign, $numbers, $smsIds, $virtualNumber, $msgType, $apiCentral, $transType, $invalidResponse)
    {
        try {
            $user_name = $virtualNumber->banglalink_virtual_number->sivn_api_user_name ?? null;
            $password = $virtualNumber->banglalink_virtual_number->sivn_api_password ?? null;
            $billMsi = $virtualNumber->banglalink_virtual_number->sivn_number ?? null;
            
            if (!$user_name || !$password) {
                $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, 'Missing credentials');
                return;
            }

            // Send ALL numbers in ONE API call
            $xml_response = \SmsHelper::bl_masking_infozillion_sms(
                $user_name,
                $password,
                $campaign->scp_message,
                $numbers,  // All numbers at once
                $campaign->sender->sir_sender_id,
                $billMsi,
                $apiCentral,
                $msgType,
                $transType
            );

            $responseCode = $xml_response['serverResponseCode'] ?? null;
            $mnoTxnId = $xml_response['mnoTxnId'] ?? null;
            $mnoResponseCode = $xml_response['mnoResponseCode'] ?? null;

            if ($responseCode == '9000' && $mnoResponseCode == '1000') {
                $this->batchUpdateStatus($smsIds, self::STATUS_DELIVERED, $mnoTxnId);
            } else {
                $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, json_encode($xml_response));
            }
        } catch (\Exception $e) {
            $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, $e->getMessage());
            \Log::error('Banglalink Masking Batch Error: ' . $e->getMessage());
        }
    }

    private function processBatchRobiAirtel($campaign, $numbers, $smsIds, $virtualNumber, $msgType, $apiCentral, $transType, $invalidResponse)
    {
        try {
            $user_name = $virtualNumber->robi_virtual_number->sivn_api_user_name ?? null;
            $password = $virtualNumber->robi_virtual_number->sivn_api_password ?? null;
            $billMsi = $virtualNumber->robi_virtual_number->sivn_number ?? null;
            
            if (!$user_name || !$password) {
                $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, 'Missing credentials');
                return;
            }

            // Send ALL numbers in ONE API call
            $xml_response = \SmsHelper::ra_masking_infozillion_sms(
                $user_name,
                $password,
                $campaign->scp_message,
                $numbers,  // All numbers at once
                $campaign->sender->sir_sender_id,
                $billMsi,
                $apiCentral,
                $msgType,
                $transType
            );

            $responseCode = $xml_response['serverResponseCode'] ?? null;
            $mnoTxnId = $xml_response['mnoTxnId'] ?? null;
            $mnoResponseCode = $xml_response['mnoResponseCode'] ?? null;

            if ($responseCode == '9000' && $mnoResponseCode == '1000') {
                $this->batchUpdateStatus($smsIds, self::STATUS_DELIVERED, $mnoTxnId);
            } else {
                $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, json_encode($xml_response));
            }
        } catch (\Exception $e) {
            $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, $e->getMessage());
            \Log::error('Robi/Airtel Masking Batch Error: ' . $e->getMessage());
        }
    }

    private function processBatchTeletalk($campaign, $numbers, $smsIds)
    {
        try {
            $senderIdDetails = SenderIdRegister::find($campaign->sender_id);
            
            if (!$senderIdDetails) {
                $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, 'Sender not found');
                return;
            }

            $successIds = [];
            $failedIds = [];

            // Teletalk might need individual sending (check if batch is supported)
            foreach ($numbers as $index => $number) {
                $xml_response = \SmsHelper::send_masking_teletalk_sms(
                    $senderIdDetails->sir_teletalk_user_name,
                    $senderIdDetails->sir_teletalk_user_password,
                    $campaign->scp_message,
                    $number,
                    $campaign->sender->sir_sender_id
                );
                
                if ($xml_response == '0150' || $xml_response == '0160') {
                    $failedIds[] = $smsIds[$index];
                } else {
                    preg_match_all('/>(.*?)</', $xml_response, $matches);
                    $full_ret_text = $matches[1][0] ?? '';
                    $exp_ret_text = explode(',', $full_ret_text);

                    if ($exp_ret_text[0] == "SUCCESS") {
                        $successIds[] = $smsIds[$index];
                    } else {
                        $failedIds[] = $smsIds[$index];
                    }
                }
            }

            if (!empty($successIds)) {
                $this->batchUpdateStatus($successIds, self::STATUS_DELIVERED, '0');
            }
            if (!empty($failedIds)) {
                $this->batchUpdateStatus($failedIds, self::STATUS_FAILED, 'Teletalk send failed');
            }
        } catch (\Exception $e) {
            $this->batchUpdateStatus($smsIds, self::STATUS_FAILED, $e->getMessage());
            \Log::error('Teletalk Masking Batch Error: ' . $e->getMessage());
        }
    }

    private function handleAutoLoad($billMsi)
    {
        $check_v_n = "88" . $billMsi;
        $exists_load = SenderIdVirtualNumber::where('sivn_number', $billMsi)
            ->where('last_load_time', '>', Carbon::now()->subMinutes(20))
            ->first();
            
        if (!$exists_load) {
            $load = SenderIdVirtualNumber::where('sivn_number', $billMsi)->first();
            if (!empty($load) && $load->sivn_load_amount >= 10) {
                try {
                    $flexi_api_key = "445015812203613491581220361";
                    $pin = "3799";
                    $amount = $load->sivn_load_amount;
                    $number_type = "2";
                    $operator = "gp";
                    $url = "http://127.0.0.1/api/v1/send-load?api_key=$flexi_api_key&pin=$pin&number=$check_v_n&amount=$amount&number_type=$number_type&operator=$operator";

                    $client = new Client();
                    $res = $client->request('GET', $url);
                    
                    $load->last_load_time = Carbon::now();
                    $load->save();
                } catch (\Exception $e) {
                    \Log::error('Auto Load Problem: ' . $e->getMessage());
                }
            }
        }
    }

    private function batchUpdateStatus($ids, $status, $smsId = null)
    {
        try {
            $updateData = [
                'scp_status' => $status,
                'updated_at' => Carbon::now()
            ];
            
            if ($smsId) {
                $updateData['scp_sms_id'] = $smsId;
            }
            
            SmsLanding::whereIn('id', $ids)->update($updateData);
            
            \Log::info('Batch updated ' . count($ids) . ' masking SMS to status: ' . $status);
        } catch (\Exception $e) {
            \Log::error('Batch update status error: ' . $e->getMessage());
        }
    }
}
