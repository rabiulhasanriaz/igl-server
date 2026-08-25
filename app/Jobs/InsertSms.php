<?php

namespace App\Jobs;

use App\Model\AccSmsBalance;
use App\Model\AccSmsRate;
use App\Model\AccUserCreditHistory;
use App\Model\Operator;
use App\Model\SmsCampaignId;
use App\Model\SmsCamPending;
use App\Model\SystemConfiguration;
use App\Model\User;
use App\Model\UserDetail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class InsertSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $isMasking;
    protected $request;
    protected $validUniqueNumbers;
    protected $total_cost;
    protected $target_time;
    protected $sms_number;
    protected $smsType;
    protected $authUser;
    protected $campaign_ids_id;
    private $campaign_permission;

    public function __construct($isMasking, $request, $validUniqueNumbers, $total_cost, $target_time, $sms_number, $smsType, $authUser, $campaign_ids_id)
    {
        $this->isMasking = $isMasking;
        $this->request = $request;
        $this->validUniqueNumbers = $validUniqueNumbers;
        $this->total_cost = $total_cost;
        $this->target_time = $target_time;
        $this->sms_number = $sms_number;
        $this->smsType = $smsType;
        $this->authUser = $authUser;
        $this->campaign_ids_id = $campaign_ids_id;

        $system_configuration = SystemConfiguration::first();
        $this->campaign_permission = !empty($system_configuration) && $system_configuration->campaign_permission == '1' ? 0 : 1;
    }

    public function handle()
    {
        try {
            $smsRate = [];

           if ($this->isMasking === 'masking') {
		    $sms_masking_type = '2'; // Masking
		    $rates = AccSmsRate::select('operator_id', 'asr_masking')
			->where('user_id', $this->authUser)
			->get();
		    foreach ($rates as $rate) {
			$smsRate[$rate->operator_id] = $rate->asr_masking;
		    }

		} elseif ($this->isMasking === 'iptsp') {
		    $sms_masking_type = '1'; // IP-TSP Non-masking
		    $rates = AccSmsRate::select('operator_id', 'asr_nonmasking_iptsp')
			->where('user_id', $this->authUser)
			->get();
		    foreach ($rates as $rate) {
			$smsRate[$rate->operator_id] = $rate->asr_nonmasking_iptsp;
		    }

		} else {
		    $sms_masking_type = '1'; // Regular Non-masking
		    $rates = AccSmsRate::select('operator_id', 'asr_nonmasking')
			->where('user_id', $this->authUser)
			->get();
		    foreach ($rates as $rate) {
			$smsRate[$rate->operator_id] = $rate->asr_nonmasking;
		    }
		}

            $current_date = Carbon::now()->toDateTimeString();

            $operators = [];
            $getOperators = Operator::select('id', 'ope_operator_name', 'ope_number')->take(5)->get();
            foreach ($getOperators as $getOperator) {
                $opeNumbers = !empty($getOperator->ope_number) ? explode(',', $getOperator->ope_number) : [];
                foreach ($opeNumbers as $prefix) {
                    if (!isset($operators[$prefix])) {
                        $operators[$prefix] = $getOperator->id;
                    }
                }
            }

            $userDetail = UserDetail::where('user_id', $this->authUser)->first();
            if (count($this->validUniqueNumbers) >= 10 && $userDetail && $userDetail->campaign_permission == 1) {
                $campaign_accept_status = 1;
            } elseif (\SmsHelper::is_unicode($this->request['message'])) {
                $campaign_accept_status = 1;
            } elseif (count($this->validUniqueNumbers) >= 10) {
                $campaign_accept_status = $this->campaign_permission;
            } else {
                $campaign_accept_status = 1;
            }

            $insertCount = 0;
            $dataForInsert = [];
            foreach ($this->validUniqueNumbers as $number) {
                $ope_number = substr($number, 0, 5);
                $operator = isset($operators[$ope_number]) ? $operators[$ope_number] : null;
                $smsCost = isset($smsRate[$operator]) ? $smsRate[$operator] * $this->sms_number : 0;

                $dataForInsert[] = [
                    'user_id' => $this->authUser,
                    'sender_id' => $this->request['sender_id'],
                    'campaign_id' => $this->campaign_ids_id,
                    'scp_cell_no' => $number,
                    'scp_message' => preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $this->request['message']),
                    'scp_sms_cost' => $smsCost,
                    'operator_id' => $operator,
                    'scp_campaign_type' => $this->request['schedule'],
                    'scp_deal_type' => '1',
                    'scp_sms_type' => $sms_masking_type,
                    'scp_sms_id' => '0',
                    'scp_tried' => '0',
                    'scp_picked' => '0',
                    'scp_sms_text_type' => $this->smsType,
                    'scp_target_time' => $this->target_time,
                    'scp_campaign_status' => $campaign_accept_status,
                    'scp_status' => '1',
                    'created_at' => $current_date,
                    'updated_at' => $current_date,
                ];

                if (++$insertCount >= 20) {
                    SmsCamPending::insert($dataForInsert);
                    $dataForInsert = [];
                    $insertCount = 0;
                }
            }

            if (!empty($dataForInsert)) {
                SmsCamPending::insert($dataForInsert);
            }

            // Log::info('InsertSms job completed successfully.');
        } catch (\Exception $e) {
            Log::error('InsertSms Job Failed: ' . $e->getMessage());
        }
    }
}

