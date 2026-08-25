<?php

namespace App\Http\Controllers\Api;

use App\Model\AccSmsBalance;
use App\Model\AccUserCreditHistory;
use App\Model\ApiLog;
use App\Model\SenderIdRegister;
use App\Model\SmsCampaign_24h;
use App\Model\SenderIdUser;
use App\Model\SmsCampaignId;
use App\Model\SmsCamPending;
use App\Model\SmsIpDailyLimit;
use App\Model\User;
use App\Model\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    /*send sms*/
    public function sendSms(Request $request)
    {
        $startedAt = microtime(true);
        $apiLog = $this->createApiLog($request, 'sendSms');

        /*validate data*/
        if (!$request->api_key) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445010', 'Missing api key');
            return response()->json(['code'=>'445010', 'message'=>'Missing api key']);
        } elseif (!$request->contacts) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445020', 'Missing contact numbers');
            return response()->json(['code'=>'445020', 'message'=>'Missing contact numbers']);
        } elseif (!$request->senderid) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445030', 'Missing sender id');
            return response()->json(['code'=>'445030', 'message'=>'Missing sender id']);
        } elseif (!$request->msg) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445170', 'Missing text sms');
            return response()->json(['code'=>'445170', 'message'=>'Missing text sms']);
        }

        /*check exist data*/
        /*check api*/
        if ($request->type == 1) {
            $userDetail = UserDetail::where('api_key', $request->api_key)->first();
        } else {
            $userDetail = UserDetail::where('api_key', $request->api_key)->where('api_permission', 1)->first();
        }

        if (!$userDetail) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445040', 'Invalid api key or You Need API Permission');
            return response()->json(['code'=>'445040', 'message'=>'Invalid api key or You Need API Permission']);
        }

        // ========== IP WHITELIST VALIDATION WITH DAILY LIMIT ==========
        $clientIp = $request->ip();
        $whitelistedIp = $userDetail->white_listed_ip;

        $isWhitelisted = false;
        $dailyLimit = 50; // Daily SMS limit per user for non-whitelisted IPs

        // Check if user has whitelisted IP configured
        if (!empty($whitelistedIp) && $whitelistedIp !== null) {
            // Check if client IP is in whitelist
            $whitelistedIps = array_map('trim', explode(',', $whitelistedIp));

            foreach ($whitelistedIps as $allowedIp) {
                if ($clientIp === $allowedIp) {
                    $isWhitelisted = true;
                    break;
                }
                // Check for wildcard pattern (e.g., 192.168.1.*)
                if (strpos($allowedIp, '*') !== false) {
                    $pattern = str_replace('.', '\.', $allowedIp);
                    $pattern = str_replace('*', '.*', $pattern);
                    if (preg_match('/^' . $pattern . '$/', $clientIp)) {
                        $isWhitelisted = true;
                        break;
                    }
                }
                // Check for CIDR notation (e.g., 192.168.1.0/24)
                if (strpos($allowedIp, '/') !== false) {
                    if ($this->ipInCidr($clientIp, $allowedIp)) {
                        $isWhitelisted = true;
                        break;
                    }
                }
            }
        }

        // If IP is NOT whitelisted, check daily limit for this user (across all non-whitelisted IPs)
        if (!$isWhitelisted) {
            // Get today's date for limit check
            $today = Carbon::today()->toDateString();

            // Get total SMS count for this user across ALL non-whitelisted IPs today
            $todayCount = SmsIpDailyLimit::where('user_id', $userDetail->user_id)
                ->where('limit_date', $today)
                ->sum('sms_count');

            // Calculate how many SMS the user wants to send in this request
            $allContacts = explode(',', $request->contacts);
            $requestedSmsCount = count($allContacts);

            // Check if adding this request would exceed daily limit
            if (($todayCount + $requestedSmsCount) > $dailyLimit) {
                $remaining = $dailyLimit - $todayCount;
                if ($remaining < 0) $remaining = 0;

                $message = 'Daily limit exceeded. Your account can send maximum ' . $dailyLimit . ' SMS per day from non-whitelisted IPs. Remaining today: ' . $remaining;
                $this->finishApiLog($apiLog, $startedAt, 'error', '445180', $message, $userDetail->user_id);

                return response()->json([
                    'code' => '445180',
                    'message' => $message
                ]);
            }
        }

        $user = User::where('id', $userDetail->user_id)->first();
        if ($user->status == 2) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445050', 'Your account was suspended', $user->id);
            return response()->json(['code'=>'445050', 'message'=>'Your account was suspended']);
        } elseif ($user->status == 3) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445060', 'Your account was expired', $user->id);
            return response()->json(['code'=>'445060', 'message'=>'Your account was expired']);
        } elseif ($user->role != 5) {
            if (!$request->for_registration) {
                $this->finishApiLog($apiLog, $startedAt, 'error', '445070', 'Only a user can send sms', $user->id);
                return response()->json(['code'=>'445070', 'message'=>'Only a user can send sms']);
            } elseif (($request->for_registration != 'resellerToUser') && ($request->for_registration != 'adminToReseller')) {
                $this->finishApiLog($apiLog, $startedAt, 'error', '445071', 'Only a user can send sms', $user->id);
                return response()->json(['code'=>'445071', 'message'=>'Only a user can send sms']);
            }
        }

        /*check sender id*/
        $sender = SenderIdRegister::where('sir_sender_id', $request->senderid)->first();
        if (!$sender) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445080', 'Invalid sender id', $user->id);
            return response()->json(['code'=>'445080', 'message'=>'Invalid sender id']);
        }

        $checkSenderUser = SenderIdUser::where(['user_id'=>$user->id, 'sender_id'=>$sender->id])->first();
        if (!$checkSenderUser) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445090', 'You have no access to this sender id', $user->id);
            return response()->json(['code'=>'445090', 'message'=>'You have no access to this sender id']);
        }

        /*check and get numbers*/
        $allContacts = explode(',', $request->contacts);
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
            $this->finishApiLog($apiLog, $startedAt, 'error', '445110', 'All numbers are invalid', $user->id);
            return response()->json(['code'=>'445110', 'message'=>'All numbers are invalid']);
        }

        /*sms count*/
        if (\SmsHelper::is_unicode($request->msg)) {
            $smsType = 'unicode';
            $sms_number = \SmsHelper::unicode_sms_count($request->msg);
        } else {
            $smsType = 'text';
            $sms_number = \SmsHelper::text_sms_count($request->msg);
        }

        $senderIdValue = trim($sender->sir_sender_id);

        if (is_numeric($senderIdValue)) {
            // Check if it's a valid operator number (MNO)
            $checkNumber = $senderIdValue;
            if (substr($checkNumber, 0, 2) == '88') {
                $checkNumber = substr($checkNumber, 2);
            }
            
            $isValidOperator = (
                substr($checkNumber, 0, 3) == '018' || 
                substr($checkNumber, 0, 3) == '016' ||
                substr($checkNumber, 0, 3) == '017' || 
                substr($checkNumber, 0, 3) == '013' ||
                substr($checkNumber, 0, 3) == '019' || 
                substr($checkNumber, 0, 3) == '014' ||
                substr($checkNumber, 0, 3) == '015'
            );
            
            if ($isValidOperator) {
                $isMasking = false;      // MNO rate (non-masking)
                $sms_masking_type = '1';
            } else {
                $isMasking = 'iptsp';    // IPTSP rate
                $sms_masking_type = '1';
            }
        } else {
            $isMasking = true;           // Masking rate (alphanumeric)
            $sms_masking_type = '2';
        }

        // ========== END FIX ==========

        $total_cost = \BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $user->id);

        if (\BalanceHelper::user_available_balance($user->id) < $total_cost) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445120', 'Not enough balance', $user->id);
            return response()->json(['code'=>'445120', 'message'=>'You haven\'t enough balance . please recharge first...']);
        } elseif (\BalanceHelper::check_parents_available_balance($user->id, $sms_number, $validUniqueNumbers, $isMasking) == false) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445130', 'Parent balance not enough', $user->id);
            return response()->json(['code'=>'445130', 'message'=>'Your reseller don\'t have enough balance . told him to recharge first...']);
        } elseif ($isMasking == true && mb_strlen($request->msg) > 500) {
            $this->finishApiLog($apiLog, $startedAt, 'error', '445160', 'Message character limit exceeded', $user->id);
            return response()->json(['code' => '445160', 'message' => 'Message character limit exceeded. Maximum 500 characters allowed.']);
        } else {
            try {
                $campaign_id = $user->id . time() . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9);
          if ($isMasking === true) {
                $sms_masking_type = '2';
            }
                $current_date = Carbon::now()->toDateTimeString();

                $sms_sender_op = null;

                if ($sms_masking_type == '1' && (substr($sender->sir_sender_id, 0, 5) == '88018' || substr($sender->sir_sender_id, 0, 5) == '88016')) {
                    $sms_sender_op = 1;
                } elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id, 0, 5) == '88017' || substr($sender->sir_sender_id, 0, 5) == '88013')) {
                    $sms_sender_op = 2;
                } elseif ($sms_masking_type == '1' && (substr($sender->sir_sender_id, 0, 5) == '88019' || substr($sender->sir_sender_id, 0, 5) == '88014')) {
                    $sms_sender_op = 3;
                } elseif ($sms_masking_type == '1' && substr($sender->sir_sender_id, 0, 5) == '88015') {
                    $sms_sender_op = 4;
                }

                foreach ($validUniqueNumbers as $value) {
                    $op = \PhoneNumber::checkOperator($value);
                }
