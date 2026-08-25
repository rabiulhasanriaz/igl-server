<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\SmsDesktopPending;
use App\Model\SmsDesktop24h;
use App\Model\SmsDesktop;
use Carbon\Carbon;
use Response;
use DB;


class SmsDesktopController extends Controller
{
    


    

    public function smsDesktopSms()
    {

        
        $retText = "no sms found";
       


        $getNonMaskingSmsCampaigns = SmsDesktopPending::
            where('sdp_target_time','<=', Carbon::now())
            ->whereIn('sdp_campaign_type', ['2','1'])
            ->where('sdp_campaign_status', 0)
            // ->where('campaign_id',10247)
            ->groupBy('sdp_message')
            ->take(10)
            ->orderBy('id', 'desc')
            ->get();

        // dd($getNonMaskingSmsCampaigns);
        if (count($getNonMaskingSmsCampaigns) > 0) {
            $smsLoop = 1;
            foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

                $limitSms = 1000;
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
                    dd($sms50Details);
                    $numbers[] = $sms50Details->sdp_cell_no;
                    
                    $transferredSmsId[] = $sms50Details->id;
                
                // dd($transferredSmsId);
                }
                
                $countTSms = 0;
                $userName = $nonMaskingSmsCampaign->api_user_name->routeDetail->user_name;
                // dd($userName);
                
                $password = $nonMaskingSmsCampaign->api_user_name->routeDetail->password;
                // dd($password);
                
                    
                   $xml_response = \SmsHelper::send_desktop_sms($userName,$password,$numbers,$nonMaskingSmsCampaign->sdp_message);
                   // dd($xml_response);
                    

                    // dd($xmlResponseArray);
                    // dd($xml_response);
                   
               
                   
                    // dd($xmlResponseArrayValue);
                    
                    if ($xml_response == "blast") {
                            $retText = "something went wrong to call dynamic api";
                        }else {
                            $xmlResponseArray[] = $xml_response->array;
                            // dd($xmlResponseArray);
                            // $blDataForInsert = array();
                            foreach($xmlResponseArray as $key => $array) {
                            foreach($array as $key1 => $value) {
                                  //$xmlResponseArrayValue[] = ;
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
                                        'sdt_sms_id' => $array[$key1][1],
                                        'sdt_sms_text_type' => $checkedSms->sdp_sms_text_type,
                                        'sdt_target_time' => $checkedSms->sdp_target_time,
                                        'created_at' => $checkedSms->created_at,
                                        'updated_at' => $checkedSms->updated_at,
                                        'sdt_delivery_report' => 'DELIVERED',
                                        'sdt_status' => '0',
                                    );
                                    $countTSms++;

                                }
                            }
                            
                            // foreach ($numbers as $blNumber) {
                            //     // dd($blNumber);
                                
                            //     // dd($blDataForInsert);
                                
                            // }
                            // dd($blDataForInsert);
                            // DB::beginTransaction();
                            try {
                                SmsDesktop24h::insert($blDataForInsert);
                                $blDataForInsert = array();

                                SmsDesktopPending::whereIn('id', $transferredSmsId)->delete();

                                $retText = "Working...". $smsLoop++;
                                
                                return view('cron.sms-desktop',compact('retText'));
                            } catch (\Exception $e) {
                                // DB::rollback();
                                $retText = "something went wrong" . $e->getMessage();
                                return view('cron.sms-desktop', compact('retText'));
                            }
                            // DB::commit();

                        }


                    
                    
                    
                // }
            }
            
            

            
        }
        
        return view('cron.sms-desktop', compact('retText'));
    }

    public function smsDesktopSms2(){
        $getNonMaskingSmsCampaigns = SmsDesktopPending::
                                        where('sdp_target_time','<=', Carbon::now())
                                        ->whereIn('sdp_campaign_type', ['2','1'])
                                        ->where('sdp_campaign_status', 0)
                                        ->groupBy('sdp_message')
                                        ->take(10)
                                        ->orderBy('id', 'desc')
                                        ->get();

            // dd($getNonMaskingSmsCampaigns);
            if (count($getNonMaskingSmsCampaigns) > 0) {
                $smsLoop = 1;
                foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

                    $limitSms = 1000;
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
                    
                    // dd($transferredSmsId);
                    }
                    // dd($numbers);
                    $countTSms = 0;
                    $userName = $nonMaskingSmsCampaign->api_user_name->routeDetail->user_name;
                    // dd($userName);
                
                    $password = $nonMaskingSmsCampaign->api_user_name->routeDetail->password;
                    // dd($nonMaskingSmsCampaign->sdp_message);
                
                    
                   $xml_response = \SmsHelper::send_desktop_sms($userName,$password,$numbers,$nonMaskingSmsCampaign->sdp_message);
                   // dd($xml_response);
                //   $xmlResponseArray = $xml_response->array; 
                   
                //   for ($i=0; $i < count($xmlResponseArray); $i++) { 
                //       $value[] = $xmlResponseArray[$i][1];
                //   }
                        // if ($xml_response->status == '-1') {
// dd($xml_response->status);
                        if ($xml_response->status == '-1') {
                            $retText = "Something was missing";
                            // \Log::info('Something was missing');
                        } elseif ($xml_response->status == '-2') {
                            $retText = "Error from two";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-3') {
                            $retText = "Error from 3";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-4') {
                            $retText = "Content Empty to call Desktop api";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-5') {
                            $retText = "Error from Five";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-6') {
                            $retText = "Error from 6";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-7') {
                            $retText = "Error from 7";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-8') {
                            $retText = "Error From 8";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-9') {
                            $retText = "Error from 9";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-10') {
                            $retText = "Error from 10";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-11') {
                            $retText = "Error from 11";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-12') {
                            $retText = "Error from 12";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response->status == '-13') {
                            $retText = "Error from 13";
                            // $retText = "content empty";
                            // \Log::info('content empty');
                        }elseif ($xml_response == 'blast') {
                            $retText = "something went wrong to call dynamic api";
                            // \Log::info('something went wrong to call dynamic api');
                        } elseif ($xml_response->status == '0'??"") {
                            
                            $blDataForInsert = array();
                            $xmlResponseArray = array();
                            $xmlResponseArray[] = $xml_response->array;
                            foreach($xmlResponseArray as $key => $array){
                                foreach($array as $key1 => $value){
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
                                        'sdt_sms_id' => $array[$key1][1],
                                        'sdt_sms_text_type' => $checkedSms->sdp_sms_text_type,
                                        'sdt_target_time' => $checkedSms->sdp_target_time,
                                        'created_at' => $checkedSms->created_at,
                                        'updated_at' => $checkedSms->updated_at,
                                        'sdt_delivery_report' => 'DELIVERED',
                                        'sdt_status' => '0',
                                    );
                                    $countTSms++;
                                }
                            }
                            
                            try {
                                SmsDesktop24h::insert($blDataForInsert);
                                $blDataForInsert = array();
// dd($blDataForInsert->status);
                                SmsDesktopPending::whereIn('id', $transferredSmsId)->delete();

                                $retText = "Working...". $smsLoop++;
                                // \Log::info('Working...' . $smsLoop++);
                            } catch (\Exception $e) {
                                $retText = "something went wrong" . $e->getMessage();
                                 // \Log::info('something went wrong' . $e->getMessage());
                                return view('cron.sms-desktop', compact('retText'));
                            }

                        }
                     


                }
                return view('cron.sms-desktop', compact('retText'));
                // \Log::info($retText);

            } else {
                $retText = "no sms found";
                // \Log::info('no sms found');
                return view('cron.sms-desktop', compact('retText'));
            }
    }

        public function deliveryReport()
    {
        $changedNumber = 0;

        // Move pending numbers from SmsDesktop24h to SmsDesktop
        try {
            DB::transaction(function () use (&$changedNumber) {
                $pendingNumbers = SmsDesktop24h::where('sdt_delivery_report', 'DELIVERED')
                    ->where('sdt_tried', '0')
                    ->take(5000)
                    ->get();

                foreach ($pendingNumbers as $pendingNumber) {
                    SmsDesktop::create([
                        'user_id' => $pendingNumber->user_id,
                        'campaign_id' => $pendingNumber->campaign_id,
                        'sd_cell_no' => $pendingNumber->sdt_cell_no,
                        'sd_message' => $pendingNumber->sdt_message,
                        'sd_sms_cost' => $pendingNumber->sdt_sms_cost,
                        'operator_id' => $pendingNumber->operator_id,
                        'sd_campaign_type' => $pendingNumber->sdt_campaign_type,
                        'sd_deal_type' => $pendingNumber->sdt_deal_type,
                        'sd_sms_type' => $pendingNumber->sdt_sms_type,
                        'sd_sms_id' => $pendingNumber->sdt_sms_id,
                        'sd_tried' => $pendingNumber->sdt_tried,
                        'sd_sms_text_type' => $pendingNumber->sdt_sms_text_type,
                        'sd_submitted_time' => $pendingNumber->created_at,
                        'sd_targeted_time' => $pendingNumber->sdt_target_time,
                        'sd_delivery_report' => 'DELIVERED', // Assuming all moved are marked as delivered
                        'sd_status' => $pendingNumber->sdt_status,
                    ]);

                    $changedNumber++;
                }

                // Delete moved data from SmsDesktop24h
                SmsDesktop24h::whereIn('id', $pendingNumbers->pluck('id'))->delete();
            });
        } catch (\Exception $e) {
            // Handle exceptions if necessary
        }

        // Prepare return data
        $returnData['changed'] = $changedNumber;

        return view('cron.sms-desktop-delivery', compact('returnData'));
    }

    public function delete_data(){

        // $returnData['still_pending'] = $_SESSION['offsetData'];
        // $returnData['check_complete'] = $_SESSION['goToNullOffset'];
        // $returnData['changed'] = $chengedNumber;
        DB::beginTransaction();
        try {
            
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
                        'sd_tried' => $moveData->sdt_tried,
                        'sd_sms_text_type' => $moveData->sdt_sms_text_type,
                        'sd_submitted_time' => $moveData->created_at,
                        'sd_targeted_time' => $moveData->sdt_target_time,
                        'sd_delivery_report' => $moveData->sdt_delivery_report,
                        'sd_status' => $moveData->sdt_status,
                    ]);
                }

                SmsDesktop24h::where('sdt_target_time', '<=', Carbon::now()->subHours(24))->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        return view('cron.sms-desktop-delete');
    }











