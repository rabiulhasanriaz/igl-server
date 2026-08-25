<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\SmsDesktopPending;
use App\Model\SmsDesktop24h;
use Carbon\Carbon;

class SmsSendCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smsSend:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $retText = "no sms found";
       


        $getNonMaskingSmsCampaigns = SmsDesktopPending::
            where('sdp_target_time','<=', Carbon::now())
            ->whereIn('sdp_campaign_type', ['1','2'])
            ->where('sdp_campaign_status', 0)
            ->groupBy('sdp_message')
            ->take(10)
            ->orderBy('id', 'asc')
            ->get();

        
        // dd($getNonMaskingSmsCampaigns);
        if (count($getNonMaskingSmsCampaigns) > 0) {
            // $smsLoop = 1;
            foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

                $limitSms = 100;
                $sms = array();
                $transferredSmsId = array();
                $getSms50OfSameCampaignIds = SmsDesktopPending::where([
                    'campaign_id' => $nonMaskingSmsCampaign->campaign_id,
                    'sdp_campaign_status' => 0,
                    'sdp_message' => $nonMaskingSmsCampaign->sdp_message])
                    ->take($limitSms)
                    ->get();
                // dd($getSms50OfSameCampaignIds);
                $numbers = array();
                foreach ($getSms50OfSameCampaignIds as $sms50Details) {
                    // dd($sms50Details);
                    $numbers[] = $sms50Details->sdp_cell_no;
                    $transferredSmsId[] = $sms50Details->id;
                    
                }
                

                $countTSms = 0;
                
                $userName = $nonMaskingSmsCampaign->api_user_name->routeDetail->user_name;
                
                
                
                $password = $nonMaskingSmsCampaign->api_user_name->routeDetail->password;
                
                    
                   $xml_response = \SmsHelper::send_desktop_sms($userName,$password,$numbers,$nonMaskingSmsCampaign->sdp_message);
                    $xmlResponseArray[] = $xml_response->array;
                   
               
                   foreach($xmlResponseArray as $key => $array) {
                    foreach($array as $key1 => $value) {
                          $xmlResponseArrayValue[] = $array[$key1][1];
                        }
                    }
                
                    
                    
                    
                    // dd($xml_response);
                    
                    if ($xml_response == '-1') {
                            $retText = "something was missing for sending sms";
                        } elseif ($xml_response == 'blast') {
                            $retText = "something went wrong to send sms";
                        } else {
                            $blDataForInsert = array();
                            
                            foreach ($xmlResponseArrayValue as $blNumber) {
                                // dd($blNumber);
                                $checkedSms = SmsDesktopPending::where('id', $transferredSmsId[$countTSms])->first();
                                // dd($checkedSms);
                                $blDataForInsert[] = array(
                                    'user_id' => $checkedSms->user_id,
                                    // 'sender_id' => $checkedSms->sender_id,
                                    'campaign_id' => $checkedSms->campaign_id,
                                    'sdt_cell_no' => $checkedSms->sdp_cell_no,
                                    'sdt_message' => $checkedSms->sdp_message,
                                    'sdt_sms_cost' => $checkedSms->sdp_sms_cost,
                                    'operator_id' => $checkedSms->operator_id,
                                    'sdt_campaign_type' => $checkedSms->sdp_campaign_type,
                                    'sdt_deal_type' => $checkedSms->sdp_deal_type,
                                    'sdt_sms_type' => $checkedSms->sdp_sms_type,
                                    'sdt_sms_id' => $blNumber,
                                    'sdt_sms_text_type' => $checkedSms->sdp_sms_text_type,
                                    'sdt_target_time' => $checkedSms->sdp_target_time,
                                    'created_at' => $checkedSms->created_at,
                                    'updated_at' => $checkedSms->updated_at,
                                    'sdt_delivery_report' => 'PENDING',
                                    'sdt_status' => '0',
                                );
                                // dd($blDataForInsert);
                                $countTSms++;
                            }
                            // dd($blDataForInsert);
                            try {
                                SmsDesktop24h::insert($blDataForInsert);
                                $blDataForInsert = array();

                                SmsDesktopPending::whereIn('id', $transferredSmsId)->delete();

                                
                                \Log::info("Working...");

                            } catch (\Exception $e) {
                                
                                \Log::info("something went wrong" . $e->getMessage());
                            }

                        }
                    
                    
                    
                // }
            }
            
            
            
        }
        return \Log::info($retText);

    }
}
