<?php

namespace App\Http\Controllers\Api;

use App\Model\LoadCampaign30day;
use App\Model\LoadSimAvailablleBalance;
use App\Model\LoadSimMessages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LoadCamPending;
use App\Model\LoadPackage;
use App\Model\LoadCampaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoadApiController extends Controller
{
/**
* Get the list of pending Flexiloads
*
* @param Request $request
* @return \Illuminate\Http\JsonResponse
*/
public function getPendingFlexiloads(Request $request)
	{
	$pendingLoads = LoadCamPending::whereIn('status', [0, 2])  // Using whereIn for better clarity
	    ->where('operator_id', 'gp') // Make sure operator_id is set to 'gp' for GrameenPhone
	    ->orderBy('id', 'asc') // Order by ascending ID (optional based on use case)
	    ->take(1) // Limit the query to 1 record
	    ->get();

	if ($pendingLoads->isEmpty()) {
	return response()->json([

	'data' => NUll,
	'device_info' => ['id' => 3,]
	]);
	}

	$load = $pendingLoads->first(); 

	$formattedData = [
	'id' => $load->id,
	'telco' => $this->mapTelco($load->operator_id),
	'number' => $this->removeCountryCode($load->targeted_number),
	'amount' => $load->campaign_price, 
	];

	$responseData = [];

	if ($request->isJson()) {
	$requestData = $request->json()->all();

	foreach ($requestData as $entry) {
	if (isset($entry['telco'], $entry['number'], $entry['amount'])) {
	$responseData[] = [
	'operator' => $entry['telco'],
	'sim_number' => $entry['number'],
	'telco' => $entry['telco'],
	'balance' => $entry['amount'],
	];
	}
	}
	} else {
	$responseData[] = [
	'operator' => $formattedData['telco'],
	'sim_number' => $formattedData['number'],
	'telco' => $formattedData['telco'],
	'balance' => $formattedData['amount'],
	];

	}
	$device_info = [
	"id" => 3,
	"type" => "android",
	"operator" => "GrameenPhone",
	"deviceid" => "abcd1234xyz",

	"balance" => null,
	"deviceinfo" => "Samsung Galaxy S21, Android 11",
	"apps_name" => "WhatsApp, Facebook, Instagram",
	"status" => "1",
	"created_at" => "2024-10-16T12:48:46.000000Z",
	"updated_at" => "2024-10-16T12:50:02.000000Z"
	];

	
	return response()->json([
	'data' => $formattedData,
	'device_info' => $device_info // Add device_info here
	]);
}


/**

*
* @param string $number
* @return string
*/
private function removeCountryCode($number)
{

	if (strpos($number, '88') === 0) {
	return substr($number, 2);
	}
	return $number;
}

/**
* Map operator_id to the respective telco name
*
* @param string $operatorId
* @return string
*/
public function makeUpdate(Request $request, $service_id)
	{

	$pending_load = LoadCamPending::where('id', $service_id)->first();

	if (!$pending_load) {
	return response()->json(['message' => 'Service ID not found.'], 404);
	}


	$balance = $request->input('balance', $pending_load->campaign_price);
	$transaction_id = $request->input('transaction_id', $pending_load->transaction_id);
	$service_status = $request->input('service_status', $pending_load->status);



	$pending_load->transaction_id = $transaction_id;
	$pending_load->status = $service_status;


	$pending_load->save();
	return response()->json(["message" => "Updated service"], 200);

	$apiResponse = $this->getApiResponse($balance, $transaction_id, $service_status);


	return response()->json([$apiResponse], 200);
}

/**
* Get API response based on the pending load
*
* @param float $balance
* @param string $transaction_id
* @param string $service_status
* @return array
*/
private function getApiResponse($balance, $transaction_id, $service_status)
{

	$device_id = 1;


	return [
	"device_id" => $device_id,

	"balance" => $balance,
	"transaction_id" => $transaction_id,
	"service_status" => $service_status,
	];
}
private function mapTelco($operatorId)
{

	$cleanedOperatorId = preg_replace('/^88/', '', $operatorId);


	$telcoMap = [
	'gp' => 'GrameenPhone',
	'airtel' => 'Airtel',
	'robi' => 'Robi',
	'teletalk' => 'Teletalk',
	'blink' => 'Banglalink',
	];


	return $telcoMap[$cleanedOperatorId] ?? 'Unknown';
}

public function handleSms(Request $request)
{
$validator = Validator::make($request->all(), [
'simid' => 'required|string',
'operator' => 'required|string',
'text' => 'required|string',
'sender' => 'required|string',
'sc_datetime' => 'required',
'simslot' => 'required',
]);

// Check if validation fails
if ($validator->fails()) {
return response()->json([
'error' => 'Validation failed',
'messages' => $validator->errors()
], 422); // 422 Unprocessable Entity
}


$message = LoadSimMessages::create([
'user_id' => null,
'sim_no' => $request->simid,
'operator_company' => $request->operator,
'message' => $request->text,
'sender' => $request->sender,
'serial_id' => null,
'status' => 1,
'created_at' => now(),
'updated_at' => now(),
]);

    // Extract the last 10 digits from the message (Phone Number)
    preg_match('/\b\d{10}\b/', $message->message, $matches);
    $lastTenDigits = $matches[0] ?? null;

    // Extract the transaction ID (stopping at the first full stop)
    preg_match('/Transaction\s+number\s+is\s+([A-Za-z0-9.]+)/', $message->message, $transactionMatches);
    $transactionId = $transactionMatches[1] ?? null;

    // Extract the amount from the message
    preg_match('/\b\d+(?:\.\d{1,2})?\b/', $message->message, $amountMatches);
    $extractedAmount = $amountMatches[0] ?? null;

    // If both phone number and amount are extracted, proceed
    if ($lastTenDigits && $extractedAmount) {
        // Introduce a delay of 5 seconds before checking the pending load
        sleep(5); // This pauses execution for 5 seconds

        $pendingLoad = LoadCamPending::where('targeted_number', 'like', '%' . $lastTenDigits)
            ->where('status', 3) // Only check pending loads with status 3
            ->where('campaign_price', $extractedAmount) // Match the amount
            ->first();

        if ($pendingLoad && $transactionId) {
            DB::transaction(function () use ($pendingLoad, $message, $transactionId) {
                // Check if the message contains "successful"
                if (stripos($message->message, 'successful') !== false) {
                    // Insert into `load_campaigns`
                    DB::table('load_campaigns')->insert([
                        'id' => $pendingLoad->id,
                        'user_id' => $pendingLoad->user_id,
                        'operator_id' => $pendingLoad->operator_id,
                        'sms_id' => $pendingLoad->sms_id,
                        'campaign_id' => $pendingLoad->campaign_id,
                        'targeted_number' => $pendingLoad->targeted_number,
                        'owner_name' => $pendingLoad->owner_name,
                        'package_id' => $pendingLoad->package_id,
                        'number_type' => $pendingLoad->number_type,
                        'campaign_type' => $pendingLoad->campaign_type,
                        'campaign_price' => $pendingLoad->campaign_price,
                        'remarks' => $pendingLoad->remarks,
                        'transaction_id' => $transactionId,
                        'status' => 1,  // Status as successful
                        'created_at' => $pendingLoad->created_at,
                        'updated_at' => now(),
                    ]);

                    // Insert into `load_campaign30days`
                    DB::table('load_campaign30days')->insert([
                        'id' => $pendingLoad->id,
                        'user_id' => $pendingLoad->user_id,
                        'operator_id' => $pendingLoad->operator_id,
                        'sms_id' => $pendingLoad->sms_id,
                        'campaign_id' => $pendingLoad->campaign_id,
                        'targeted_number' => $pendingLoad->targeted_number,
                        'owner_name' => $pendingLoad->owner_name,
                        'package_id' => $pendingLoad->package_id,
                        'number_type' => $pendingLoad->number_type,
                        'campaign_type' => $pendingLoad->campaign_type,
                        'campaign_price' => $pendingLoad->campaign_price,
                        'remarks' => $pendingLoad->remarks,
                        'transaction_id' => $transactionId,
                        'status' => 1,  // Status as successful
                        'created_at' => $pendingLoad->created_at,
                        'updated_at' => now(),
                    ]);

                    // Delete from pending table
                    $pendingLoad->delete();
                } else {

                    $pendingLoad->update([
                        'status' => 1,
                    ]);
                }
            });
        }
    }

return response()->json([
"operator" => $message->operator_company,
'text' => $message->message,
'sender' => $message->sender,
'simid' => $message->sim_no,
'deviceid' => 1,
"deviceinfo" => "Samsung Galaxy S21, Android 11",
'simslot' => $request->simslot,
'sc_datetime' => $message->created_at->toIso8601String(),
], 201);


//$messages = LoadSimMessages::latest()->take(10)->get()->map(function ($message) {
//return [
//'operator' => $message->operator_company,
//'text' => $message->message,
//'sender' => $message->sender,
//'simid' => $message->sim_no,
//'deviceid' => "abcd1234xyz",
//'deviceinfo' => "Samsung Galaxy S21, Android 11",
//'simslot' => 1,
//'sc_datetime' => $message->created_at->toIso8601String(),
//];
//});


return response()->json(["success"=>False], 400);
}


}