// public function nonMaskingSmsa()
//     {
//         //for ($loopNo1 = 1; $loopNo1 <= 10; $loopNo1++) {

//             $getNonMaskingSmsCampaigns = SmsDesktopPending::
//             where('sdp_target_time','<=', Carbon::now())
//             ->whereIn('sdp_campaign_type', ['2','1'])
//             ->where('sdp_campaign_status', 0)
//             ->groupBy('sdp_message')
//             ->take(10)
//             ->orderBy('id', 'asc')
//             ->get();
//             // dd($getNonMaskingSmsCampaigns);

//             if (count($getNonMaskingSmsCampaigns) > 0) {
//                 $smsLoop = 1;
//                 foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

//                     $limitSms = 500;
//                     $sms = array();
//                     $transferredSmsId = array();
//                     $getSms50OfSameCampaignIds = SmsDesktopPending::where([
//                         'campaign_id' => $nonMaskingSmsCampaign->campaign_id,
//                         'sdp_campaign_status' => 0,
//                         'sdp_message' => $nonMaskingSmsCampaign->sdp_message])
//                         ->take($limitSms)
//                         ->get();
//                     // dd($getSms50OfSameCampaignIds);
//                     $numbers = array();
//                     foreach ($getSms50OfSameCampaignIds as $sms50Details) {
//                         // dd($sms50Details);
//                         $numbers[] = $sms50Details->sdp_cell_no;
//                         // dd($numbers);
//                         $transferredSmsId[] = $sms50Details->id;
                    
