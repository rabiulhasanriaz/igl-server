<?php

namespace App\Jobs;

use App\Model\AccSmsBalance;
use App\Model\AccSmsRate;
use App\Model\AccUserCreditHistory;
use App\Model\Operator;
use App\Model\SmsCampaignId;
use App\Model\SmsCamPending;
use App\Model\SmsDesktopPending;
use App\Model\SystemConfiguration;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InsertDesktopSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    // protected $isMasking;
    protected $request;
    protected $validUniqueNumbers;
    protected $total_cost;
    protected $target_time;
    protected $sms_number;
    protected $smsType;
    protected $authUser;
    protected $campaign_ids_id;
    private $campaign_permission;

    public function __construct($request,$validUniqueNumbers,$total_cost,$target_time,$sms_number,$smsType,$authUser,$campaign_ids_id)
    {
        // $this->isMasking = $isMasking;
        $this->request = $request;
        $this->validUniqueNumbers = $validUniqueNumbers;
        $this->total_cost = $total_cost;
        $this->target_time = $target_time;
        $this->sms_number = $sms_number;
        $this->smsType = $smsType;
        $this->authUser= $authUser;
        $this->campaign_ids_id = $campaign_ids_id;

        $system_configuration = SystemConfiguration::first();
        if (!empty($system_configuration)) {
            $this->campaign_permission = ($system_configuration->campaign_permission == '1')? 0 : 1;
        } else {
            $this->campaign_permission = 1;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $smsRate = array();
            // if ($this->isMasking == true) {
            //     $sms_masking_type = '2';
            //     $getSms_rates = AccSmsRate::select('operator_id','asr_masking')->where(['user_id' => $this->authUser])->get();
            //     foreach ($getSms_rates as $getsms_rate) {
            //         $smsRate[$getsms_rate['operator_id']] = $getsms_rate['asr_masking'];
            //     }
            // } else {
                $sms_masking_type = '1';

                $getSms_rates = AccSmsRate::select('operator_id','asr_dynamic')->where(['user_id' => $this->authUser])->get();
                
                foreach ($getSms_rates as $getsms_rate) {
                    $smsRate[$getsms_rate['operator_id']] = $getsms_rate['asr_dynamic'];
                }
            // }

            $current_date = Carbon::now()->toDateTimeString();

            $operators = array();
            $getOperators = Operator::select('id', 'ope_operator_name', 'ope_number')->take(5)->get();
            foreach ($getOperators as $getOperator){
                $getOperators1 = explode(',', $getOperator['ope_number']);
                foreach ($getOperators1 as $getOperator1){
                    $operators[$getOperator1] = $getOperator['id'];
                }
            }



            // if (count($this->validUniqueNumbers) >= 10) {
            //     $campaign_accept_status = $this->campaign_permission;
            // } else {
            //     $campaign_accept_status = 1;
            // }


            $insertCount = 0;
            $dataForInsert = array();
            $serial = 0;
            foreach ($this->validUniqueNumbers as $number) {
                $ope_number = substr($number, 0, 5);
                $operator = $operators[$ope_number];
                $smsCost = ($smsRate[$operator]*$this->sms_number);

                $dataForInsert[] = array(
                    'user_id' => $this->authUser,
                    // 'sender_id' => $this->request['sender_id'],
                    'campaign_id' => $this->campaign_ids_id,
                    'sdp_cell_no' => $number,
                    'sdp_message' => preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $this->request['message']),
                    'sdp_sms_cost' => $smsCost,//\BalanceHelper::singleSmsCost($this->sms_number, $number, $this->isMasking, $this->authUser)
                    'operator_id' => $operator,
                    'sdp_campaign_type' => $this->request['schedule'], //*1=instant, 2=Schedule *
                    'sdp_deal_type' => '1', //* 1=SMS, 2=Campaign *
                    'sdp_sms_type' => $sms_masking_type, //*1=NonMasking, 2=Masking*
                    'sdp_sms_id' => '0',
                    'sdp_tried' => '0', //*Try For Send *
                    'sdp_picked' => '0', //*0=not try, 1= try *
                    'sdp_sms_text_type' => $this->smsType, //*SMS type=text/unicode*
                    'sdp_target_time' => $this->target_time,
                    'sdp_campaign_status' => '0',
                    'sdp_status' => '1',
                    'created_at' => $current_date,
                    'updated_at' => $current_date,
                );
                if ($insertCount < 20) {
                    $insertCount++;
                } else {
                    SmsDesktopPending::insert($dataForInsert);
                    $dataForInsert = array();
                    $insertCount = 0;
                }
            }
            SmsDesktopPending::insert($dataForInsert);

            //*Log::info('Route 2 SMS Campaign Inserted Successfully');*
        }catch (\Exception $e) {
            //*Log::info('boooooooooo'.$e->getMessage());*
        }
    }
}
