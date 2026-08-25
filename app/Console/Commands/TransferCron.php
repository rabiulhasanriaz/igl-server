<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\SmsDesktop24h;
use App\Model\SmsDesktop;
use Carbon\Carbon;
use DB;

class TransferCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:cron';

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
           
        $chengedNumber = 0;

        /*set offsetData in session if wasn't set previous*/
        if (!isset($_SESSION['offsetData'])) {
            $_SESSION['offsetData'] = 0;
        }

        /*set goToNullOffset in session if wasn't set previous*/
        if (!isset($_SESSION['goToNullOffset'])) {
            $_SESSION['goToNullOffset'] = 0;
        }

        for ($j = 0; $j < 10; $j++) {

            /*set offsetData variable based on session offsetData & goToNullOffset*/
            if ($_SESSION['goToNullOffset'] == 0) {
                $offsetData = $_SESSION['offsetData'];

            } else {
                $offsetData = 0;
                $_SESSION['offsetData'] = 0;
                $_SESSION['goToNullOffset'] = 0;
            }


            

            /*get undelivered numbers*/
            $pendingNumbers = SmsDesktop24h::select('sdt_sms_id','user_id')
                                            ->where('sdt_sms_type','1')
                                            ->whereIn('sdt_delivery_report',['PENDING','NOT ENABLE DELIVER','SENT BUT NOT RECEIVE DELIVER'])
                                            ->skip($offsetData)
                                            ->take(30)
                                            ->get();

            // dd($pendingNumbers);

            $undeliveredNumber = Null;
            if (count($pendingNumbers) < 50) {
                $_SESSION['goToNullOffset'] = 1;
            }
            if (count($pendingNumbers) > 0) {

                $numberOfRows = count($pendingNumbers);

                foreach ($pendingNumbers as $pendingNumber) {
                    if (!empty($pendingNumber['sdt_sms_id'])) {
                        if ($undeliveredNumber == null) {
                            $undeliveredNumber = $pendingNumber['sdt_sms_id'];
                        } else {
                            $undeliveredNumber = $undeliveredNumber . "," . $pendingNumber['sdt_sms_id'];
                        }
                    }
                    $userName = $pendingNumber->report_user_name->routeDetail->user_name;
                    
                
                
                    $password = $pendingNumber->report_user_name->routeDetail->password;
                    // dd($password);
                }


                $jsonDeliveryReport = \SmsHelper::getRoute2DeliveryReport($undeliveredNumber,$userName,$password);
               
                // dd($jsonDeliveryReport);
                
                if ($jsonDeliveryReport == '0150') {
                    $returnData['no_number'] = "something went wrong";
                } elseif ($jsonDeliveryReport == '0160') {
                    $returnData['no_number'] = "something went wrong";
                } else {
                    $delivryReport = $jsonDeliveryReport;
                    
                    
                    //dd($xmlResponseArrayValue);
                   
                    $countCheckNumber = count($delivryReport->array);
                    

                    $_SESSION['offsetData'] = $_SESSION['offsetData'] + ($numberOfRows - $countCheckNumber);
                    
                    for ($i = 0; $i < $countCheckNumber; $i++) {
                        if ($delivryReport->status != "1" || $delivryReport->status != "2") {
                            $smsId = $delivryReport->array[$i][0];
                            $report = $delivryReport->array[$i][5];
                            $updReport = SmsDesktop24h::where('sdt_sms_id', $smsId)->first();
                            // dd($updReport);
                            if ($updReport) {
                                if ($report == '0') {
                                    $updReport->sdt_delivery_report = "NOT ENABLE DELIVER";
                                }elseif ($report == '1') {
                                    $updReport->sdt_delivery_report = "SENT BUT NOT RECEIVE DELIVER";
                                } elseif ($report == '2') {
                                    $updReport->sdt_delivery_report = "FAILED";
                                }elseif ($report == '3') {
                                    $updReport->sdt_delivery_report = "DELIVERED";
                                }elseif ($report == '4') {
                                    $updReport->sdt_delivery_report = "TIME OUT";
                                }elseif ($report == '5') {
                                    $updReport->sdt_delivery_report = "OTHER";
                                }
                                
                                $updReport->save();
                                $chengedNumber++;
                            }

                        } else {
                            $_SESSION['offsetData']++;
                        }
                    }
                }
            } else {
                $returnData['no_number'] = "no number available for check report";
            }

            if ($_SESSION['goToNullOffset'] == 1) {
                break;
            }

        }

//        SmsCampaign_24h::where('sct_sms_type', '2')->update(['sct_delivery_report'=>'DELIVERED']);

        $returnData['still_pending'] = $_SESSION['offsetData'];
        $returnData['check_complete'] = $_SESSION['goToNullOffset'];
        $returnData['changed'] = $chengedNumber;

        try {
            DB::transaction(function () {
                $moveDatasFromToday = SmsDesktop24h::where('sdt_target_time', '<=', Carbon::now()->subHours(24))->get();
                // dd($moveDatasFromToday);
                foreach ($moveDatasFromToday as $moveData) {
                    SmsDesktop::create([
                        'user_id' => $moveData->user_id,
                        //'sender_id' => $moveData->sender_id,
                        'campaign_id' => $moveData->campaign_id,
                        'sd_cell_no' => $moveData->sdt_cell_no,
                        'sd_message' => $moveData->sdt_message,
                        'sd_sms_cost' => $moveData->sdt_sms_cost,
                        'operator_id' => $moveData->operator_id,
                        'sd_campaign_type' => $moveData->sdt_campaign_type,
                        'sd_deal_type' => $moveData->sdt_deal_type,
                        'sd_sms_type' => $moveData->sdt_sms_type,
                        'sd_sms_id' => $moveData->sdt_sms_id,
                        'sd_sms_text_type' => $moveData->sdt_sms_text_type,
                        'sd_submitted_time' => $moveData->created_at,
                        'sd_targeted_time' => $moveData->sdt_target_time,
                        'sd_delivery_report' => $moveData->sdt_delivery_report,
                        'sd_status' => $moveData->sdt_status,
                    ]);
                }

                SmsDesktop24h::where('sdt_target_time', '<=', Carbon::now()->subHours(24))->delete();

            });
        } catch (\Exception $e) {

        }

        \Log::info($returnData);
    
        
    }
}