//                     // dd($transferredSmsId);
//                     }

//                     $countTSms = 0;
//                     $userName = $nonMaskingSmsCampaign->api_user_name->routeDetail->user_name;
                
                
//                     $password = $nonMaskingSmsCampaign->api_user_name->routeDetail->password;


                        

//                     $xml_response = \SmsHelper::send_desktop_sms($userName,$password,$numbers,$nonMaskingSmsCampaign->sdp_message);
//                     $xmlResponseArray[] = $xml_response->array;
                   
               
//                    foreach($xmlResponseArray as $key => $array) {
//                     foreach($array as $key1 => $value) {
//                           $xmlResponseArrayValue[] = $array[$key1][1];
//                         }
//                     }
//                         // dd($xml_response);
//                         if ($xml_response == '-1') {
//                             $retText = "Something was missing";
//                         } elseif ($xml_response == 'blast') {
//                             // $retText = "Something Went Wrong to call robi non-masking api";
//                             $retText = "Something Went Wrong to call robi non-masking api";
//                         } else {
//                             $dataForInsert = array();

//                             foreach ($xmlResponseArrayValue as $blNumber) {

//                                 $checkedSms = SmsDesktopPending::where('id', $transferredSmsId[$countTSms])->first();
//                                 // dd($checkedSms);
//                                 $dataForInsert[] = array(
//                                     'user_id' => $checkedSms->user_id,
//                                     // 'sender_id' => $checkedSms->sender_id,
//                                     'campaign_id' => $checkedSms->campaign_id,
//                                     'sdt_cell_no' => $checkedSms->sdp_cell_no,
//                                     'sdt_message' => $checkedSms->sdp_message,
//                                     'sdt_sms_cost' => $checkedSms->sdp_sms_cost,
//                                     'operator_id' => $checkedSms->operator_id,
//                                     'sdt_campaign_type' => $checkedSms->sdp_campaign_type,
//                                     'sdt_deal_type' => $checkedSms->sdp_deal_type,
//                                     'sdt_sms_type' => $checkedSms->sdp_sms_type,
//                                     'sdt_sms_id' => $blNumber,
//                                     'sdt_sms_text_type' => $checkedSms->sdp_sms_text_type,
//                                     'sdt_target_time' => $checkedSms->sdp_target_time,
//                                     'created_at' => $checkedSms->created_at,
//                                     'updated_at' => $checkedSms->updated_at,
//                                     'sdt_delivery_report' => 'PENDING',
//                                     'sdt_status' => '0',
//                                 );
//                                 // dd($blDataForInsert);
//                                 $countTSms++;
//                             }
//                             DB::beginTransaction();
//                             try {
//                                 SmsDesktop24h::insert($dataForInsert);
//                                 $dataForInsert = array();

