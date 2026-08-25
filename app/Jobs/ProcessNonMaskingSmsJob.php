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

class ProcessNonMaskingSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_DELIVERED = 1;
    const STATUS_FAILED = 2;

    public function __construct()
    {
        $this->queue = 'non-masking';
    }

    public function handle()
    {
        $invalidResponse = ['1001', '1002', '1003', '1004', '1005', '1006', '1007', '1008', '1009', '1010', '1011', '1012', '1013', '1014', '1015', '1016', '1017', '1018', '1019', '1020', '1050', '1051', '1052', '1053', '1054'];
        
        $apiCentral = 'myQ1uzu3mRVWdjVq4A1mV5GscebslZ4y';
        $transType = 'T';

        // Get ALL pending non-masking SMS (status = 0)
        $getNonMaskingSmsCampaigns = SmsLanding::where('scp_sms_type', '1')
            ->where('scp_target_time', '<=', Carbon::now())
            ->where('scp_campaign_status', 1)
            ->where('scp_status', self::STATUS_PENDING)
            ->groupBy('sender_id')
            ->groupBy('scp_message')
            ->orderBy('id', 'asc')
            ->get();

        if (count($getNonMaskingSmsCampaigns) > 0) {
            foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {
                // Get ALL SMS for this campaign (status = 0)
                $getSmsList = SmsLanding::where([
                    'campaign_id' => $nonMaskingSmsCampaign->campaign_id,
                    'scp_campaign_status' => 1,
                    'scp_message' => $nonMaskingSmsCampaign->scp_message,
                    'sender_id' => $nonMaskingSmsCampaign->sender_id,
                    'scp_sms_type' => '1',
                    'scp_status' => self::STATUS_PENDING
                ])->get();
                    
                foreach ($getSmsList as $smsDetails) {
                    $sender_id = $nonMaskingSmsCampaign->sender->sir_sender_id;
                    $numbers = [$smsDetails->scp_cell_no];
                    
                    if ($smsDetails->scp_sms_text_type == 'unicode') {
                        $msgType = '3';
                    } else {
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

                    if (substr($sender_id, 0, 3) == '018') {
                        $this->processRobiAirtel($nonMaskingSmsCampaign, $smsDetails, $numbers, $msgType, $apiCentral, $transType, $invalidResponse);
                    }
                    elseif ($matchedOperator) {
                        $this->processVirtualNumberOperator($nonMaskingSmsCampaign, $smsDetails, $numbers, $matchedOperator, $msgType, $apiCentral, $transType, $invalidResponse);
                    }
                    elseif ((substr($sender_id, 0, 5) == '017') || (substr($sender_id, 0, 5) == '013')) {
                        $this->processGP($nonMaskingSmsCampaign, $smsDetails, $numbers, $msgType, $invalidResponse);
                    }
                    elseif ((substr($sender_id, 0, 5) == '019') || (substr($sender_id, 0, 5) == '014')) {
                        $this->processBanglalink($nonMaskingSmsCampaign, $smsDetails, $numbers);
                    }
                }
            }
        }
    }

    private function processRobiAirtel($campaign, $smsDetails, $numbers, $msgType, $apiCentral, $transType, $invalidResponse)
    {
        $sender_id = $campaign->sender->sir_sender_id;
        $senderIdDet = SenderIdRegister::with('robi_virtual_number')->where('sir_sender_id', $sender_id)->first();
        
        if (empty($senderIdDet)) {
            $this->updateStatus($smsDetails->id, self::STATUS_FAILED, 'Sender ID not found');
            return;
        }

        $xml_response = \SmsHelper::ra_non_masking_iptsp_sms('felna', 'Felna123', $campaign->scp_message, $numbers, '8809648903075', '8809648903075', $apiCentral, $transType, $msgType);

        $responseCode = $xml_response['serverResponseCode'] ?? null;
        $mnoTxnId = $xml_response['mnoTxnId'] ?? null;
        $mnoResponseCode = $xml_response['mnoResponseCode'] ?? null;

        if ($responseCode == '9000' && $mnoResponseCode == '1000') {
            $this->updateStatus($smsDetails->id, self::STATUS_DELIVERED, $mnoTxnId);
        } else {
            $this->updateStatus($smsDetails->id, self::STATUS_FAILED, json_encode($xml_response));
        }
    }

    private function processVirtualNumberOperator($campaign, $smsDetails, $numbers, $matchedOperator, $msgType, $apiCentral, $transType, $invalidResponse)
    {
        $sender_id = $campaign->sender->sir_sender_id;
        $virtualNumber = SenderIdVirtualNumber::where('operator_id', $matchedOperator->id)
            ->where('sivn_status', 1)
            ->first();

        if (!$virtualNumber) {
            $this->updateStatus($smsDetails->id, self::STATUS_FAILED, 'No virtual number found');
            return;
        }

        $xml_response = \SmsHelper::ra_non_masking_iptsp_sms(
            $virtualNumber->sivn_api_user_name,
            $virtualNumber->sivn_api_password,
            $campaign->scp_message,
            $numbers,
            $sender_id,
            $sender_id,
            $apiCentral,
            $transType,
            $msgType
        );

        $responseCode = $xml_response['serverResponseCode'] ?? null;
        $mnoTxnId = $xml_response['mnoTxnId'] ?? null;
        $mnoResponseCode = $xml_response['mnoResponseCode'] ?? null;

        if ($responseCode == '9000' && $mnoResponseCode == '1000') {
            $this->updateStatus($smsDetails->id, self::STATUS_DELIVERED, $mnoTxnId);
        } else {
            $this->updateStatus($smsDetails->id, self::STATUS_FAILED, json_encode($xml_response));
        }
    }

    private function processGP($campaign, $smsDetails, $numbers, $msgType, $invalidResponse)
    {
        $sender_id = $campaign->sender->sir_sender_id;
        $senderIdDet = SenderIdRegister::with('gp_virtual_number')->where('sir_sender_id', $sender_id)->first();
        
        if (empty($senderIdDet)) {
            $this->updateStatus($smsDetails->id, self::STATUS_FAILED, 'GP Sender ID not found');
            return;
        }

        $user_name = "IGLWAdmin";
        $password = "qazXSW11!!";
        $xml_response = \SmsHelper::update_send_masking_gp_sms($user_name, $password, $sender_id, $campaign->scp_message, $numbers);
        
        $response_array = explode("\n", $xml_response);
        
        foreach ($response_array as $oneReport) {
            $report_details = explode(',', $oneReport);
            $aaa = isset($report_details[2]) ? $report_details[2] : ($report_details[1] ?? null);
            
            if ($aaa == "TPS limit Exceeded" || !isset($report_details[1])) {
                continue;
            }
            
            $mnoTxnId = isset($report_details[2]) ? $report_details[2] : $report_details[1];
            $status = isset($report_details[2]) ? $report_details[1] : $report_details[0];
            
            if ($status == '1000' || $status == 'SUCCESS') {
                $this->updateStatus($smsDetails->id, self::STATUS_DELIVERED, $mnoTxnId);
            } else {
                $this->updateStatus($smsDetails->id, self::STATUS_FAILED, $status);
            }
        }
    }

    private function processBanglalink($campaign, $smsDetails, $numbers)
    {
        $sender_id = $campaign->sender->sir_sender_id;
        $senderIdDet = SenderIdRegister::with('banglalink_virtual_number')->where('sir_sender_id', $sender_id)->first();
        
        if (empty($senderIdDet)) {
            $this->updateStatus($smsDetails->id, self::STATUS_FAILED, 'Banglalink Sender ID not found');
            return;
        }

        $user_name = "igl";
        $password = "397c6678b1ca033edbeba622573ed000";
        $xml_response = \SmsHelper::send_masking_banglalink_sms($user_name, $password, $campaign->scp_message, $numbers, $sender_id);
        
        $this->updateStatus($smsDetails->id, self::STATUS_DELIVERED, '0');
    }

    private function updateStatus($id, $status, $smsId = null)
    {
        try {
            $updateData = [
                'scp_status' => $status,
                'updated_at' => Carbon::now()
            ];
            
            if ($smsId) {
                $updateData['scp_sms_id'] = $smsId;
            }
            
            SmsLanding::where('id', $id)->update($updateData);
        } catch (\Exception $e) {
            \Log::error('Update status error: ' . $e->getMessage());
        }
    }
}
