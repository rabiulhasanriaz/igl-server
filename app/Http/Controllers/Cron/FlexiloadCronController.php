<?php

namespace App\Http\Controllers\Cron;

use App\Model\LoadCampaign30day;
use App\Model\LoadSimAvailablleBalance;
use App\Model\LoadSimMessages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LoadCamPending;
use App\Model\LoadPackage;
use Illuminate\Support\Facades\Log;
use App\Model\LoadCampaign;
use App\Model\LoadCampaign24;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
class FlexiloadCronController extends Controller
{
public function sendFlexiload()
{
    $operatorMapping = [
        'blink'    => 'bl',
        'gp'       => 'gp',
        'robi'     => 'rb',
        'airtel'   => 'at',
        'teletalk' => 'tt',
    ];

    // Take 5 pending loads at a time
    $pending_loads = LoadCamPending::where('status', 0)
        ->orderBy('id', 'asc')
        ->take(5)
        ->get();

    $user_id = "01958666900";
    $password = "666999";

    foreach ($pending_loads as $pending_load) {
        // COMPLETELY ISOLATE EACH OPERATOR PROCESSING
        try {
            $this->processIndividualOperator(
                $pending_load, 
                $operatorMapping, 
                $user_id, 
                $password
            );
        } catch (\Exception $e) {
            // Only mark this specific operator as failed, continue with others
            \Log::error("Operator {$pending_load->operator_id} failed: " . $e->getMessage());
            $pending_load->update(['status' => 1]);
            continue;
        }
    }

    // ------------------ Operator pending counts ------------------
    $operatorPendings = [
        'airtel'   => LoadCamPending::where('operator_id', 'airtel')->count(),
        'gp'       => LoadCamPending::where('operator_id', 'gp')->count(),
        'blink'    => LoadCamPending::where('operator_id', 'blink')->count(),
        'robi'     => LoadCamPending::where('operator_id', 'robi')->count(),
        'teletalk' => LoadCamPending::where('operator_id', 'teletalk')->count(),
    ];

    return view('cron.sendPendingLoad', [
        'rest_pendings'   => LoadCamPending::count(),
        'airtel_pending'  => $operatorPendings['airtel'],
        'gp_pending'      => $operatorPendings['gp'],
        'bl_pending'      => $operatorPendings['blink'],
        'robi_pending'    => $operatorPendings['robi'],
        'tt_pending'      => $operatorPendings['teletalk'],
    ]);
}

private function processIndividualOperator($pending_load, $operatorMapping, $user_id, $password)
{
    $operator = $pending_load->operator_id;
    $apiOperator = $operatorMapping[$operator] ?? $operator;
    $targeted_number = $pending_load->targeted_number;
    $flexiload_price = $pending_load->campaign_price;
    $number_type     = $pending_load->number_type;
    $sms_id          = $pending_load->sms_id;
    $offer_id        = $pending_load->offer_id;
    $campaign_type   = $pending_load->campaign_type;

    if (in_array($operator, ['gp', 'airtel', 'robi', 'teletalk'])) {
        $number_type = 1; // force prepaid
    }
    $rechargeType = ($number_type == 2) ? 'postpaid' : 'prepaid';
    
    DB::beginTransaction();
    try {
        $response = null;
        $apiNumber = preg_replace('/^88/', '', $targeted_number);
        $lastDigit = substr((string)$flexiload_price, -1);

        $isSuccess = false;
        $trx_id = '';
        $message_text = '';

        if (!in_array($lastDigit, ['0', '5']) && empty($offer_id)) {
            if ($operator == 'blink') {
                // ---- Try Banglalink offer ----
                $offerApiUrl = "http://103.86.193.25:9090/api/offer?" . http_build_query([
                    "user_id"  => $user_id,
                    "password" => $password,
                    "number"   => $apiNumber,
                    "amount"   => $flexiload_price,
                    "refer_id" => $sms_id,
                    "operator" => $apiOperator,
                ]);

                $offerApiResponse = json_decode(@file_get_contents($offerApiUrl), true) ?? [];
                $matchedOffer = null;
                if (!empty($offerApiResponse['offers'])) {
                    foreach ($offerApiResponse['offers'] as $offer) {
                        if (($offer['offerId'] ?? '') === $offer_id) {
                            $matchedOffer = $offer;
                            break;
                        }
                    }
                }

                if ($matchedOffer) {
                    $simNumber = $offerApiResponse['sim_number'] ?? $apiNumber;
                    $tranId    = $matchedOffer['tranId'] ?? '';
                    $ussdCode  = $matchedOffer['ussdCode'] ?? '';
                    $validity  = $matchedOffer['validity'] ?? '';

                    $sendToUrl = "http://103.86.193.25:9090/api/offer/recharge?" . http_build_query([
                        "user_id"    => $user_id,
                        "password"   => $password,
                        "number"     => $apiNumber,
                        "amount"     => $flexiload_price,
                        "refer_id"   => $sms_id,
                        "operator"   => $apiOperator,
                        "sim_number" => $simNumber,
                        "offer_id"   => $offer_id,
                        "tran_id"    => $tranId,
                        "ussd_code"  => $ussdCode,
                        "validity"   => $validity,
                    ]);

                    $response = json_decode(@file_get_contents($sendToUrl));
                }
            }
            elseif ($operator == 'robi') {
                $offerApiUrl = "http://103.86.193.25:9090/api/offer?" . http_build_query([
                    "user_id"  => $user_id,
                    "password" => $password,
                    "number"   => $apiNumber,
                    "amount"   => $flexiload_price,
                    "refer_id" => $sms_id,
                    "operator" => $apiOperator,
                ]);

                $offerApiResponse = json_decode(@file_get_contents($offerApiUrl), true) ?? [];
                $matchedOffer = null;
                if (!empty($offerApiResponse['offers'])) {
                    foreach ($offerApiResponse['offers'] as $offer) {
                        if (($offer['offerId'] ?? '') === $offer_id) {
                            $matchedOffer = $offer;
                            break;
                        }
                    }
                }

                if ($matchedOffer) {
                    $simNumber = $offerApiResponse['sim_number'] ?? $apiNumber;
                    $tranId    = $matchedOffer['tranId'] ?? '';
                    $ussdCode  = $matchedOffer['ussdCode'] ?? '';
                    $validity  = $matchedOffer['validity'] ?? '';

                    $sendToUrl = "http://103.86.193.25:9090/api/offer/recharge?" . http_build_query([
                        "user_id"    => $user_id,
                        "password"   => $password,
                        "number"     => $apiNumber,
                        "amount"     => $flexiload_price,
                        "refer_id"   => $sms_id,
                        "operator"   => $apiOperator,
                        "sim_number" => $simNumber,
                        "offer_id"   => $offer_id,
                        "tran_id"    => $tranId,
                        "ussd_code"  => $ussdCode,
                        "validity"   => $validity,
                    ]);

                    $response = json_decode(@file_get_contents($sendToUrl));
                }
            }
            elseif ($operator == 'airtel') {
                $offerApiUrl = "http://103.86.193.25:9090/api/offer?" . http_build_query([
                    "user_id"  => $user_id,
                    "password" => $password,
                    "number"   => $apiNumber,
                    "amount"   => $flexiload_price,
                    "refer_id" => $sms_id,
                    "operator" => $apiOperator,
                ]);

                $offerApiResponse = json_decode(@file_get_contents($offerApiUrl), true) ?? [];
                $matchedOffer = null;
                if (!empty($offerApiResponse['offers'])) {
                    foreach ($offerApiResponse['offers'] as $offer) {
                        if (($offer['offerId'] ?? '') === $offer_id) {
                            $matchedOffer = $offer;
                            break;
                        }
                    }
                }

                if ($matchedOffer) {
                    $simNumber = $offerApiResponse['sim_number'] ?? $apiNumber;
                    $tranId    = $matchedOffer['tranId'] ?? '';
                    $ussdCode  = $matchedOffer['ussdCode'] ?? '';
                    $validity  = $matchedOffer['validity'] ?? '';

                    $sendToUrl = "http://103.86.193.25:9090/api/offer/recharge?" . http_build_query([
                        "user_id"    => $user_id,
                        "password"   => $password,
                        "number"     => $apiNumber,
                        "amount"     => $flexiload_price,
                        "refer_id"   => $sms_id,
                        "operator"   => $apiOperator,
                        "sim_number" => $simNumber,
                        "offer_id"   => $offer_id,
                        "tran_id"    => $tranId,
                        "ussd_code"  => $ussdCode,
                        "validity"   => $validity,
                    ]);

                    $response = json_decode(@file_get_contents($sendToUrl));
                }
            } 
            elseif ($operator == 'teletalk') {
                $offerApiUrl = "http://103.86.193.25:9090/api/offer?" . http_build_query([
                    "user_id"  => $user_id,
                    "password" => $password,
                    "number"   => $apiNumber,
                    "amount"   => $flexiload_price,
                    "refer_id" => $sms_id,
                    "operator" => $apiOperator,
                ]);

                $offerApiResponse = json_decode(@file_get_contents($offerApiUrl), true) ?? [];
                $matchedOffer = null;
                if (!empty($offerApiResponse['offers'])) {
                    foreach ($offerApiResponse['offers'] as $offer) {
                        if (($offer['offerId'] ?? '') === $offer_id) {
                            $matchedOffer = $offer;
                            break;
                        }
                    }
                }

                if ($matchedOffer) {
                    $simNumber = $offerApiResponse['sim_number'] ?? $apiNumber;
                    $tranId    = $matchedOffer['tranId'] ?? '';
                    $ussdCode  = $matchedOffer['ussdCode'] ?? '';
                    $validity  = $matchedOffer['validity'] ?? '';

                    $sendToUrl = "http://103.86.193.25:9090/api/offer/recharge?" . http_build_query([
                        "user_id"    => $user_id,
                        "password"   => $password,
                        "number"     => $apiNumber,
                        "amount"     => $flexiload_price,
                        "refer_id"   => $sms_id,
                        "operator"   => $apiOperator,
                        "sim_number" => $simNumber,
                        "offer_id"   => $offer_id,
                        "tran_id"    => $tranId,
                        "ussd_code"  => $ussdCode,
                        "validity"   => $validity,
                    ]);

                    $response = json_decode(@file_get_contents($sendToUrl));
                }
            } 
            elseif (in_array($operator, ['gp', 'grameen'])) {
                $offerApiUrl = "http://103.86.193.25:9090/api/powerload?" . http_build_query([
                    "user_id"  => $user_id,
                    "password" => $password,
                    "number"   => $apiNumber,
                    "amount"   => $flexiload_price,
                    "refer_id" => $sms_id,
                    "operator" => 'grameen',
                    "type"     => 'offer',
                ]);

                $offerApiResponse = json_decode(@file_get_contents($offerApiUrl), true) ?? [];
                
                $matchedOffer = null;
                if (!empty($offerApiResponse['offers'])) {
                    foreach ($offerApiResponse['offers'] as $offer) {
                        if (($offer['offerId'] ?? '') === $offer_id) {
                            $matchedOffer = $offer;
                            break;
                        }
                    }
                }

                if ($matchedOffer) {
                    $simNumber = $offerApiResponse['sim_number'] ?? $apiNumber;
                    $campaignId      = $matchedOffer['campaignID'] ?? '';
                    $optInKeyword    = $matchedOffer['optInKeyword'] ?? '';
                    $retailerCommission = $matchedOffer['retailerCommission'] ?? 0;
                    $sessionContext = $matchedOffer['sessionContext'] ?? $offerApiResponse['sessionContext'] ?? '';

                    $sendToUrl = "http://103.86.193.25:9090/api/powerload/submit?" . http_build_query([
                        "user_id"            => $user_id,
                        "password"           => $password,
                        "number"             => $apiNumber,
                        "amount"             => $flexiload_price,
                        "sim_number"         => $simNumber,
                        "campaign_id"        => $campaignId,
                        "offer_id"           => $offer_id,
                        "opt_in_keyword"     => $optInKeyword,
                        "retailer_commission"=> $retailerCommission,
                        "refer_id"           => $sms_id,
                        "sessionContext"     => $sessionContext,
                    ]);

                    $response = json_decode(@file_get_contents($sendToUrl));
                }
            }

            // if still no response or no matched offer, fallback to regular recharge
            if (!$response) {
                $sendToUrl = "http://103.86.193.25:9090/api/recharge?" . http_build_query([
                    "user_id"       => $user_id,
                    "password"      => $password,
                    "number"        => $targeted_number,
                    "amount"        => $flexiload_price,
                    "refer_id"      => $sms_id,
                    "operator"      => $apiOperator,
                    "recharge_type" => $rechargeType,
                ]);
                $response = json_decode(@file_get_contents($sendToUrl));
            }
        }

        // ------------------ Banglalink Offer Recharge ------------------
        elseif ($operator == 'blink' && $offer_id && $campaign_type == 2) {
            $offerApiUrl = "http://103.86.193.25:9090/api/offer?" . http_build_query([
                "user_id"  => $user_id,
                "password" => $password,
                "number"   => $apiNumber,
                "amount"   => $flexiload_price,
                "refer_id" => $sms_id,
                "operator" => $apiOperator,
            ]);

            $offerApiResponse = json_decode(@file_get_contents($offerApiUrl), true) ?? [];
            $matchedOffer = null;
            if (!empty($offerApiResponse['offers'])) {
                foreach ($offerApiResponse['offers'] as $offer) {
                    if (($offer['offerId'] ?? '') === $offer_id) {
                        $matchedOffer = $offer;
                        break;
                    }
                }
            }

            if ($matchedOffer) {
                $simNumber = $offerApiResponse['sim_number'] ?? $apiNumber;
                $tranId    = $matchedOffer['tranId'] ?? '';
                $ussdCode  = $matchedOffer['ussdCode'] ?? '';
                $validity  = $matchedOffer['validity'] ?? '';

                $sendToUrl = "http://103.86.193.25:9090/api/offer/recharge?" . http_build_query([
                    "user_id"    => $user_id,
                    "password"   => $password,
                    "number"     => $apiNumber,
                    "amount"     => $flexiload_price,
                    "refer_id"   => $sms_id,
                    "operator"   => $apiOperator,
                    "sim_number" => $simNumber,
                    "offer_id"   => $offer_id,
                    "tran_id"    => $tranId,
                    "ussd_code"  => $ussdCode,
                    "validity"   => $validity,
                ]);

                $response = json_decode(@file_get_contents($sendToUrl));
            } else {
                // No matched offer => mark failed
                $pending_load->update(['status' => 1]);
                DB::commit();
                return;
            }
        }
        // ------------------ Airtel Offer Recharge ------------------
        elseif ($operator == 'airtel' && $offer_id && $campaign_type == 2) {
            $offerApiUrl = "http://103.86.193.25:9090/api/offer?" . http_build_query([
                "user_id"  => $user_id,
                "password" => $password,
                "number"   => $apiNumber,
                "amount"   => $flexiload_price,
                "refer_id" => $sms_id,
                "operator" => $apiOperator,
            ]);

            $offerApiResponse = json_decode(@file_get_contents($offerApiUrl), true) ?? [];
            $matchedOffer = null;
            if (!empty($offerApiResponse['offers'])) {
                foreach ($offerApiResponse['offers'] as $offer) {
                    if (($offer['offerId'] ?? '') === $offer_id) {
                        $matchedOffer = $offer;
                        break;
                    }
                }
            }

            if ($matchedOffer) {
                $simNumber = $offerApiResponse['sim_number'] ?? $apiNumber;
                $tranId    = $matchedOffer['tranId'] ?? '';
                $ussdCode  = $matchedOffer['ussdCode'] ?? '';
                $validity  = $matchedOffer['validity'] ?? '';

                $sendToUrl = "http://103.86.193.25:9090/api/offer/recharge?" . http_build_query([
                    "user_id"    => $user_id,
                    "password"   => $password,
                    "number"     => $apiNumber,
                    "amount"     => $flexiload_price,
                    "refer_id"   => $sms_id,
                    "operator"   => $apiOperator,
                    "sim_number" => $simNumber,
                    "offer_id"   => $offer_id,
                    "tran_id"    => $tranId,
                    "ussd_code"  => $ussdCode,
                    "validity"   => $validity,
                ]);

                $response = json_decode(@file_get_contents($sendToUrl));
            } else {
                // No matched offer => mark failed
                $pending_load->update(['status' => 1]);
                DB::commit();
                return;
            }
        }
        // ------------------ Robi Offer Recharge ------------------
        elseif ($operator == 'robi' && $offer_id && $campaign_type == 2) {
            $offerApiUrl = "http://103.86.193.25:9090/api/offer?" . http_build_query([
                "user_id"  => $user_id,
                "password" => $password,
                "number"   => $apiNumber,
                "amount"   => $flexiload_price,
                "refer_id" => $sms_id,
                "operator" => $apiOperator,
            ]);

            $offerApiResponse = json_decode(@file_get_contents($offerApiUrl), true) ?? [];
            $matchedOffer = null;
            if (!empty($offerApiResponse['offers'])) {
                foreach ($offerApiResponse['offers'] as $offer) {
                    if (($offer['offerId'] ?? '') === $offer_id) {
                        $matchedOffer = $offer;
                        break;
                    }
                }
            }

            if ($matchedOffer) {
                $simNumber = $offerApiResponse['sim_number'] ?? $apiNumber;
                $tranId    = $matchedOffer['tranId'] ?? '';
                $ussdCode  = $matchedOffer['ussdCode'] ?? '';
                $validity  = $matchedOffer['validity'] ?? '';

                $sendToUrl = "http://103.86.193.25:9090/api/offer/recharge?" . http_build_query([
                    "user_id"    => $user_id,
                    "password"   => $password,
                    "number"     => $apiNumber,
                    "amount"     => $flexiload_price,
                    "refer_id"   => $sms_id,
                    "operator"   => $apiOperator,
                    "sim_number" => $simNumber,
                    "offer_id"   => $offer_id,
                    "tran_id"    => $tranId,
                    "ussd_code"  => $ussdCode,
                    "validity"   => $validity,
                ]);

                $response = json_decode(@file_get_contents($sendToUrl));
            } else {
                // No matched offer => mark failed
                $pending_load->update(['status' => 1]);
                DB::commit();
                return;
            }
        }
        // ------------------ Teletalk Offer Recharge ------------------
        elseif ($operator == 'teletalk' && $offer_id && $campaign_type == 2) {
            $offerApiUrl = "http://103.86.193.25:9090/api/offer?" . http_build_query([
                "user_id"  => $user_id,
                "password" => $password,
                "number"   => $apiNumber,
                "amount"   => $flexiload_price,
                "refer_id" => $sms_id,
                "operator" => $apiOperator,
            ]);

            $offerApiResponse = json_decode(@file_get_contents($offerApiUrl), true) ?? [];
            $matchedOffer = null;
            if (!empty($offerApiResponse['offers'])) {
                foreach ($offerApiResponse['offers'] as $offer) {
                    if (($offer['offerId'] ?? '') === $offer_id) {
                        $matchedOffer = $offer;
                        break;
                    }
                }
            }

            if ($matchedOffer) {
                $simNumber = $offerApiResponse['sim_number'] ?? $apiNumber;
                $tranId    = $matchedOffer['tranId'] ?? '';
                $ussdCode  = $matchedOffer['ussdCode'] ?? '';
                $validity  = $matchedOffer['validity'] ?? '';

                $sendToUrl = "http://103.86.193.25:9090/api/offer/recharge?" . http_build_query([
                    "user_id"    => $user_id,
                    "password"   => $password,
                    "number"     => $apiNumber,
                    "amount"     => $flexiload_price,
                    "refer_id"   => $sms_id,
                    "operator"   => $apiOperator,
                    "sim_number" => $simNumber,
                    "offer_id"   => $offer_id,
                    "tran_id"    => $tranId,
                    "ussd_code"  => $ussdCode,
                    "validity"   => $validity,
                ]);

                $response = json_decode(@file_get_contents($sendToUrl));
            } else {
                // No matched offer => mark failed
                $pending_load->update(['status' => 1]);
                DB::commit();
                return;
            }
        }
        // ------------------ GP/Grameen Offer Recharge ------------------
        elseif (($operator == 'gp' || $operator == 'grameen') && $offer_id && $campaign_type == 2) {
            $offerApiUrl = "http://103.86.193.25:9090/api/powerload?" . http_build_query([
                "user_id"  => $user_id,
                "password" => $password,
                "number"   => $apiNumber,
                "amount"   => $flexiload_price,
                "refer_id" => $sms_id,
                "operator" => 'grameen',
                "type"     => 'offer',
            ]);

            $offerApiResponse = json_decode(@file_get_contents($offerApiUrl), true) ?? [];
            $matchedOffer = null;
            if (!empty($offerApiResponse['offers'])) {
                foreach ($offerApiResponse['offers'] as $offer) {
                    if (($offer['offerId'] ?? '') === $offer_id) {
                        $matchedOffer = $offer;
                        break;
                    }
                }
            }

            if ($matchedOffer) {
                $simNumber = $offerApiResponse['sim_number'] ?? $apiNumber;
                $campaignId = $matchedOffer['campaignID'] ?? '';
                $optInKeyword = $matchedOffer['optInKeyword'] ?? '';
                $retailerCommission = $matchedOffer['retailerCommission'] ?? 0;
                $sessionContext = $matchedOffer['sessionContext'] ?? $offerApiResponse['sessionContext'] ?? '';

                $sendToUrl = "http://103.86.193.25:9090/api/powerload/submit?" . http_build_query([
                    "user_id"            => $user_id,
                    "password"           => $password,
                    "number"             => $apiNumber,
                    "amount"             => $flexiload_price,
                    "sim_number"         => $simNumber,
                    "campaign_id"        => $campaignId,
                    "offer_id"           => $offer_id,
                    "opt_in_keyword"     => $optInKeyword,
                    "retailer_commission"=> $retailerCommission,
                    "refer_id"           => $sms_id,
                    "sessionContext"     => $sessionContext,
                ]);

                $response = json_decode(@file_get_contents($sendToUrl));
            } else {
                // No matched offer => mark failed
                $pending_load->update(['status' => 1]);
                DB::commit();
                return;
            }
        }
        // ------------------ Regular Recharge ------------------
        else {
            $sendToUrl = "http://103.86.193.25:9090/api/recharge?" . http_build_query([
                "user_id"       => $user_id,
                "password"      => $password,
                "number"        => $operator == 'teletalk' ? $apiNumber : $targeted_number,
                "amount"        => $flexiload_price,
                "refer_id"      => $sms_id,
                "operator"      => $apiOperator,
                "recharge_type" => $rechargeType,
            ]);

            $response = json_decode(@file_get_contents($sendToUrl));
        }

        // ------------------ Process response ------------------
        if (isset($response->status) && $response->status === true && isset($response->result) && $response->result === true) {
            // Extract transaction ID from API response
            $trx_id = $response->tranId ?? $response->transaction_id ?? null;
            $simNumber = $response->sim_number ?? null;
            $operator = $response->operator ?? null;

            // Only proceed if transaction ID exists
            if (!empty($trx_id)) {
                $message_text = $response->message ?? '';
                
                // Format dbOperator to match your blade template format
                switch ($operator) {
                    case 'bl':
                    case 'blink':
                        $dbOperator = 'blink';
                        break;
                    case 'gp':
                    case 'grameen':
                        $dbOperator = 'gp';
                        break;
                    case 'at':
                        $dbOperator = 'airtel';
                        break;
                    case 'rb':
                        $dbOperator = 'robi';
                        break;
                    case 'tt':
                    case 'teletalk':
                        $dbOperator = 'teletalk';
                        break;
                    default:
                        $dbOperator = $pending_load->operator_id; // fallback to original operator
                        break;
                }

                // Save message to LoadSimMessages
                LoadSimMessages::create([
                    'user_id'          => $pending_load->user_id ?? null,
                    'sim_no'           => $simNumber,
                    'operator_company' => $dbOperator,
                    'message'          => $message_text,
                    'sender'           => 'iTopUP',
                    'serial_id'        => $sms_id,
                    'status'           => 1,
                ]);

                if (isset($response->sim_balance)) {
                    // Match API operator with your DB column
                    $operatorColumn = null;
                    switch ($apiOperator) {
                        case 'at':
                            $operatorColumn = 'airtel';
                            break;
                        case 'bl':
                            $operatorColumn = 'blink';
                            break;
                        case 'gp':
                            $operatorColumn = 'gp';
                            break;
                        case 'rb':
                            $operatorColumn = 'robi';
                            break;
                        case 'tt':
                            $operatorColumn = 'teletalk';
                            break;
                    }

                    // If operator matches, update its sim balance
                    if ($operatorColumn) {
                        LoadSimAvailablleBalance::query()->update([
                            $operatorColumn => $response->sim_balance,
                        ]);
                    }
                }
                $campaignData = $pending_load->toArray();
                $campaignData['status'] = 1; // Set status to 1
                // Save to LoadCampaign30day including transaction ID
                LoadCampaign30day::create(array_merge(
                    $pending_load->toArray(),
                    [
                        'transaction_id' => $trx_id,
                        'message'        => $message_text,
                        'status'         => 1
                    ]
                ));

                // Remove pending record
                $pending_load->delete();
                DB::commit();
            } else {
                // No transaction ID => mark failed
                $pending_load->update(['status' => 1]);
                DB::commit();
            }
        } else {
            // API failed => mark failed
            $pending_load->update(['status' => 1]);
            DB::commit();
        }

    } catch (\Exception $e) {
        DB::rollBack();
        // Throw exception to be caught by outer try-catch
        throw $e;
    }
}
public function flexiloadCallback(Request $request)
{


    $referId = $request->input('refer_id');
    $status = strtolower($request->input('status'));
    $transactionId = $request->input('transaction_id');
    $message = $request->input('message');

    if (!$referId) {
        return response()->json(['error' => 'Missing refer_id'], 400);
    }

    // Find the pending record by refer_id (sms_id)
    $pendingLoad = LoadCamPending::where('sms_id', $referId)->first();

    if (!$pendingLoad) {
        return response()->json(['error' => 'Record not found'], 404);
    }

    DB::beginTransaction();

    try {
        // Update the message log
        LoadSimMessages::create([
            'user_id'          => $pendingLoad->user_id ?? null,
            'sim_no'           => $pendingLoad->targeted_number,
            'operator_company' => $pendingLoad->operator_id,
            'message'          => $message ?? 'Callback received',
            'sender'           => 'API_CALLBACK',
            'serial_id'        => $referId,
            'status'           => $status === 'success' ? 1 : 0,
        ]);

        if ($status === 'success') {
            // Move to 30-day success table
            LoadCampaign30day::create(array_merge(
                $pendingLoad->toArray(),
                [
                    'transaction_id' => $transactionId,
                    'message'        => $message ?? 'Recharge Successful'
                ]
            ));

            // Delete from pending
            $pendingLoad->delete();
        } else {
            // If failed, mark as failed but keep in pending table
            $pendingLoad->status = 1; // 1 = failed
            $pendingLoad->save();
        }

        DB::commit();

        return response()->json(['message' => 'Callback processed successfully'], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /**
     * Store incoming Flexiload messages
     */
    public function flexiload_message_store(Request $request)
    {
        if (empty($request->msg)) {
            return response()->json([
                'msg' => 'message empty',
                'status' => 2
            ], 200);
        }

        $simno = $request->sim;
        $opcompany = $request->op;
        $msg = $request->msg;
        $sender = $request->phone;
        $serialid = $request->st;
        $total_message = $msg;

        // Get transaction ID
        try {
            $trx_id = $this->getTransactionIdFromMessage($total_message, $opcompany);
        } catch (\Exception $e) {
            $trx_id = "";
        }

        // Get available balance from message
        try {
            if (strpos($total_message, 'account balance is TK') !== false) {
                $message_with_balance = explode('account balance is TK', $total_message)[1];
                $balance_parts = preg_split('/\s+/', $message_with_balance);
                $available_balance = str_replace(',', '', $balance_parts[1]);
            } elseif (strpos($total_message, 'new balance is') !== false) {
                $message_with_balance = explode('new balance is', $total_message)[1];
                $balance_parts = preg_split('/\s+/', $message_with_balance);
                $available_balance = str_replace(',', '', $balance_parts[1]);
            } elseif (strpos($total_message, 'Your new balance is') !== false) {
                $message_with_balance = explode('Your new balance is', $total_message)[1];
                $balance_parts = preg_split('/\s+/', $message_with_balance);
                $available_balance = str_replace(',', '', $balance_parts[1]);
            } else {
                $available_balance = "";
            }

            $available_balance = rtrim($available_balance, '.');

        } catch (\Exception $e) {
            $available_balance = "";
        }

        try {
            $loadMsg = new \App\Model\LoadSimMessages();
            $loadMsg->sim_no = $simno;
            $loadMsg->operator_company = $opcompany;
            $loadMsg->message = $msg;
            $loadMsg->sender = $sender;
            $loadMsg->serial_id = $serialid;
            $loadMsg->save();

            // Update pending campaign if transaction ID exists
            if (!empty($trx_id) && !empty($serialid)) {
                $loadCamPending = LoadCamPending::where('id', $serialid)->first();
                if (!empty($loadCamPending)) {
                    $loadCamPending->transaction_id = $trx_id;
                    $loadCamPending->save();

                    $loadMsg->user_id = $loadCamPending->user_id;
                    $loadMsg->save();

                    $loadCamPending->sms_id = $loadCamPending->id;
                    LoadCampaign30day::create(json_decode($loadCamPending, true));
                    $loadCamPending->delete();
                }
            }

            // Update SIM balance
            if (!empty($available_balance)) {
                $loadSimBal = \App\Model\LoadSimAvailablleBalance::where('status', 1)->first();
                if (empty($loadSimBal)) {
                    $loadSimBal = new \App\Model\LoadSimAvailablleBalance();
                }

                $loadSimBal->$opcompany = $available_balance;
                $loadSimBal->save();
            }
        } catch (\Exception $e) {
            // handle silently
        }

        return response()->json([
            'data' => 'message inserted successfully',
            'status' => 1,
            'insert' => 1
        ], 200);
    }



    public function testlexiloadReport()
    {
        $flapi_key = "W2SA149S9DQ35JTPX0Z695W8K06Z42O73JDM58XH1Z6W0MQH8D";
        $flapi_userid = "01958666900";
        $postdata = array(
            "id" => "290158071081052",
            "user" => $flapi_userid,
            "key" => $flapi_key
        );
        $sendtourl = "http://rambd.com/sendapi/status";

        $sendtoApi = \SmsHelper::sendFlexiload($sendtourl, $postdata);
        return $sendtoApi;
        json_decode($sendtoApi)->status;
    }

    public function getFlexiloadReport()
    {
        $chengedNumber = 0;
        /*set offsetData in session if wasn't set previous*/
        if (session()->get('flexi_offsetData') == NULL) {
            session(['flexi_offsetData' => 0]);
        }

        /*set goToNullOffset in session if wasn't set previous*/
        if (session()->get('flexi_goToNullOffset') == NULL) {
            session(['flexi_goToNullOffset' => 0]);
        }

        for ($j = 0; $j < 10; $j++) {
            /*set offsetData variable based on session offsetData & goToNullOffset*/
            if (session()->get('flexi_goToNullOffset') == 0) {
                $offsetData = session()->get('flexi_offsetData');
            } else {
                $offsetData = 0;
                session(['flexi_offsetData' => 0]);
                session(['flexi_goToNullOffset' => 0]);
            }

            /*get undelivered numbers*/
            $pendingNumbers = LoadCampaign30day::where(['transaction_id' => NULL])->orWhere(['transaction_id' => ''])->skip($offsetData)->take(5)->get();

            $undeliveredNumber = Null;
            if (count($pendingNumbers) < 5) {
                session(['flexi_goToNullOffset' => 1]);
            }
            if (count($pendingNumbers) > 0) {
                foreach ($pendingNumbers as $pending) {
                    $flapi_key = "W2SA149S9DQ35JTPX0Z695W8K06Z42O73JDM58XH1Z6W0MQH8D";
                    $flapi_userid = "01958666900";
                    $postdata = array(
                        "id" => $pending->sms_id,
                        "user" => $flapi_userid,
                        "key" => $flapi_key
                    );
                    $sendtourl = "http://rambd.com/sendapi/status";

                    $jsonDeliveryReport = \SmsHelper::sendFlexiload($sendtourl, $postdata);
                    $decodeJson = json_decode($jsonDeliveryReport);
                    if (isset($decodeJson->trxid) && ($decodeJson->trxid != "") && ($decodeJson->trxid != NULL)) {
                        $pending->transaction_id = $decodeJson->trxid;
                        $pending->save();
                    } else {
                        session(['flexi_offsetData' => session()->get('flexi_offsetData') + 1]);
                    }

                }

            } else {
                $returnData['no_number'] = "no number available for check report";
            }

            if (session()->get('flexi_goToNullOffset') == 1) {
                break;
            }

        }

        $returnData['still_pending'] = session('flexi_offsetData');
        $returnData['check_complete'] = session('flexi_goToNullOffset');
        $returnData['changed'] = $chengedNumber;

        return view('cron.flexiload-report', compact('returnData'));
    }


    public function flexiload_pending(Request $request)
    {

        $simno = $request->simno;
        $opcompany = $request->company;
        $simbalance = $request->simbal;

        if ($opcompany == 2) {
            $opcompany = "banglalink";
        }

        //only pending data find
        $pendingdatacount = LoadCamPending::where('status', 0)->where('operator_id', $opcompany)->count();
        if ($pendingdatacount > 0) {
            $pendingdata = LoadCamPending::where('status', 0)->where('operator_id', $opcompany)->first();

            //op id  airetl =1,blink =2, gp = 3,robi= 4, teletalk =4

            $serial_id = $pendingdata->id;
            $number = $pendingdata->targeted_number;
            $operator_name = $pendingdata->operator_id;
            $number_type = $pendingdata->number_type;
            $amount = $pendingdata->campaign_price;

            $number = substr($number, 2);
            $offer_data = LoadPackage::where('package_price',$amount)->first();
            if (!empty($offer_data)) {
                $offer = 'yes';
            }else{
                $offer = 'no';
            }

            if ($operator_name == "gp" && ($number_type == 1 || $number_type == 2)) {
                $number_type = 1;
            }
            if ($operator_name == "gp" && $number_type == 3) {
                $number_type = 3;
            }
            if ($operator_name == "airtel") {
                $number_type = 1;
            }
            if ($operator_name == "robi") {
                $number_type = 1;
            }
            if ($operator_name == "teletalk") {
                $number_type = 1;
            }

            $pendingdataupdate = LoadCamPending::where('id', $pendingdata->id)->update(['status'=> 1]);


            return response()->json([
                'id' => $serial_id,
                'phone' => $number,
                'amount' => $amount,
                'type' => $number_type,
                'offer' => $offer,
                'status' => 1
            ], 200);


        }

        return response()->json([
            'data' => 'Not Found',
            'status' => 2
        ], 200);


        //
    }

    // public function flexiload_message_store(Request $request)
    // {

    //     /*$request->validate([
    //         'sim' => 'required',
    //         'op' => 'required',
    //         'msg' => 'required',
    //         'phone' => 'required',
    //         'st' => 'nullable'
    //     ]);*/
    //     if (empty($request->msg)) {
    //         return response()->json([
    //             'msg' => 'message empty',
    //             'status' => 2
    //         ], 200);
    //     }

    //     $simno = $request->sim;
    //     $opcompany = $request->op;
    //     $msg = $request->msg;
    //     $sender = $request->phone;
    //     $serialid = $request->st;

    //     $total_message = $msg;

    //     try {
    //         $trx_id = $this->getTransactionIdFromMessage($total_message, $opcompany);
    //         // dd($trx_id);
    //     } catch (\Exception $e) {
    //         $trx_id = "";
    //     }


    //     try {
    //         if (strpos($total_message, 'account balance is TK') !== false) {
    //             /* gp and bl format */
    //             $message_with_balance = explode('account balance is TK', $total_message)[1];
    //             $balance_parts = preg_split('/\s+/', $message_with_balance);
    //             $available_balance = str_replace(',', '', $balance_parts[1]);
    //         } elseif (strpos($total_message, 'new balance is') !== false) {
    //             /* robi/airtel/teletalk format */
    //             $message_with_balance = explode('new balance is', $total_message)[1];
    //             $balance_parts = preg_split('/\s+/', $message_with_balance);
    //             $available_balance = str_replace(',', '', $balance_parts[1]);
    //         } elseif (strpos($total_message, 'Your new balance is') !== false) {
    //             /* additional format */
    //             $message_with_balance = explode('Your new balance is', $total_message)[1];
    //             $balance_parts = preg_split('/\s+/', $message_with_balance);
    //             $available_balance = str_replace(',', '', $balance_parts[1]);
    //         } else {
    //             $available_balance = "";
    //         }

    //         $available_balance = rtrim($available_balance, '.');

    //     } catch (\Exception $e) {
    //         $available_balance = "";
    //     }

    //     try {
    //         $loadMsg = new LoadSimMessages();

    //         $loadMsg->sim_no = $simno;
    //         $loadMsg->operator_company = $opcompany;
    //         $loadMsg->message = $msg;
    //         $loadMsg->sender = $sender;
    //         $loadMsg->serial_id = $serialid;
    //         $loadMsg->save();


    //         if ((isset($trx_id)) && ($trx_id != '') && ($serialid != '')) {
    //             $loadCamPending = LoadCamPending::where('id', $serialid)->first();
    //             if (!empty($loadCamPending)) {
    //                 $loadCamPending->transaction_id = $trx_id;
    //                 $loadCamPending->save();

    //                 $user_id = $loadCamPending->user_id; // Store the current user_id
    //                 $loadMsg->user_id = $user_id;
    //                 $loadMsg->save();

    //                 $loadCamPending->sms_id = $loadCamPending->id;
    //                 $succesfull_load = new LoadCampaign30day();
    //                 $succesfull_load->create(json_decode($loadCamPending, true));
    //                 $loadCamPending->delete();
    //             }
    //         } 
    //         elseif ((isset($trx_id)) && ($trx_id != '')) {
    //             $load_number = $this->getPhoneNumberFromMessage($total_message, $opcompany);
    //             $load_amount = $this->getLoadAmountFromMessage($total_message, $opcompany);

    //             $loadCamPending = LoadCamPending::where('targeted_number', $load_number)
    //                 ->where('campaign_price', $load_amount)
    //                 ->where(function ($query) {
    //                     $query->where('transaction_id', NULL)
    //                         ->orWhere('transaction_id', '');

    //                 })
    //                 ->orderBy('id', 'DESC')
    //                 ->first();
    //                 // dd($loadCam30Days);as
    //             if (!empty($loadCamPending)) {
    //                 $loadCamPending->transaction_id = $trx_id;
    //                 $loadCamPending->save();

    //                 $user_id = $loadCamPending->user_id;
    //                 $loadMsg->user_id = $user_id;
    //                 $loadMsg->save();

    //                 $loadCamPending->sms_id = $loadCamPending->id;
    //                 $succesfull_load = new LoadCampaign30day();
    //                 $succesfull_load->create(json_decode($loadCamPending, true));
    //                 $loadCamPending->delete();
    //             }
    //         }

    //         if ((isset($available_balance)) && ($available_balance != '')) {
    //             $loadSimBal = LoadSimAvailablleBalance::where('status', 1)->first();

    //             if (empty($loadSimBal)) {
    //                 $loadSimBal = new LoadSimAvailablleBalance();
    //             }

    //             $loadSimBal->$opcompany = $available_balance;
    //             $loadSimBal->save();
    //         }
    //     } catch (\Exception $exception) {

    //     }


    //     return response()->json([
    //         'data' => 'message insert successfully',
    //         'status' => 1,
    //         'insert' => 1
    //     ], 200);

    // }


    public function getLoadAmountFromMessage($total_message, $opcompany)
    {
        try {
            if (strpos($total_message, 'Payment request of TK ') !== false) {
                /*gp format*/
                $message_with_amount = explode('Payment request of TK ', $total_message)[1];
                $amount = intval(str_replace(',', '', explode(' ', $message_with_amount)[0]));
            } elseif (strpos($total_message, 'request of BDT ') !== false) {
                /*gp format*/
                $message_with_amount = explode('request of BDT ', $total_message)[1];
                $amount = intval(str_replace(',', '', explode(' ', $message_with_amount)[0]));
            } elseif (strpos($total_message, 'payment of ') !== false) {
                /*gp format*/
                $message_with_amount = explode('payment of ', $total_message)[1];
                $amount = intval(str_replace(',', '', explode(' ', $message_with_amount)[0]));
            } elseif (strpos($total_message, 'Recharge of ') !== false) {
                /*gp format*/
                $message_with_amount = explode('Recharge of ', $total_message)[1];
                $amount = intval(str_replace(',', '', explode(' ', $message_with_amount)[0]));
            } elseif (strpos($total_message, 'recharge of ') !== false) {
                /*gp format*/
                $message_with_amount = explode('recharge of ', $total_message)[1];
                $amount = intval(str_replace(',', '', explode(' ', $message_with_amount)[0]));
            } elseif ((strpos($total_message, 'Recharge request of TK ') !== false)) {
                /*bl format*/
                $message_with_amount = explode('Recharge request of TK ', $total_message)[1];
                $amount = intval(str_replace(',', '', explode(' ', $message_with_amount)[0]));
            } elseif ((strpos($total_message, 'Recharge Request of TK ') !== false)) {
                /*bl format*/
                $message_with_amount = explode('Recharge Request of TK ', $total_message)[1];
                $amount = intval(str_replace(',', '', explode(' ', $message_with_amount)[0]));
            } elseif ((strpos($total_message, 'Recharge ') !== false)) {
                /*robi/airtel format*/
                $message_with_amount = explode('Recharge ', $total_message)[1];
                $amount = intval(str_replace(',', '', explode(' ', $message_with_amount)[0]));
            } elseif (strpos($total_message, 'recharged successfully with ') !== false) {
                /*teletalk format*/
                $message_with_amount = explode('recharged successfully with ', $total_message)[1];
                $amount = intval(str_replace(',', '', explode(' ', $message_with_amount)[0]));
            } else {
                $amount = "";
            }
        } catch (\Exception $exception) {
            $amount = "";
        }

        return $amount;
    }

    public function getPhoneNumberFromMessage($total_message, $opcompany)
    {
        try {
            if (strpos($total_message, 'for mobile no.') !== false) {
                /* gp format */
                $message_with_number = explode('for mobile no.', $total_message)[1];
                $number = explode(',', $message_with_number)[0];
            } elseif (strpos($total_message, 'for mobile no ') !== false) {
                /* bl format */
                $message_with_number = explode('for mobile no ', $total_message)[1];
                $number = explode(',', $message_with_number)[0];
            } elseif (strpos($total_message, 'BDT to ') !== false) {
                /* additional format */
                $message_with_number = explode('BDT to ', $total_message)[1];
                $number = explode(' ', $message_with_number)[0];
            } elseif (strpos($total_message, 'Tk to ') !== false) {
                /* robi/airtel format */
                $message_with_number = explode(' Tk to ', $total_message)[1];
                $number = explode(' ', $message_with_number)[0];
            } elseif (strpos($total_message, ' has been recharged successfully with ') !== false) {
                /* teletalk format */
                $message_with_number = explode(' has been recharged successfully with ', $total_message)[0];
                $t_number = explode(' ', $message_with_number);
                $number = end($t_number);
            } elseif (strpos($total_message, ' is accepted for processing') !== false) {
                /* blink format */
                $message_with_number = explode(' is accepted for processing', $total_message)[0];
                $t_number = explode('for 0', $message_with_number);
                $number = end($t_number);
            } elseif (strpos($total_message, ' is accepted for processing') !== false) {
                /* teletalk format */
                $message_with_number = explode(' is accepted for processing', $total_message)[0];
                $t_number = explode('for ', $message_with_number);
                $number = end($t_number);
            }else {
                $number = "";
            }
        } catch (\Exception $exception) {
            $number = "";
        }
        $number = trim($number);

        if (strpos($number, '880') === 0) {
            return $number;
        } else {
            return "880" . $number;
        }
    }


    public function getTransactionIdFromMessage($total_message, $opcompany = null)
    {
        try {
            if (strpos($total_message, 'transaction ID ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('transaction ID ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            }elseif (strpos($total_message, 'Transaction ID is ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction ID is ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            }elseif (strpos($total_message, 'Transaction ID ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction ID ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            } elseif (strpos($total_message, 'Transaction number is ') !== false) {
                /*robi/airtel format*/
                $message_with_trx_id = explode('Transaction number is ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];

                if (substr($trx_id, -4) == 'Your') {
                    $trx_id = substr($trx_id, 0, -4);  
                }
            } elseif (strpos($total_message, 'Transaction number ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction number ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            } elseif (strpos($total_message, 'Transaction ID is ') !== false) {
                /*teletalk format*/
                $message_with_trx_id = explode('Transaction ID is ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];

                if (substr($trx_id, -4) == 'Your') {
                    $trx_id = substr($trx_id, 0, -4);
                }
            } else {
                $trx_id = "";
            }
        } catch (\Exception $e) {
            $trx_id = "";
        }

        return $trx_id;
    }
       private $apiKey = 'ak_HwzYFi4QelI9V6rvWkvw926OZTAf8PuJobYjg7B1';
    private $apiSecret = 'sk_FDXJHJA84DNr1Xx3ysG6gU7VISXKs1SKHa4xH7XAHPoijOzTxmf5743RozKW';
    private $baseUrl = 'https://gateway.irecharge.net/api/v1';

    /**
     * Send Flexiload using iRecharge API
     */
    public function sendFlexiloadIRecharge()
    {
        // First check connection status
        $connectionStatus = $this->checkIRechargeConnectionStatus();
        
        // Get gateway information
        $gatewayInfo = $this->getGatewayBalance();
        
        // Take 5 pending loads at a time (status 0 = pending)
        $pending_loads = LoadCamPending::where('status', 0)
            ->orderBy('id', 'asc')
            ->take(5)
            ->get();

        $results = [];

        foreach ($pending_loads as $pending_load) {
            try {
                $result = $this->processIRechargeRecharge($pending_load);
                $results[] = $result;
            } catch (\Exception $e) {
                Log::error("iRecharge failed for ID {$pending_load->id}: " . $e->getMessage());
                $pending_load->update(['status' => 1, 'remarks' => $e->getMessage()]);
                $results[] = [
                    'id' => $pending_load->id,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                continue;
            }
        }

        // Get pending counts by operator
        $operatorPendings = [
            'airtel'   => LoadCamPending::where('operator_id', 'airtel')->count(),
            'gp'       => LoadCamPending::where('operator_id', 'gp')->count(),
            'blink'    => LoadCamPending::where('operator_id', 'blink')->count(),
            'robi'     => LoadCamPending::where('operator_id', 'robi')->count(),
            'teletalk' => LoadCamPending::where('operator_id', 'teletalk')->count(),
        ];

        return view('cron.sendPendingLoadIrecharge', [
            'rest_pendings'   => LoadCamPending::count(),
            'airtel_pending'  => $operatorPendings['airtel'],
            'gp_pending'      => $operatorPendings['gp'],
            'bl_pending'      => $operatorPendings['blink'],
            'robi_pending'    => $operatorPendings['robi'],
            'tt_pending'      => $operatorPendings['teletalk'],
            'results'         => $results,
            'connection_status' => $connectionStatus,
            'gateway_info' => $gatewayInfo,
        ]);
    }

    /**
     * Process individual recharge using iRecharge API
     */
    private function processIRechargeRecharge($pending_load)
    {
        $operator = $pending_load->operator_id;
        $targeted_number = $pending_load->targeted_number;
        $flexiload_price = $pending_load->campaign_price;
        $number_type = $pending_load->number_type;
        $sms_id = $pending_load->sms_id;
        $offer_id = $pending_load->offer_id;
        $campaign_type = $pending_load->campaign_type;

        // Check if already completed
        $existingCompleted = LoadCampaign30day::where('sms_id', $sms_id)->first();
        if ($existingCompleted && $existingCompleted->status == 1) {
            Log::info("Transaction already completed for SMS ID: {$sms_id}");
            $pending_load->delete();
            return [
                'id' => $pending_load->id,
                'status' => 'success',
                'transaction_id' => $existingCompleted->transaction_id,
                'message' => 'Transaction already completed'
            ];
        }

        // Format number
        $apiNumber = preg_replace('/^88/', '', $targeted_number);
        
        // Determine operator type
        $operatorType = ($number_type == 2) ? 'postpaid' : 'prepaid';
        
        if ($operator == 'gp' && $number_type == 3) {
            $operatorType = 'skitto';
        }

        $irechargeOperator = $this->mapOperatorToIRecharge($operator);
        
        // Webhook URL based on your routes
        $webhookUrl = config('app.url') . '/api/webhook/irecharge';

        DB::beginTransaction();

        try {
            $requestData = [
                'service_category' => 'recharge',
                'service_type' => ($offer_id && $campaign_type == 2) ? 'offer' : 'regular',
                'recipient_number' => $apiNumber,
                'operator' => $irechargeOperator,
                'operator_type' => $operatorType,
                'amount' => (float)$flexiload_price,
                'reference' => (string)$sms_id,
                'callback_url' => $webhookUrl,
            ];

            Log::info('iRecharge Request Data:', $requestData);

            $response = $this->callIRechargeApi($requestData);

            Log::info('iRecharge Response:', $response);

            // Check if API call was successful
            if ($response && isset($response['success']) && $response['success'] === true) {
                $transactionData = $response['data'] ?? [];
                $gatewayTransactionId = $transactionData['transaction_id'] ?? null;

                if ($gatewayTransactionId) {
                    // Store gateway transaction_id in pending record
                    $pending_load->gateway_transaction_id = $gatewayTransactionId;
                    $pending_load->transaction_id = $gatewayTransactionId;
                    $pending_load->status = 1; // Mark as sent to gateway
                    $pending_load->save();
                    
                    DB::commit();

                    Log::info('Gateway transaction stored', [
                        'sms_id' => $sms_id,
                        'pending_id' => $pending_load->id,
                        'gateway_transaction_id' => $gatewayTransactionId
                    ]);

                    return [
                        'id' => $pending_load->id,
                        'status' => 'sent_to_gateway',
                        'transaction_id' => $gatewayTransactionId,
                        'message' => 'Transaction sent to gateway, waiting for delivery confirmation'
                    ];
                }
            }
            
            // Handle API errors
            $errorMsg = $response['error'] ?? $response['message'] ?? 'API call failed';
            $pending_load->update(['status' => 1, 'remarks' => $errorMsg]);
            DB::commit();
            
            return [
                'id' => $pending_load->id,
                'status' => 'failed',
                'error' => $errorMsg
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('iRecharge Process Error: ' . $e->getMessage());
            $pending_load->update(['status' => 0, 'remarks' => 'Retry: ' . $e->getMessage()]);
            
            return [
                'id' => $pending_load->id,
                'status' => 'retry',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Call iRecharge API using cURL (PHP 7.4 compatible)
     */
    private function callIRechargeApi($data)
    {
        $ch = curl_init();
        $url = $this->baseUrl . "/create_request";
        
        $jsonData = json_encode($data);
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'X-API-Key: ' . $this->apiKey,
                'X-API-Secret: ' . $this->apiSecret,
                'Content-Type: application/json',
                'Accept: application/json',
                'Content-Length: ' . strlen($jsonData)
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        
        curl_close($ch);
        
        if ($curlError) {
            Log::error('iRecharge API cURL Error: ' . $curlError);
            return [
                'success' => false,
                'error' => 'cURL Error: ' . $curlError
            ];
        }
        
        $decodedResponse = json_decode($response, true);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return $decodedResponse;
        }
        
        return [
            'success' => false,
            'error' => 'HTTP Error: ' . $httpCode,
            'response' => $decodedResponse
        ];
    }

    /**
     * Check iRecharge modem connection status
     */
    public function checkIRechargeConnectionStatus()
    {
        $ch = curl_init();
        $url = $this->baseUrl . "/connection-status";
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'X-API-Key: ' . $this->apiKey,
                'X-API-Secret: ' . $this->apiSecret,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
        
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $curlError,
                'active_gateways' => 0
            ];
        }
        
        return json_decode($response, true);
    }

    /**
     * Get gateway balance
     */
    public function getGatewayBalance()
    {
        $gatewaysData = $this->getFirstActiveGateway();
        
        if ($gatewaysData['success'] && isset($gatewaysData['gateway'])) {
            $gateway = $gatewaysData['gateway'];
            
            return [
                'success' => true,
                'gateway_name' => $gateway['name'] ?? 'Unknown',
                'operator' => $gateway['operator'] ?? 'Unknown',
                'current_balance' => $gateway['current_balance'] ?? 0,
                'connection_status' => $gateway['connection_status'] ?? 'unknown',
                'software_active' => $gateway['software_active'] ?? false,
            ];
        }
        
        return $gatewaysData;
    }

    /**
     * Get first active gateway
     */
    private function getFirstActiveGateway()
    {
        $ch = curl_init();
        $url = $this->baseUrl . "/gateways";
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'X-API-Key: ' . $this->apiKey,
                'X-API-Secret: ' . $this->apiSecret,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if (isset($result['success']) && $result['success'] === true) {
            $gateways = $result['data']['gateways'] ?? [];
            
            foreach ($gateways as $gateway) {
                if ($gateway['connection_status'] === 'connected' && $gateway['software_active'] === true) {
                    return [
                        'success' => true,
                        'gateway' => $gateway,
                    ];
                }
            }
        }
        
        return ['success' => false, 'message' => 'No active gateway found'];
    }

    /**
     * Map operator to iRecharge format
     */
    private function mapOperatorToIRecharge($operator)
    {
        $mapping = [
            'gp' => 'grameenphone',
            'grameen' => 'grameenphone',
            'blink' => 'banglalink',
            'banglalink' => 'banglalink',
            'robi' => 'robi',
            'airtel' => 'airtel',
            'teletalk' => 'teletalk',
        ];
        
        return $mapping[$operator] ?? $operator;
    }
// IGL API Configuration
    private $iglBaseUrl = 'http://103.86.193.25:8080/api/';
    private $iglEmail = 'admin@iglweb.com';
    private $iglPassword = '123456';
    private $iglToken = null;
    
    // SIM Profile mapping (auto-populated from API)
    private $simProfiles = [];
    private $operatorToSimProfileId = [];

    // =============================================
    // IGL AUTHENTICATION
    // =============================================
    private function authenticateIGL()
    {
        try {
            $client = new Client([
                'timeout' => 30,
                'verify' => false,
            ]);

            $response = $client->post($this->iglBaseUrl . 'auth/login/', [
                'json' => [
                    'email' => $this->iglEmail,
                    'password' => $this->iglPassword,
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            
            if (isset($body['token'])) {
                $this->iglToken = $body['token'];
                Log::info('IGL Authentication successful');
                
                // Load SIM profiles after authentication
                $this->loadSimProfiles();
                return true;
            }

            Log::error('IGL Authentication failed');
            return false;

        } catch (\Exception $e) {
            Log::error('IGL Authentication exception: ' . $e->getMessage());
            return false;
        }
    }

    // =============================================
    // LOAD SIM PROFILES FROM API
    // =============================================
    private function loadSimProfiles()
    {
        try {
            $client = new Client([
                'timeout' => 30,
                'verify' => false,
            ]);

            $response = $client->get($this->iglBaseUrl . 'sim-profiles/', [
                'headers' => ['Authorization' => 'Token ' . $this->iglToken],
            ]);

            $data = json_decode($response->getBody(), true);
            $profiles = $data['results'] ?? $data;
            
            $this->simProfiles = [];
            $this->operatorToSimProfileId = [];
            
            foreach ($profiles as $profile) {
                $profileId = $profile['id'];
                $operator = strtolower($profile['operator']);
                
                $this->simProfiles[$profileId] = [
                    'id' => $profileId,
                    'name' => $profile['name'],
                    'operator' => $operator,
                    'sim_number' => $profile['sim_number'],
                    'port' => $profile['port'],
                    'is_active' => $profile['is_active'],
                ];
                
                // Map operator to SIM profile ID
                $this->operatorToSimProfileId[$operator] = $profileId;
            }
            
            Log::info('SIM Profiles loaded:', [
                'mapping' => $this->operatorToSimProfileId,
                'profiles' => $this->simProfiles
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load SIM profiles: ' . $e->getMessage());
        }
    }

    // =============================================
    // GET SIM PROFILE ID BY OPERATOR
    // =============================================
    private function getSimProfileIdByOperator($operator)
    {
        $operatorMap = [
            'gp' => 'grameenphone',
            'grameen' => 'grameenphone',
            'blink' => 'banglalink',
            'banglalink' => 'banglalink',
            'robi' => 'robi',
            'airtel' => 'airtel',
            'teletalk' => 'teletalk',
        ];
        
        $apiOperator = $operatorMap[$operator] ?? $operator;
        $simProfileId = $this->operatorToSimProfileId[$apiOperator] ?? null;
        
        if (!$simProfileId) {
            Log::warning('No SIM profile found for operator: ' . $operator);
            foreach ($this->simProfiles as $id => $profile) {
                if ($profile['is_active']) {
                    $simProfileId = $id;
                    break;
                }
            }
        }
        
        return $simProfileId;
    }

    // =============================================
    // IGL SEND LOAD
    // =============================================
    public function sendIGLLoad()
    {
        if (!$this->authenticateIGL()) {
            Log::error('IGL Authentication failed');
            return view('cron.sendIGLLoad', [
                'error' => 'Authentication failed. Please check your API credentials.',
                'rest_pendings' => LoadCamPending::count(),
                'airtel_pending' => 0,
                'gp_pending' => 0,
                'bl_pending' => 0,
                'robi_pending' => 0,
                'tt_pending' => 0,
                'results' => [],
                'success_count' => 0,
                'failed_count' => 0,
                'processing_count' => 0,
                'failed_loads' => LoadCamPending::where('status', 1)
                    ->orderBy('id', 'desc')
                    ->take(100)
                    ->get(),
            ]);
        }

        $pending_loads = LoadCamPending::where('status', 0)
            ->orderBy('id', 'asc')
            ->take(5)
            ->get();

        $results = [];
        $successCount = 0;
        $failedCount = 0;
        $processingCount = 0;

        foreach ($pending_loads as $pending_load) {
            try {
                $result = $this->processIGLTransaction($pending_load);
                $results[] = $result;
                if ($result['status'] == 'success') {
                    $successCount++;
                } elseif ($result['status'] == 'processing') {
                    $processingCount++;
                } else {
                    $failedCount++;
                }
            } catch (\Exception $e) {
                Log::error("IGL failed for ID {$pending_load->id}: " . $e->getMessage());
                $pending_load->update([
                    'status' => 1,
                    'remarks' => 'Failed',
                    'transaction_id' => null,
                ]);
                $results[] = [
                    'id' => $pending_load->id,
                    'sms_id' => $pending_load->sms_id,
                    'phone' => $pending_load->targeted_number,
                    'amount' => $pending_load->campaign_price,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                $failedCount++;
                continue;
            }
        }

        $operatorPendings = [
            'airtel'   => LoadCamPending::where('operator_id', 'airtel')->count(),
            'gp'       => LoadCamPending::where('operator_id', 'gp')->count(),
            'blink'    => LoadCamPending::where('operator_id', 'blink')->count(),
            'robi'     => LoadCamPending::where('operator_id', 'robi')->count(),
            'teletalk' => LoadCamPending::where('operator_id', 'teletalk')->count(),
        ];

        return view('cron.sendIGLLoad', [
            'rest_pendings'   => LoadCamPending::count(),
            'airtel_pending'  => $operatorPendings['airtel'],
            'gp_pending'      => $operatorPendings['gp'],
            'bl_pending'      => $operatorPendings['blink'],
            'robi_pending'    => $operatorPendings['robi'],
            'tt_pending'      => $operatorPendings['teletalk'],
            'results'         => $results,
            'success_count'   => $successCount,
            'failed_count'    => $failedCount,
            'processing_count' => $processingCount,
            'failed_loads' => LoadCamPending::where('status', 1)
                ->orderBy('id', 'desc')
                ->take(100)
                ->get(),
            'error'           => null,
        ]);
    }

    // =============================================
    // IGL PROCESS TRANSACTION (Using sms_id as idempotency_key)
    // =============================================
    private function processIGLTransaction($pending_load)
    {
        $operator = $pending_load->operator_id;
        $targeted_number = $pending_load->targeted_number;
        $flexiload_price = $pending_load->campaign_price;
        $number_type = $pending_load->number_type;
        $sms_id = $pending_load->sms_id;  // UNIQUE - used as idempotency_key
        $campaign_id = $pending_load->campaign_id;
        $offer_id = $pending_load->offer_id;
        $campaign_type = $pending_load->campaign_type;
        $user_id = $pending_load->user_id;
        $package_id = $pending_load->package_id ?? null;

        // Get the correct SIM profile ID based on operator
        $simProfileId = $this->getSimProfileIdByOperator($operator);
        
        if (!$simProfileId) {
            Log::error('No SIM profile available for operator: ' . $operator);
            $pending_load->update([
                'status' => 1,
                'remarks' => 'Failed',
                'transaction_id' => null,
            ]);
            return [
                'id' => $pending_load->id,
                'status' => 'failed',
                'error' => 'No SIM profile available'
            ];
        }
        $profileSimNumber = $this->simProfiles[$simProfileId]['sim_number'] ?? 'UNKNOWN';
        if (!$profileSimNumber) {
            $profileSimNumber = 'UNKNOWN';
        }

        Log::info('Processing IGL Transaction:', [
            'pending_id' => $pending_load->id,
            'operator' => $operator,
            'sim_profile_id' => $simProfileId,
            'sms_id' => $sms_id,
            'phone' => $targeted_number,
            'amount' => $flexiload_price
        ]);

        // Check if already completed using sms_id (unique)
        $existingCompleted = LoadCampaign30day::where('sms_id', $sms_id)->first();
            
        if ($existingCompleted && $existingCompleted->status == 1) {
            Log::info("Transaction already completed for SMS ID: {$sms_id}");
            $pending_load->delete();
            return [
                'id' => $pending_load->id,
                'sms_id' => $sms_id,
                'status' => 'success',
                'transaction_id' => $existingCompleted->transaction_id,
                'message' => 'Already completed'
            ];
        }

        $phone_number = preg_replace('/^88/', '', $targeted_number);
        $product = $this->getProductType($operator, $number_type, $campaign_type, $offer_id);
        
        // A retry must keep the original key so Django can locate and
        // re-execute the existing failed transaction.
        $idempotency_key = (string) $sms_id;

        DB::beginTransaction();

        try {
            $client = new Client([
                'timeout' => 60,
                'verify' => false,
            ]);

            $requestData = [
                'sim_profile_id' => $simProfileId,
                'product' => $product,
                'phone_number' => $phone_number,
                'amount' => (int) round($flexiload_price),
                'idempotency_key' => $idempotency_key,
                'retry_failed' => true,
            ];

            if ($campaign_type == 2 && !empty($offer_id)) {
                $requestData['offer_id'] = $offer_id;
            }

            Log::info('IGL API Request (sms_id as idempotency_key):', $requestData);

            $response = $client->post($this->iglBaseUrl . 'transactions/send/', [
                'headers' => [
                    'Authorization' => 'Token ' . $this->iglToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData,
            ]);

            $responseBody = json_decode($response->getBody(), true);
            $transactionUuid = $responseBody['id'] ?? null;
            
            // Wait and check status if processing
            if (isset($responseBody['status']) && $responseBody['status'] === 'processing') {
                sleep(2);
                if ($transactionUuid) {
                    $statusResponse = $client->get($this->iglBaseUrl . "transactions/{$transactionUuid}/", [
                        'headers' => ['Authorization' => 'Token ' . $this->iglToken],
                    ]);
                    $responseBody = json_decode($statusResponse->getBody(), true);
                    $transactionUuid = $responseBody['id'] ?? $transactionUuid;
                }
            }
            
            Log::info('IGL API Response:', $responseBody);

            $apiStatus = strtolower($responseBody['status'] ?? '');

            // The carrier accepted the request but has not confirmed delivery yet.
            // Keep it in the pending table as status 2 and save the API UUID for sync.
            if (in_array($apiStatus, ['pending', 'processing'], true)) {
                $pending_load->update([
                    'status' => 2,
                    'remarks' => 'Processing',
                    'transaction_id' => $transactionUuid,
                ]);
                DB::commit();

                Log::info('IGL transaction waiting for confirmation', [
                    'sms_id' => $sms_id,
                    'api_transaction_id' => $transactionUuid,
                ]);

                return [
                    'id' => $pending_load->id,
                    'sms_id' => $sms_id,
                    'phone' => $phone_number,
                    'amount' => $flexiload_price,
                    'status' => 'processing',
                    'transaction_id' => $transactionUuid,
                    'message' => 'Waiting for carrier confirmation',
                ];
            }

            if ($apiStatus === 'success') {
                $transactionId = $responseBody['carrier_transaction_id'] ?? null;
                $sourceSimNumber = $responseBody['source_sim_number'] ?? $profileSimNumber;
                $sourceSimNumber = $sourceSimNumber ?: $profileSimNumber;
                $fullMessage = $responseBody['message'] ?? '';
                
                if (empty($transactionId) && !empty($fullMessage)) {
                    if (preg_match('/(?:transaction ID|Transaction ID)[:\s]+([A-Z0-9\.]+)/i', $fullMessage, $matches)) {
                        $transactionId = $matches[1];
                    } elseif (preg_match('/R\d{6}\.\d{4}\.\d{6}/', $fullMessage, $matches)) {
                        $transactionId = $matches[0];
                    }
                }
                
                // Save to LoadSimMessages
                $loadMsg = new LoadSimMessages();
                $loadMsg->user_id = $user_id;
                $loadMsg->sim_no = $sourceSimNumber;
                $loadMsg->operator_company = $operator;
                $loadMsg->message = $fullMessage;
                $loadMsg->sender = 'IGL_API';
                $loadMsg->serial_id = $sms_id;
                $loadMsg->status = 1;
                $loadMsg->save();

                // Save to LoadCampaign30day
                $campaignData = [
                    'user_id' => $user_id,
                    'operator_id' => $operator,
                    'sms_id' => $sms_id,
                    'campaign_id' => $campaign_id ?: $sms_id,
                    'targeted_number' => $targeted_number,
                    'owner_name' => $pending_load->owner_name ?? null,
                    'package_id' => $package_id,
                    'number_type' => $number_type,
                    'campaign_type' => $campaign_type,
                    'campaign_price' => $flexiload_price,
                    'api_port' => $pending_load->api_port ?? null,
                    'transaction_id' => $transactionId,
                    'remarks' => 'Delivered',
                    'status' => 1,
                ];
                
                LoadCampaign30day::create($campaignData);
                
                Log::info('Saved - Success! sms_id: ' . $sms_id . ', transaction_id: ' . $transactionId);

                $pending_load->delete();
                DB::commit();

                return [
                    'id' => $pending_load->id,
                    'sms_id' => $sms_id,
                    'phone' => $phone_number,
                    'amount' => $flexiload_price,
                    'status' => 'success',
                    'transaction_id' => $transactionId,
                    'message' => 'Delivered',
                ];
            }

            // FAILED TRANSACTION
            $errorMsg = $responseBody['error_message'] ?? $responseBody['message'] ?? 'Transaction failed';
            
            $loadMsg = new LoadSimMessages();
            $loadMsg->user_id = $user_id;
            $loadMsg->sim_no = $responseBody['source_sim_number'] ?? $profileSimNumber;
            $loadMsg->operator_company = $operator;
            $loadMsg->message = $errorMsg;
            $loadMsg->sender = 'IGL_API';
            $loadMsg->serial_id = $sms_id;
            $loadMsg->status = 0;
            $loadMsg->save();
            
            $pending_load->update([
                'status' => 1,
                'remarks' => 'Failed',
                'transaction_id' => null,
            ]);
            DB::commit();

            return [
                'id' => $pending_load->id,
                'sms_id' => $sms_id,
                'status' => 'failed',
                'error' => 'Failed',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('IGL Process Error: ' . $e->getMessage());
            
            if (strpos($e->getMessage(), 'duplicate') !== false) {
                $existing = LoadCampaign30day::where('sms_id', $sms_id)->first();
                if ($existing) {
                    $pending_load->delete();
                    return [
                        'id' => $pending_load->id,
                        'sms_id' => $sms_id,
                        'status' => 'success',
                        'transaction_id' => $existing->transaction_id,
                        'message' => 'Already processed',
                    ];
                }
            }
            
            $pending_load->update([
                'status' => 1,
                'remarks' => 'Failed',
                'transaction_id' => null,
            ]);
            return [
                'id' => $pending_load->id,
                'sms_id' => $sms_id,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    // =============================================
    // IGL GET PRODUCT TYPE
    // =============================================
    private function getProductType($operator, $number_type, $campaign_type, $offer_id)
    {
        if ($campaign_type == 2 && !empty($offer_id)) {
            return 'offer';
        }
        if ($operator == 'gp' && $number_type == 3) {
            return 'skitto';
        }
        if ($number_type == 2) {
            return 'postpaid';
        }
        return 'flexiload';
    }

    // =============================================
    // IGL GET SIM PROFILES
    // =============================================
    public function getIGLSimProfiles()
    {
        if (!$this->authenticateIGL()) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }

        return response()->json([
            'success' => true,
            'profiles' => $this->simProfiles,
            'mapping' => $this->operatorToSimProfileId,
        ]);
    }

    // =============================================
    // IGL GET TRANSACTION HISTORY
    // =============================================
    public function getIGLTransactionHistory(Request $request)
    {
        if (!$this->authenticateIGL()) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }

        try {
            $client = new Client([
                'timeout' => 30,
                'verify' => false,
            ]);

            $page = $request->get('page', 1);
            $response = $client->get($this->iglBaseUrl . 'transactions/', [
                'headers' => ['Authorization' => 'Token ' . $this->iglToken],
                'query' => ['page' => $page],
            ]);

            return response()->json(['success' => true, 'data' => json_decode($response->getBody(), true)]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =============================================
    // IGL GET SPECIFIC TRANSACTION
    // =============================================
    public function getIGLTransaction($uuid)
    {
        if (!$this->authenticateIGL()) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }

        try {
            $client = new Client([
                'timeout' => 30,
                'verify' => false,
            ]);

            $response = $client->get($this->iglBaseUrl . "transactions/{$uuid}/", [
                'headers' => ['Authorization' => 'Token ' . $this->iglToken],
            ]);

            return response()->json(['success' => true, 'data' => json_decode($response->getBody(), true)]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =============================================
    // IGL SYNC PENDING TRANSACTIONS
    // =============================================
    public function syncIGLTransactions()
    {
        if (!$this->authenticateIGL()) {
            Log::error('IGL Sync: Authentication failed');
            return response()->json(['error' => 'Authentication failed'], 401);
        }

        $pendingSync = LoadCamPending::where('status', 2)
            ->whereNotNull('transaction_id')
            ->where('transaction_id', '!=', '')
            ->get();

        $synced = 0;
        $failed = 0;

        foreach ($pendingSync as $pending) {
            try {
                $client = new Client([
                    'timeout' => 30,
                    'verify' => false,
                ]);

                $response = $client->get($this->iglBaseUrl . "transactions/{$pending->transaction_id}/", [
                    'headers' => ['Authorization' => 'Token ' . $this->iglToken],
                ]);

                $transaction = json_decode($response->getBody(), true);
                
                if (isset($transaction['status']) && $transaction['status'] === 'success') {
                    LoadSimMessages::create([
                        'user_id' => $pending->user_id,
                        'sim_no' => $transaction['source_sim_number'] ?? 'UNKNOWN',
                        'operator_company' => $pending->operator_id,
                        'message' => $transaction['message'] ?? '',
                        'sender' => 'IGL_SYNC',
                        'serial_id' => $pending->sms_id,
                        'status' => 1,
                    ]);
                    
                    LoadCampaign30day::create([
                        'user_id' => $pending->user_id,
                        'operator_id' => $pending->operator_id,
                        'sms_id' => $pending->sms_id,
                        'campaign_id' => $pending->campaign_id ?: $pending->sms_id,
                        'targeted_number' => $pending->targeted_number,
                        'owner_name' => $pending->owner_name ?? null,
                        'package_id' => $pending->package_id ?? null,
                        'number_type' => $pending->number_type,
                        'campaign_type' => $pending->campaign_type,
                        'campaign_price' => $pending->campaign_price,
                        'api_port' => $pending->api_port ?? null,
                        'transaction_id' => $transaction['carrier_transaction_id'] ?? $pending->transaction_id,
                        'remarks' => 'Delivered',
                        'status' => 1,
                    ]);
                    
                    $pending->delete();
                    $synced++;
                } elseif (isset($transaction['status']) && $transaction['status'] === 'failed') {
                    $pending->update([
                        'status' => 1,
                        'remarks' => 'Failed',
                        'transaction_id' => null,
                    ]);
                    $failed++;
                }
                
            } catch (\Exception $e) {
                Log::error("IGL Sync error for {$pending->id}: " . $e->getMessage());
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'synced' => $synced,
            'failed' => $failed,
            'remaining' => LoadCamPending::where('status', 2)->whereNotNull('transaction_id')->count(),
        ]);
    }

    // =============================================
    // IGL TEST CONNECTION
    // =============================================
    public function testIGLConnection()
    {
        $authResult = $this->authenticateIGL();
        
        if (!$authResult) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'IGL API connection successful',
            'sim_profiles' => $this->simProfiles,
            'operator_mapping' => $this->operatorToSimProfileId,
        ]);
    }

    // =============================================
    // IGL GET BALANCE
    // =============================================
    public function getIGLBalance(Request $request)
    {
        if (!$this->authenticateIGL()) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }

        return response()->json([
            'success' => true,
            'profiles' => $this->simProfiles,
            'mapping' => $this->operatorToSimProfileId,
        ]);
    }

    // =============================================
    // IGL WEBHOOK
    // =============================================
    public function iglWebhook(Request $request)
    {
        $expectedSecret = (string) config(
            'services.igl_webhook_secret',
            env('IGL_WEBHOOK_SECRET', '')
        );
        $receivedSecret = (string) $request->header('X-IGL-Webhook-Secret', '');
        if ($expectedSecret === '' || !hash_equals($expectedSecret, $receivedSecret)) {
            Log::warning('IGL Webhook rejected: invalid secret');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        Log::info('IGL Webhook received:', $payload);

        $apiTransactionId = $payload['id'] ?? null;
        $carrierTransactionId = $payload['carrier_transaction_id'] ?? null;
        $transactionId = $carrierTransactionId ?: $apiTransactionId;
        $status = strtolower($payload['status'] ?? '');
        $message = $payload['message'] ?? '';
        $idempotencyKey = $payload['idempotency_key'] ?? null;

        if (!$apiTransactionId && !$idempotencyKey) {
            return response()->json(['error' => 'Missing transaction identifier'], 400);
        }
        if (!in_array($status, ['success', 'failed', 'pending', 'processing'], true)) {
            return response()->json(['error' => 'Invalid transaction status'], 422);
        }

        DB::beginTransaction();
        try {
            $pendingQuery = LoadCamPending::query();
            if ($idempotencyKey) {
                $smsLookupId = preg_replace(
                    '/-retry-\d{14}$/',
                    '',
                    (string) $idempotencyKey
                );
                $pendingQuery->where('sms_id', $smsLookupId);
            } else {
                $pendingQuery->where('transaction_id', $apiTransactionId);
            }
            $pendingLoad = $pendingQuery->lockForUpdate()->first();

            if (!$pendingLoad) {
                DB::rollBack();
                $alreadyDelivered = $idempotencyKey
                    ? LoadCampaign30day::where('sms_id', $smsLookupId)->exists()
                    : false;
                return response()->json([
                    'message' => $alreadyDelivered
                        ? 'Webhook already processed'
                        : 'No matching processing record found',
                ], 200);
            }

            $smsId = $pendingLoad->sms_id;
            if ($status === 'success') {
                        LoadSimMessages::updateOrCreate(
                            [
                                'serial_id' => $smsId,
                                'sender' => 'IGL_WEBHOOK',
                            ],
                            [
                            'user_id' => $pendingLoad->user_id,
                            'sim_no' => $payload['source_sim_number'] ?? 'UNKNOWN',
                            'operator_company' => $pendingLoad->operator_id,
                            'message' => $message,
                            'status' => 1,
                            ]
                        );
                        
                        LoadCampaign30day::updateOrCreate(
                            ['sms_id' => $smsId],
                            [
                            'user_id' => $pendingLoad->user_id,
                            'operator_id' => $pendingLoad->operator_id,
                            'campaign_id' => $pendingLoad->campaign_id ?: $smsId,
                            'targeted_number' => $pendingLoad->targeted_number,
                            'owner_name' => $pendingLoad->owner_name ?? null,
                            'package_id' => $pendingLoad->package_id ?? null,
                            'number_type' => $pendingLoad->number_type,
                            'campaign_type' => $pendingLoad->campaign_type,
                            'campaign_price' => $pendingLoad->campaign_price,
                            'api_port' => $pendingLoad->api_port ?? null,
                            'transaction_id' => $transactionId,
                            'remarks' => 'Delivered',
                            'status' => 1,
                            ]
                        );
                        
                        $pendingLoad->delete();
                        Log::info('Webhook: Saved transaction for sms_id: ' . $smsId);
            } elseif (in_array($status, ['pending', 'processing'], true)) {
                        $pendingLoad->update([
                            'status' => 2,
                            'remarks' => 'Processing',
                            'transaction_id' => $apiTransactionId ?: $pendingLoad->transaction_id,
                        ]);
            } elseif ($status === 'failed') {
                        $pendingLoad->update([
                            'status' => 1,
                            'remarks' => 'Failed',
                            'transaction_id' => null,
                        ]);
            }

            DB::commit();
            return response()->json(['message' => 'Webhook processed successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('IGL Webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =============================================
    // IGL CHECK RECENT TRANSACTIONS
    // =============================================
    public function checkRecentTransactions()
    {
        $recentTransactions = LoadCampaign30day::orderBy('id', 'desc')->take(10)->get();
        
        $transactions = [];
        foreach ($recentTransactions as $transaction) {
            $transactions[] = [
                'id' => $transaction->id,
                'sms_id' => $transaction->sms_id,
                'campaign_id' => $transaction->campaign_id,
                'transaction_id' => $transaction->transaction_id,
                'targeted_number' => $transaction->targeted_number,
                'amount' => $transaction->campaign_price,
                'remarks' => $transaction->remarks,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at,
            ];
        }
        
        return response()->json([
            'success' => true,
            'sim_profile_mapping' => $this->operatorToSimProfileId,
            'campaign30day_count' => LoadCampaign30day::count(),
            'recent_transactions' => $transactions,
        ]);
    }

}

