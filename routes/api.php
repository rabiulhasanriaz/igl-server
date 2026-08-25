<?php

use Illuminate\Http\Request;

use App\Http\Controllers\Api\LoadApiController; // Ensure you import the correct controller
use App\Http\Controllers\Api\Webhook\IRechargeWebhookController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/*Route::get('/user', function (Request $request) {
    dd($request);
});*/

Route::namespace('Api')->prefix('v1')->group(function (){
    Route::match(['get', 'post'], 'send', 'ApiController@sendSms');
    Route::match(['get', 'post'],'balance', 'ApiController@showBalance');
    Route::match(['get', 'post'], 'sms-campaign-report', 'ApiController@smsCampaignReport');
    Route::match(['get', 'post'],'send-load', 'FlexiloadApiController@send_flexi_load');
});

Route::namespace('Api')->prefix('v2')->group(function (){
    Route::match(['get', 'post'], 'send', 'SmsSendDesktopController@sendSmsDesktop');
    Route::match(['get', 'post'],'balance', 'SmsSendDesktopController@showBalance');
    Route::match(['get', 'post'],'send-load', 'FlexiloadApiController@send_flexi_load');
});
Route::prefix('webhook')->group(function () {
    
    // Main webhook endpoint for iRecharge
    Route::post('/irecharge', [IRechargeWebhookController::class, 'handle'])
        ->name('webhook.irecharge');
    
    // Health check endpoint to verify webhook is accessible
    Route::get('/irecharge/health', [IRechargeWebhookController::class, 'health'])
        ->name('webhook.irecharge.health');
    
    // Optional: Get webhook status/logs (protected, for debugging)
    Route::get('/irecharge/status', [IRechargeWebhookController::class, 'status'])
        ->middleware('auth:api') // Protect this endpoint
        ->name('webhook.irecharge.status');
        Route::get('/irecharge/cleanup', [IRechargeWebhookController::class, 'cleanup'])
        ->name('webhook.irecharge.cleanup');
    // Optional: Test webhook endpoint (for testing purposes)
    Route::post('/irecharge/test', [IRechargeWebhookController::class, 'test'])
        ->name('webhook.irecharge.test');
});
// Route::namespace('Api')->group(function(){
// 	Route::get('getMitPendingsms',['uses'=>'SmsSendDesktopController@sms_pending']);
// });

// Route::namespace('Api')->group(function(){
// 	Route::get('updateMitSmsStatus',['uses'=>'SmsSendDesktopController@sms_message_store']);
// });

// Route::group(['prefix' => 'storeSms' , 'namespace' => 'Api'])

Route::get('pre/user/getId', 'PreUserController@getId');
Route::get('pre/user/store', 'PreUserController@store');


Route::prefix('irobotic')->group(function () {
    Route::match(['get', 'post'], 'get_list', [LoadApiController::class, 'getPendingFlexiloads']);
    Route::match(['get', 'post'], 'make_update/{service_id}', [LoadApiController::class, 'makeUpdate']);
    Route::match(['get', 'post'], 'smsin', [LoadApiController::class, 'handleSms']);

});


// Route::get('/user', function (Request $request) {
//     $asb_paid_by = $request->get('asb_paid_by');

//     $users = \App\Model\AccSmsBalance::with('companyName')->select(
//         'asb_pay_to',
//         DB::raw('SUM(asb_credit) as total_credit'),
//         DB::raw('SUM(asb_debit) as total_debit'),
//         DB::raw('(SUM(asb_credit) - SUM(asb_debit)) as balance')
//     )
//         ->when($asb_paid_by, function ($query) use ($asb_paid_by) {
//             return $query->where('asb_paid_by', $asb_paid_by);
//         })
//         ->groupBy('asb_pay_to')
//         ->paginate(5);
        

//     return response()->json($users);
    
// });