//                                 SmsDesktopPending::whereIn('id', $transferredSmsId)->delete();

//                                 $retText = "Working...". $smsLoop++;
//                             } catch (\Exception $e) {
//                                 DB::rollback();
//                                 $retText = "something went wrong" . $e->getMessage();
//                                 return view('cron.sms-desktop', compact('retText'));
//                             }
//                             DB::commit();

//                         }
                    


//                 }
//                 return view('cron.sms-desktop', compact('retText'));

//             } else {
//                 $retText = "no sms found";
//                 return view('cron.sms-desktop', compact('retText'));
//             }
//         //}
//         //return view('cron.non-masking', compact('retText'));

//     }


    public function nonMaskingSmsa()
    {
        //for ($loopNo1 = 1; $loopNo1 <= 10; $loopNo1++) {
// dd('a');
            $retText = "no sms found";
       


        $getNonMaskingSmsCampaigns = SmsDesktop24h::
            where('sdt_target_time','<=', Carbon::now()->subHours(24))
            ->groupBy('sdt_message')
            ->take(10)
            ->orderBy('id', 'asc')
            ->get();

        // dd($getNonMaskingSmsCampaigns);
        if (count($getNonMaskingSmsCampaigns) > 0) {
            $smsLoop = 1;
            foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

                $limitSms = 500;
                $sms = array();
                $transferredSmsId = array();
                $getSms50OfSameCampaignIds = SmsDesktop24h::where([
                    'campaign_id' => $nonMaskingSmsCampaign->campaign_id,
                    'sdt_message' => $nonMaskingSmsCampaign->sdt_message])
                    ->take($limitSms)
                    ->get();
                // dd($getSms50OfSameCampaignIds);
                $numbers = array();
                foreach ($getSms50OfSameCampaignIds as $sms50Details) {
                    // dd($sms50Details);
                    $numbers[] = $sms50Details->sdt_cell_no;
                    // dd($numbers);
                    $transferredSmsId[] = $sms50Details->id;
                
                // dd($transferredSmsId);
                }
                // dd($numbers);
                $countTSms = 0;
                
               
                   
                   
                
                            $blDataForInsert = array();
                            
                            foreach($numbers as $number) {
                                      $checkedSms = SmsDesktop24h::where('id', $transferredSmsId[$countTSms])->first();
                                // dd($checkedSms);
                                    $blDataForInsert[] = array(
                                        'user_id' => $checkedSms->user_id,
                                        //'sender_id' => $moveData->sender_id,
                                        'campaign_id' => $checkedSms->campaign_id,
                                        'sd_cell_no' => $checkedSms->sdt_cell_no,
                                        'sd_message' => $checkedSms->sdt_message,
                                        'sd_sms_cost' => $checkedSms->sdt_sms_cost,
                                        'operator_id' => $checkedSms->operator_id,
                                        'sd_campaign_type' => $checkedSms->sdt_campaign_type,
                                        'sd_deal_type' => $checkedSms->sdt_deal_type,
                                        'sd_sms_type' => $checkedSms->sdt_sms_type,
                                        'sd_sms_id' => $checkedSms->sdt_sms_id,
                                        'sd_sms_text_type' => $checkedSms->sdt_sms_text_type,
                                        'sd_submitted_time' => $checkedSms->created_at,
                                        'sd_targeted_time' => $checkedSms->sdt_target_time,
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now(),
                                        'sd_delivery_report' => $checkedSms->sdt_delivery_report,
                                        'sd_status' => $checkedSms->sdt_status,
                                    );
                                    $countTSms++;
                                
                            }            
                            // dd($blDataForInsert);
                           DB::beginTransaction();
                            try {
                                SmsDesktop::insert($blDataForInsert);
                                $blDataForInsert = array();

                                SmsDesktop24h::whereIn('id', $transferredSmsId)->delete();
                                DB::commit();
                                $retText = "Working...". $smsLoop++;
                                return view('cron.sms-desktop-delete', compact('retText'));
                                
                            } catch (\Exception $e) {
                                DB::rollback();
                                $retText = "something went wrong" . $e->getMessage();
                                return view('cron.sms-desktop-delete', compact('retText'));
                            }

                        // }
                
                
                    
                    
                    
                // }
            }
            

            
        }
        return view('cron.sms-desktop-delete', compact('retText'));

    }



    public function deliveryReportRoute2(){
        // for ($j = 0; $j < 10; $j++) {
        
                              // dd($tried);
        $getPendingData = SmsDesktop24h::where('sdt_sms_type',1)
                                       ->where('sdt_delivery_report','DELIVERED')
                                       ->where('sdt_tried','0')
                                       ->take(100)
                                       ->orderBy('id','desc')
                                       ->get();
                                       // dd($getPendingData);
        if (count($getPendingData) > 0) {
            foreach ($getPendingData as $data) {
                $smsId[] = $data->sdt_sms_id;
                $userName = $data->report_user_name->routeDetail->user_name;
                        
                    
                    
                $password = $data->report_user_name->routeDetail->password;
            }
            
            // dd($smsId);
    
            $jsonDeliveryReport = \SmsHelper::getRoute2DeliveryReport($smsId,$userName,$password);
            // dd($jsonDeliveryReport);
    
            if ($jsonDeliveryReport->status != '0') {
                $result = "Something Went Wrong!";
                
            }else {
                $delivryReport = $jsonDeliveryReport;
                $countCheckNumber = count($delivryReport->array);
                // dd($countCheckNumber);
                for ($i = 0; $i < $countCheckNumber; $i++) {
                    $loop = 1;
                    // dd($delivryReport->status);
                    if ($delivryReport->status != "1" || $delivryReport->status != "2") {
                        $smsId = $delivryReport->array[$i][0];
                        // dd($smsId);
                        $report = $delivryReport->array[$i][5];
                        $updReport = SmsDesktop24h::where('sdt_sms_id', $smsId)->first();
                        // dd($updReport);
                        if ($updReport) {
                            if ($report == '0') {
                                $updReport->sdt_delivery_report = "DELIVERED";
                                $updReport->sdt_tried = "0";
                            }elseif ($report == '1') {
                                $updReport->sdt_delivery_report = "DELIVERED";
                                $updReport->sdt_tried = "1";
                            } elseif ($report == '2') {
                                $updReport->sdt_delivery_report = "FAILED";
                                $updReport->sdt_tried = "2";
                            }elseif ($report == '3') {
                                $updReport->sdt_delivery_report = "DELIVERED";
                                $updReport->sdt_tried = "3";
                            }elseif ($report == '4') {
                                $updReport->sdt_delivery_report = "TIME OUT";
                                $updReport->sdt_tried = "4";
                            }elseif ($report == '5') {
                                $updReport->sdt_delivery_report = "OTHER";
                                $updReport->sdt_tried = "5";
                            }
                            
                            $updReport->save();
                            
                        }

                    } 
                }
                
            }
            $result = "Working....";
        }else {
            $result = "No Pending Data";   
        }
    // }
        
        
        
        return view('cron.route2-delivery',compact('result'));
    }


    




    


    

    



    



    


}