$total_sms_number = $sms_number * count($validUniqueNumbers);
                $insertCampaign = SmsCampaignId::create([
                    'user_id' => $user->id,
                    'sender_id' => $sender->id,
                    'sci_campaign_id' => $campaign_id,
                    'sci_total_submitted' => count($validUniqueNumbers),
                    'sci_total_cost' => $total_cost,
                    'sci_campaign_type' => '1',
                    'sci_deal_type' => '1',
                    'sci_sms_type' =>  $sms_masking_type,
                    'sci_sender_operator' => $sms_sender_op,
                    'sci_dynamic_type' => '0',
                    'sci_targeted_time' => $current_date,
                    'sci_browser' => $request->header('User-Agent'),
                    'sci_mac_address' => null,
                    'sci_ip_address' => $request->ip(),
                    'sci_from_api' => 1,
                ]);

                $insertCount = 0;
                $dataForInsert = array();
                $serial = 0;
                foreach ($validUniqueNumbers as $number) {
                    $operator = \PhoneNumber::checkOperator($number);

                    $messageWithoutEmojis = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', '', $request->msg);

                    $dataForInsert[] = array(
                        'user_id' => $user->id,
                        'sender_id' => $sender->id,
                        'campaign_id' => $insertCampaign->id,
                        'scp_cell_no' => $number,
                        'scp_message' => $messageWithoutEmojis,
                        'scp_sms_cost' => \BalanceHelper::singleSmsCost($sms_number, $number, $isMasking, $user->id),
                        'operator_id' => $operator['id'],
                        'scp_campaign_type' => '1',
                        'scp_deal_type' => '1',
                        'scp_sms_type' => $sms_masking_type,
                        'scp_sms_id' => '0',
                        'scp_tried' => '0',
                        'scp_picked' => '0',
                        'scp_sms_text_type' => $smsType,
                        'scp_target_time' => $current_date,
                        'scp_status' => '1',
                        'created_at' => $current_date,
                        'updated_at' => $current_date,
                    );
                    if ($insertCount < 20) {
                        $insertCount++;
                    } else {
                        SmsCamPending::insert($dataForInsert);
                        $dataForInsert = array();
                        $insertCount = 0;
                    }
                }
                SmsCamPending::insert($dataForInsert);

                // ========== STORE DAILY LIMIT RECORD FOR NON-WHITELISTED IP ==========
                if (!$isWhitelisted) {
                    SmsIpDailyLimit::create([
                        'ip_address' => $clientIp,
                        'user_id' => $userDetail->user_id,
                        'sms_count' => count($validUniqueNumbers),
                        'limit_date' => Carbon::today()->toDateString()
                    ]);
                }

                /*debit user balance*/
                $user_position = $user->position;
                $user_id = $user->id;

                $user_det = User::where('id', $user_id)->first();

                while ($user_position >= 1) {
                    /*get total cost*/
                    foreach ($validUniqueNumbers as $value) {
                        $op = \PhoneNumber::checkOperator($value);
                    }

                    $campaign_cost = \BalanceHelper::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $user_det->id);

                    AccSmsBalance::create([
                        'asb_paid_by' => $user_det->create_by,
                        'asb_pay_to' => $user_det->id,
                        'asb_pay_ref' => $campaign_id,
                        'asb_credit' => '0',
                        'asb_debit' => $campaign_cost,
                        'asb_submit_time' => $current_date,
                        'asb_target_time' => $current_date,
                        'asb_pay_mode' => '4',
                        'asb_payment_status' => '1',
                        'asb_deal_type' => '2',
                        'credit_return_type' => '0',
                    ]);

                    $user_det = User::where('id', $user_det->create_by)->first();
                    $user_position = $user_det->position;
                }

                /*add user credit history*/
                AccUserCreditHistory::create([
                    'campaign_id' => $insertCampaign->id,
                    'user_id' => $user->id,
                    'uch_sms_count' => $sms_number,
                    'uch_sms_cost' => $total_cost,
                ]);

                $this->finishApiLog($apiLog, $startedAt, 'success', '445000', null, $user->id);

                return response()->json([
                    'code'=>'445000',
                    'message'=>'Message has been sent...',
                    'campaign_id'=>$campaign_id
                ]);
            } catch (\Exception $e) {
                $this->finishApiLog(
                    $apiLog,
                    $startedAt,
                    'error',
                    '445150',
                    $e->getMessage(),
                    isset($user) ? $user->id : null
                );

                return response()->json(['code'=>'445150', 'message'=>'Something was wrong to sent sms. please contact with admin!!! ..'.$e->getMessage()]);
            }
        }
    }

    /**
     * Check if an IP is within a CIDR range
     */
    private function ipInCidr($ip, $cidr)
    {
        list($subnet, $mask) = explode('/', $cidr);
        $subnet = ip2long($subnet);
        $ip = ip2long($ip);
        $mask = -1 << (32 - $mask);
        $subnet &= $mask;

        return ($ip & $mask) == $subnet;
    }

    private function createApiLog(Request $request, $apiName)
    {
        try {
            return ApiLog::create([
                'api_name' => $apiName,
                'ip_address' => $request->ip(),
                'contacts_count' => $request->contacts ? count(explode(',', $request->contacts)) : 0,
                'status' => 'processing',
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function finishApiLog($apiLog, $startedAt, $status, $responseCode, $errorMessage = null, $userId = null)
    {
        if (!$apiLog) {
            return;
        }

        try {
            $apiLog->update([
                'user_id' => $userId,
                'status' => $status,
                'response_code' => $responseCode,
                'error_message' => $errorMessage,
                'processing_time_ms' => round((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (\Exception $e) {
            // Do not stop API if log update fails
        }
    }

    /*sms campaign report using api*/
    public function smsCampaignReport(Request $request)
    {
        if (!$request->api_key) {
            return response()->json(['code'=>'445010', 'message'=>'Missing api key']);
        } elseif (!$request->campaign_id) {
            return response()->json(['code'=>'445190', 'message'=>'Missing campaign id']);
        }
        $userDetail = UserDetail::where('api_key', $request->api_key)->first();
        if (!$userDetail) {
            return response()->json(['code'=>'445040', 'message'=>'Invalid api key']);
        }
        $user = User::where('id', $userDetail->user_id)->first();
        if (!$user) {
            return response()->json(['code'=>'445040', 'message'=>'Invalid api key']);
        } elseif ($user->status == 2) {
            return response()->json(['code'=>'445050', 'message'=>'Your account was suspended']);
        } elseif ($user->status == 3) {
            return response()->json(['code'=>'445060', 'message'=>'Your account was expired']);
        }
        $campaign = SmsCampaignId::where('user_id', $user->id)
            ->where('sci_campaign_id', $request->campaign_id)
            ->first();
        if (!$campaign) {
            return response()->json(['code'=>'445200', 'message'=>'Campaign not found']);
        }
        $sentReportsQuery = SmsCampaign_24h::where('campaign_id', $campaign->id);
        $pendingReportsQuery = SmsCamPending::where('campaign_id', $campaign->id);
        if ($request->contacts) {
            $contacts = array_filter(array_map('trim', explode(',', $request->contacts)));
            $numbers = array();
            foreach ($contacts as $contact) {
                $numbers[] = \PhoneNumber::addNumberPrefix($contact);
            }
            $numbers = array_unique($numbers);
            $sentReportsQuery->whereIn('sct_cell_no', $numbers);
            $pendingReportsQuery->whereIn('scp_cell_no', $numbers);
        }
        $sentReports = $sentReportsQuery->orderBy('id', 'asc')->get();
        $pendingReports = $pendingReportsQuery->orderBy('id', 'asc')->get();
        $reports = array();
        $successCount = 0;
        $failedCount = 0;
        $pendingCount = count($pendingReports);
        foreach ($sentReports as $report) {
            $gatewayStatus = strtoupper((string) $report->sct_status);
            $deliveryReport = strtoupper((string) $report->sct_delivery_report);
            $isSuccess = in_array($gatewayStatus, ['SUCCESS', '1000', '0']) || in_array($deliveryReport, ['DELIVERED', 'SUCCESS']);
            if ($isSuccess) {
                $successCount++;
            } else {
                $failedCount++;
            }
            $reports[] = array(
                'number' => $report->sct_cell_no,
                'message' => $report->sct_message,

                'send_status' => 'sent',
                'success' => $isSuccess,
                'gateway_status' => $report->sct_status,
                'delivery_report' => $report->sct_delivery_report,
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
            );
        }
        foreach ($pendingReports as $report) {
            $reports[] = array(
                'number' => $report->scp_cell_no,
                'message' => $report->scp_message,

                'send_status' => 'pending',
                'success' => false,
                'gateway_status' => $report->scp_status,
                'delivery_report' => 'PENDING',
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
            );
        }
        return response()->json(array(
            'code' => '445000',
            'campaign_id' => $campaign->sci_campaign_id,
            'summary' => array(
                'submitted' => $campaign->sci_total_submitted,
                'sent' => count($sentReports),
                'success' => $successCount,
                'failed' => $failedCount,
                'pending' => $pendingCount,
            ),
            'reports' => $reports,
        ));
    }

    /*show user balance using api*/
    public function showBalance(Request $request)
    {
        /*validate data*/
        if (!$request->api_key) {
            return response()->json(['code'=>'445010', 'message'=>'Missing api key']);
        }
        /*check api*/
        $userDetail = UserDetail::where('api_key', $request->api_key)->first();
        if (!$userDetail) {
            return response()->json(['code'=>'445040', 'message'=>'Invalid api key']);
        }

        $user = User::where('id', $userDetail->user_id)->first();
        if ($user->status == 2) {
            return response()->json(['code'=>'445050', 'message'=>'Your account was suspended']);
        } elseif ($user->status == 3) {
            return response()->json(['code'=>'445060', 'message'=>'Your account was expired']);
        }

        try {
            $userAvailableBalance = \BalanceHelper::user_available_balance($user->id);
            return response()->json(['code'=>'445000', 'balance'=>number_format($userAvailableBalance, 2)." tk"]);
        } catch (\Exception $e) {
            return response()->json(['code'=>'445160', 'message'=>'Something was wrong to check balance. please contact with admin!!! ..']);
        }
    }
}