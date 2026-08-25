<?php
use Illuminate\Support\Facades\DB;
use App\Jobs\SendMaskingNonMaskingSmsJob;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/','Auth\LoginController@maintenance')->name('maintenance');
// Route::get('web/testing', function(){
    
//     $start = microtime(true); 
//     $test = DB::table("acc_user_credit_histories")->count();
//     $time = microtime(true) - $start;
//     return $time." Total=".$test;
// });
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('employee/logout', 'Auth\EmployeeLoginController@logout')->name('employee.logout');
Route::post('/update-login-status', 'Auth\LoginController@update_login_status')->name('update-login-status');

Route::namespace('Auth')->name('auth.')->middleware('guest')->group(function () {
    Route::get('/login', 'LoginController@index')->name('login');
    Route::post('/login', 'LoginController@processLogin');
    Route::post('/forgot-password', 'LoginController@forgotPassword')->name('forgot_password');
    Route::get('/otp-verify', 'LoginController@otpForm')->name('otp.verify');
    Route::post('/otp-check', 'LoginController@checkOtp')->name('otp.check');
    Route::post('/otp-resend', 'LoginController@resendOtp')->name('otp.resend');

    Route::get('emp/login', 'EmployeeLoginController@index')->name('employeeLogin');
    Route::post('emp/login', 'EmployeeLoginController@login_process');
    Route::post('emp/forgot-password', 'EmployeeForgotPasswordController@recover_process')->name('employee_forgot_password');

});

Route::get('/home', 'Auth\HomeController@index')->middleware('auth')->name('goToHome');
Route::get('/get-user-balance', function () {
        return response()->json([
            'balance' => number_format(BalanceHelper::user_available_balance(Auth::id()), 2)
        ]);
    })->name('get-balance');

// Employee Route starts
Route::namespace('Employee')->name('employee.')->prefix('emp')->middleware(['RoleEmployee'])->group(function(){

    Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::post('/profile-update', 'ProfileController@profileUpdate')->name('profileUpdate');

    Route::get('', 'HomeController@index')->name('index');
    Route::get('My-Users', 'UsersController@all_users_info')->name('user_list');
    Route::get('low-balance-user-list', 'UsersController@low_balance_users_list')->name('low_balance_users_list');

    Route::get('/transaction-history/{user_id}', 'UsersController@transaction_history_particular')->name('transaction_history');

    Route::get('change-password', 'ChangePasswordController@change_password')->name('change_password');
    Route::post('change-password', 'ChangePasswordController@change_password_process')->name('change_password');

    Route::group(['prefix' => 'package' , 'namespace' => 'Flexiload' , 'as' => 'package.'],function(){
        Route::get('package-list','LoadController@package_list')->name('package-list');
        Route::post('package-flexiload', 'LoadController@packageFormProcess')->name('package-buy');
        Route::post('show-packages', 'LoadController@showPackagesByAjax')->name('show-packages-by-ajax');

        Route::get('package-history', ['uses' => 'LoadController@package_history'])->name('package-history');
    });

    Route::get('report','ReportController@report')->name('report');
    Route::get('report-download','ReportController@reportDownloadPdf')->name('report-download');
    Route::get('report-download-route2','ReportController@reportDesktopDownloadPdf')->name('report-download-route2');
    Route::get('report-download-route2-without','ReportController@reportDesktopDownloadPdfWithout')->name('report-download-route2-without');
});




/*----------------------------------------------
| ----------Start Main Admin/root route------------
| -----------------------------------------------
*/

Route::namespace('Admin')->name('admin.')->middleware(['auth', 'RoleRoot'])->prefix('root')->group(function () {

    // ---show dashboard page
    Route::get('/', 'HomeController@index')->name('index');
    
    Route::get('data-count','HomeController@getCount')->name('data-count');
    Route::get('data-cost','HomeController@getCost')->name('data-cost');
    
        Route::get('/scheduled-count', 'HomeController@getScheduledCount')->name('scheduled-count');
    Route::get('/scheduled-cost', 'HomeController@getScheduledCost')->name('scheduled-cost');
    
    Route::get('daily-sms-data', 'HomeController@getDailySmsData')
    ->name('daily-sms-data');
    Route::get('today-sms-counts', 'HomeController@getTodaySmsCounts')->name('today-sms-counts');
Route::get('monthly-sms-count', 'HomeController@getMonthlySmsCount')
    ->name('monthly-sms-count')
    ->middleware('auth');
    Route::get('/change-password', 'ProfileController@showChangePasswordForm')->name('change-password');
    Route::post('/change-password', 'ProfileController@updatePassword');
    Route::get('/all-loggedin-users', 'HomeController@loggedInUsers' )->name('loggedInUsers');

    Route::get('/pending-sms-campaigns', 'SmsCampaignController@showPendingSmsCampaigns')->name('pending-campaign-sms');
    Route::get('/accept-sms-campaigns/{id}', 'SmsCampaignController@acceptPendingSmsCampaigns')->name('accept-campaign-sms');

    Route::get('campaign-reshedule-permission','DynamicPermissionController@dynamic_schedule')->name('campaign-reshedule-permission');
    Route::post('campaign-reshedule-update','DynamicPermissionController@scheduleUpdate')->name('campaign-reshedule-update');
    

    Route::get('/accept-dynamic-campaigns/{id}', 'SmsCampaignController@acceptDynamicCampaigns')->name('accept-dynamic-sms');


    Route::get('/reject-sms-campaigns/{id}', 'SmsCampaignController@rejectPendingSmsCampaigns')->name('reject-campaign-sms');


    Route::get('/reject-dynamic-sms-campaigns/{id}', 'SmsCampaignController@rejectDynamicPendingSmsCampaigns')->name('reject-dynamic-campaign-sms');

    Route::get('api-permission','ApiPermissionController@api_user')->name('api-permission');
    Route::get('api-monitor', 'ApiPermissionController@apiMonitor')->name('api-monitor');
    Route::get('api-monitor/delete/{id}', 'ApiPermissionController@apiLogDelete')->name('api-monitor-delete');
Route::get('api-monitor/delete-all', 'ApiPermissionController@apiLogDeleteAll')->name('api-monitor-delete-all');
    Route::get('api-permission-active/{id}','ApiPermissionController@api_user_active')->name('api-permission-active');
    Route::get('api-permission-suspend/{id}','ApiPermissionController@api_user_suspend')->name('api-permission-suspend');

    Route::get('dynamic-permission','DynamicPermissionController@dynamic_user')->name('dynamic-permission');
    Route::get('dynamic-permission-active/{id}','DynamicPermissionController@dynamic_user_active')->name('dynamic-permission-active');
    Route::get('dynamic-permission-suspend/{id}','DynamicPermissionController@dynamic_user_suspend')->name('dynamic-permission-suspend');

    Route::get('campaign-permission','CampaignPermissionController@total_user')->name('campaign-permission');
    Route::get('campaign-permission-active/{id}','CampaignPermissionController@campaign_permission_active')->name('campaign-permission-active');
    Route::get('campaign-permission-suspend/{id}','CampaignPermissionController@campaign_permission_suspend')->name('campaign-permission-suspend');


    Route::get('route-2-report','DynamicPermissionController@route_2_report')->name('route-2-report');
    Route::get('route-2-report-ajax','DynamicPermissionController@route2DetailAjax')->name('route-2-report-ajax');


    Route::group(['prefix' => 'english' , 'as' => 'english.'],function(){
        Route::get('route-registers','DynamicPermissionController@route_registers')->name('route-registers');
        Route::post('route-register-store','DynamicPermissionController@route_register_store')->name('route-register-store');
        Route::get('assign-route','DynamicPermissionController@assign_route')->name('assign-route');
        Route::post('assign-route-store','DynamicPermissionController@assign_route_store')->name('assign-route-store');

        Route::get('route-edit/{id}','DynamicPermissionController@route_edit')->name('route-edit');
        Route::post('route-update/{id}','DynamicPermissionController@route_update')->name('route-update');
        Route::get('route-delete/{id}','DynamicPermissionController@route_delete')->name('route-delete');
        Route::get('assign-route-edit/{id}','DynamicPermissionController@assign_route_edit')->name('assign-route-edit');
        Route::post('assign-route-update/{id}','DynamicPermissionController@assign_route_update')->name('assign-route-update');
        Route::get('assign-route-delete/{id}','DynamicPermissionController@assigned_route_delete')->name('assign-route-delete');
    });

    Route::get('/system-configuration', 'SystemConfigurationController@showSystemConfiguration')->name('show-system-configuration');
    Route::post('/system-configuration', 'SystemConfigurationController@updateSystemConfiguration')->name('update-system-configuration');

    /* ----all route of reseller in admin panel----- */
    Route::prefix('reseller')->name('reseller.')->group(function () {
        Route::get('/', 'ResellerController@index')->name('index');
        Route::get('/create', 'ResellerController@create')->name('create');
        Route::post('/store', 'ResellerController@store')->name('store');
        Route::post('/update/{id}', 'ResellerController@update')->name('update')->where('id', '[0-9]+');
        Route::get('/edit/{id}', 'ResellerController@edit')->name('edit')->where('id', '[0-9]+');
        Route::get('/transaction-history/{id}', 'BalanceController@show')->name('transactionHistory')->where('id', '[0-9]+');
        Route::get('/price-view/{id}', 'SmsRateController@edit')->name('priceView')->where('id', '[0-9]+');
        Route::post('/price/update/{id}', 'SmsRateController@update')->name('priceView.update')->where('id', '[0-9]+');
        Route::get('/tree-view', 'ResellerController@treeView')->name('tree');
        Route::get('/limit-apply', 'ResellerLimitController@limitApplyForm')->name('limitApply');
        Route::post('/limit/update/{id}', 'ResellerLimitController@limitUpdateForm')->name('limitUpdate');
        Route::get('/suspend/{id}', 'ResellerController@suspend')->name('suspend');
        Route::get('/active/{id}', 'ResellerController@active')->name('active');

        Route::get('/go-to-user/{id}', 'ResellerController@goToThisAccount')->name('goToThisAccount');

        Route::get('/employee-limit', 'EmployeeController@employee_limit_form_view')->name('employee_limit');
        Route::post('/employee-limit', 'EmployeeController@employee_limit_process');
         Route::get('/sms-monitoring/chart-data', 'ResellerController@getSmsChartData')->name('sms_monitoring.chart_data');
         Route::get('/sms-monitoring/user-data', 'ResellerController@getSmsUsersData')->name('sms_monitoring.users_data');
        Route::get('/sms-monitoring/stream', 'ResellerController@streamSmsUpdates')->name('sms_monitoring.stream');
        Route::get('/sms-monitoring/{date?}', 'ResellerController@smsMonitoringadminDashboard')->name('sms_monitoring');
        
        
        
        
         
         
         
    });


    /* ----end route of reseller in admin panel----- */


    /* ----all route of reseller in admin panel----- */
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('/', 'EmployeeController@index')->name('index');
        Route::view('/create', 'admin.employee.employee_registration')->name('create');
        Route::post('/store', 'EmployeeController@store')->name('store');
        Route::post('/update/{id}', 'EmployeeController@update')->name('update')->where('id', '[0-9]+');
        Route::get('/edit/{id}', 'EmployeeController@edit')->name('edit')->where('id', '[0-9]+');

    });
    /* ----end route of reseller in admin panel----- */


    /* ----all route of sender id in admin panel----- */
    Route::prefix('senderID')->name('senderID.')->group(function () {
        Route::get('/', 'SenderIDController@index')->name('index');
        Route::get('/add', 'SenderIDController@create')->name('create');
        Route::post('/add', 'SenderIDController@store')->name('create');
        Route::patch('/update', 'SenderIDController@update')->name('update');
        Route::get('/update-status/{id}', 'SenderIDController@updateStatus')->name('update_status')->where('id', '[0-9]+');
        Route::get('/delivery-senderID', 'SenderIDController@deliverySenderIDList')->name('deliverySenderIDList');
        Route::get('/delivery-senderID/check/{id}', 'SenderIDController@checkDeliverySenderID')->name('checkDeliverySenderID')->where('id', '[0-9]+');
        Route::post('/delivery-senderID/check/{id}', 'SenderIDController@updateDeliverySenderId')->where('id', '[0-9]+');
        Route::get('/delivery-senderID/check/{id}/{operator}/{number}', 'SenderIDController@panelCheckDeliverySenderId')->where('id', '[0-9]+')->name('panelCheckDeliverySenderID');

        Route::prefix('non-masking')->name('nonMaskingSenderID.')->group(function () {
            Route::get('/', 'NonMaskingSenderIDController@index')->name('index');
            Route::post('/store', 'NonMaskingSenderIDController@store')->name('store');
               Route::post('/import', 'NonMaskingSenderIDController@importExcel')->name('import');
            Route::get('/edit/{id}', 'NonMaskingSenderIDController@edit')->name('edit')->where('id', '[0-9]+');
            Route::post('/update/{id}', 'NonMaskingSenderIDController@update')->name('update')->where('id', '[0-9]+');
            Route::get('/delete/{id}', 'NonMaskingSenderIDController@delete')->name('delete')->where('id', '[0-9]+');
        });

        Route::prefix('user-senderID')->name('userSenderID.')->group(function () {
            Route::get('/', 'UserSenderIDController@index')->name('index');
            Route::post('/store', 'UserSenderIDController@store')->name('store');
            Route::get('/edit/{id}', 'UserSenderIDController@edit')->name('edit')->where('id', '[0-9]+');
            Route::post('/update/{id}', 'UserSenderIDController@update')->name('update')->where('id', '[0-9]+');
            Route::get('/delete/{id}', 'UserSenderIDController@delete')->name('delete')->where('id', '[0-9]+');
        });

    });
    /* ----end route of sender id in admin panel----- */


    /* ----all route of virtual number in admin panel----- */
  Route::prefix('virtual-number')->name('virtualNumber.')->group(function () {
    Route::get('/', 'VirtualNumberController@index')->name('index');
    Route::get('/add', 'VirtualNumberController@create')->name('create');
    Route::post('/store', 'VirtualNumberController@store')->name('store');
    Route::get('/edit/{id}', 'VirtualNumberController@edit')->name('edit');
    Route::post('/update/{id}', 'VirtualNumberController@update')->name('update');
    Route::get('/delete/{id}', 'VirtualNumberController@delete')->name('delete');
    Route::get('/balance-query/{id}', 'VirtualNumberController@balanceCheck')->name('balance_query');
    
    // New low balance routes
    Route::get('/low-balance', 'VirtualNumberController@lowBalance')->name('low_balance');
    Route::get('/refresh-low-balance', 'VirtualNumberController@refreshLowBalance')->name('refresh_low_balance');
    Route::get('/bulk-balance', 'VirtualNumberController@bulkBalanceCheck')->name('bulk_balance');
       Route::get('/get-sender-ids', 'VirtualNumberController@getSenderIds')->name('getSenderIds');
    Route::post('/change-sender-for-pending', 'VirtualNumberController@changeSenderForPending')->name('changeSenderForPending');
    Route::get('/get-pending-count', 'VirtualNumberController@getPendingCount')->name('getPendingCount');
});
    /* ----end route of virtual number in admin panel----- */
    
Route::prefix('whitelisted-ip')->name('whitelistedIp.')->group(function () {
    Route::get('/', 'WhitelistedIpController@index')->name('index');
    Route::get('/add', 'WhitelistedIpController@create')->name('create');
    Route::post('/store', 'WhitelistedIpController@store')->name('store');
    Route::get('/edit/{id}', 'WhitelistedIpController@edit')->name('edit');
    Route::post('/update/{id}', 'WhitelistedIpController@update')->name('update');
    Route::get('/delete/{id}', 'WhitelistedIpController@delete')->name('delete');
    Route::get('/check-status/{id}', 'WhitelistedIpController@checkIpStatus')->name('check_status');
    Route::get('/non-whitelisted', 'WhitelistedIpController@nonWhitelistedUsers')->name('non_whitelisted');
    Route::get('/daily-usage/{id}', 'WhitelistedIpController@getDailyUsage')->name('daily_usage');

});

    /* ----all route of balance in admin panel----- */
    Route::prefix('balance')->name('balance.')->group(function () {
        Route::prefix('credit')->name('credit.')->group(function () {
            Route::get('/', 'BalanceController@cdtCreate')->name('create');
            Route::post('/store', 'BalanceController@cdtStore')->name('store');
        });

        Route::prefix('debit')->name('debit.')->group(function () {
            Route::get('/', 'BalanceController@dbtCreate')->name('create');
            Route::post('/store', 'BalanceController@dbtStore')->name('store');
        });
    });
    /* ----end route of balance in admin panel----- */


    /* ----all route of category contact in admin panel----- */
    Route::prefix('categoryContact')->name('categoryContact.')->group(function () {
        Route::get('/', 'CatContactController@index')->name('index');
        Route::post('/store', 'CatContactController@storeCategory')->name('storeCategory');
        Route::post('/edit', 'CatContactController@updateCategory')->name('updateCategory');
        Route::get('/delete/{id}', 'CatContactController@deleteCategory')->name('deleteCategory')->where('id', '[0-9]+');
        Route::get('/{slug}', 'CatContactController@show')->name('show');
        Route::post('/storeContact', 'CatContactController@storeContact')->name('storeContact');
        Route::post('/importContact', 'CatContactController@importContact')->name('importContact');
        Route::post('/updateContact', 'CatContactController@updateContact')->name('updateContact');
        Route::get('/{slug}/delete/{id}', 'CatContactController@deleteContact')->name('deleteContact');
    });

// Root Flexiload routes
    Route::group(['prefix' => 'flexiload', 'as' => 'flexiload.', 'namespace' => 'Flexiload'], function(){
        Route::get('allUsers', ['uses' => 'LoadController@index'])->name('allUsers');

        Route::post('edit', ['uses' => 'LoadController@customizeLoadInfo'])->name('customize');
        Route::get('active-inactive', ['uses' => 'LoadController@makeActiveInactive'])->name('activeInactive');


        Route::post('add_package', ['uses' => 'LoadController@addPackage'])->name('addPackage');
        Route::post('edit_package', ['uses' => 'LoadController@editPackage'])->name('editPackage');

        Route::get('all-resellers-comissions', ['uses'=>'LoadController@setComissionsView'])->name('setComissions');
        Route::post('all-resellers-comissions', ['uses'=>'LoadController@setComissions']);

        Route::get('allPackages', ['uses' => 'LoadController@viewAllPackages'])->name('allPackages');
        Route::get('reload-load/{id}', 'LoadController@reload_load')->name('reload-load');
        Route::get('reload-load-all', 'LoadController@reload_all')->name('reload-load-all');
        Route::get('set-trx-id', 'LoadController@set_trx_id_page')->name('set-trx-page');
        Route::post('update-trx-id/{id}','LoadController@update_trx_id')->name('update-trx-id');

        Route::get('balance-enquiry','LoadController@balance_enquiry')->name('balance-enquiry');
        Route::get('load-message','LoadController@load_message')->name('load-message');
                Route::get('user-wise-history', 'LoadController@userWiseLoadHistory')->name('user-wise-history');
        Route::get('number-wise-history', 'LoadController@numberWiseLoadHistory')->name('number-wise-history');
        Route::get('user-detailed-history/{userId}', 'LoadController@userDetailedHistory')->name('user-detailed-history');
        Route::get('number-detailed-history/{number}', 'LoadController@numberDetailedHistory')->name('number-detailed-history');
        Route::get('history-summary', 'LoadController@loadHistorySummary')->name('history-summary');




        // Make Controller Load API=====================================================
        // =============================================================================
        Route::get('load-api','LoadController@load_api')->name('load-api');
        Route::get('load-api-details/{operator_user_port}','LoadController@load_api_details')->name('load-api-details');

        
        Route::get('load-api-create','LoadController@load_api_create')->name('load-api-create');
        Route::post('load-api-store','LoadController@load_api_store')->name('load-api-store');


        Route::get('load-api-edit/{operator_id}','LoadController@load_api_edit')->name('load-api-edit');
        Route::post('load-api-update/{operator_id}','LoadController@load_api_update')->name('load-api-update');
        

        Route::get('load-api-delete/{operator_id}','LoadController@load_api_delete')->name('load-api-delete');
        Route::get('load-api-inactive/{operator_id}','LoadController@load_api_inactive')->name('load-api-inactive');
        Route::get('load-api-active/{operator_id}','LoadController@load_api_active')->name('load-api-active');

        Route::post('api_one_status','LoadController@api_one_status')->name('api-one-status');
        Route::post('api_two_status','LoadController@api_two_status')->name('api-two-status');

        
        Route::get('load-cron','FlexiControllerNew@load_cron')->name('load-cron');
        Route::get('get-flexiload-report','FlexiControllerNew@getFlexiloadReport')->name('get_flexiload_report');
        Route::get('flexi-msg','FlexiControllerNew@flexiload_message_details')->name('flexi-msg');

        // End Controller Load API=======================================================
        // ==============================================================================



    });
    Route::group(['prefix' => 'reports' , 'as' => 'reports.'],function(){
        Route::get('sms-flexi-reports','ReportsController@sms_flexi_reports')->name('sms-flexi-reports');
        Route::get('operator-reports','ReportsController@operator_reports')->name('operator-reports');
         Route::get('balance-transaction-reports','ReportsController@balance_transaction_reports')->name('balance-transaction-reports');
           Route::get('balance-transaction-reports-export', 'ReportsController@exportBalanceTransactions')->name('balance-transaction-reports-export');
        Route::get('sender-operator-report', 'ReportsController@sender_operator_report')->name('sender-operator-report');
    Route::get('sender-operator-report-export', 'ReportsController@exportSenderOperatorReport')->name('sender-operator-report-export');
    Route::get('sender-operator-data', 'ReportsController@getSenderOperatorData')->name('sender-operator-data');
    
           });
    /* ----end route of category contact in admin panel----- */
    Route::prefix('ticket')->name('ticket.')->group(function () {

            // Tickets
            Route::get('/tickets', 'AdminTicketController@index')->name('tickets');
            Route::get('/tickets/{id}', 'AdminTicketController@show')->name('tickets.show');
            Route::post('/tickets/{id}/assign', 'AdminTicketController@assignTicket')->name('tickets.assign');
            Route::post('/tickets/{id}/update-status', 'AdminTicketController@updateStatus')->name('tickets.updateStatus');
            Route::post('/tickets/{id}/update-priority', 'AdminTicketController@updatePriority')->name('tickets.updatePriority');
            Route::post('/tickets/{id}/reply', 'AdminTicketController@storeReply')->name('tickets.reply');
            Route::delete('/tickets/{id}', 'AdminTicketController@destroy')->name('tickets.destroy');

            // Filtered / Special Tickets
            Route::get('/my-tickets', 'AdminTicketController@myAssignedTickets')->name('myTickets');
            Route::get('/unassigned-tickets', 'AdminTicketController@unassignedTickets')->name('unassignedTickets');

            // Stats
            Route::get('/stats', 'AdminTicketController@getStats')->name('stats');

            // Bulk Actions

            Route::post('/bulk-assign', 'AdminTicketController@bulkAssign')->name('bulkAssign');
        Route::post('/bulk-update-status', 'AdminTicketController@bulkUpdateStatus')->name('bulkUpdateStatus');
        Route::post('/bulk-update-priority', 'AdminTicketController@bulkUpdatePriority')->name('bulkUpdatePriority');
        Route::post('/bulk-delete', 'AdminTicketController@bulkDelete')->name('bulkDelete');
        });
     Route::view('/changeLoginBackground', 'admin/extraOperations/changeLoginBackground')->name('changeBackground');

     Route::post('/changeLoginBackground', 'ChangeLoginBackgroundController@changeLoginBackground')->name('changeBackgroundPost');

     Route::view('/deleteDataBeforeOneMonth', 'admin/extraOperations/delete_sms_data_before_one_month')->name('deleteDataBeforeOneMonth');

     Route::post('/deleteDataBeforeOneMonth', 'DeleteDataBeforeOneMonthController@delete_data_before_one_month');
});

/*----------------------------------------------
| ----------End Main Admin/root route------------
| -----------------------------------------------
*/





/*----------------------------------------------
| -----------Start Reseller Route List----------
| ----------------------------------------------
*/
Route::namespace('Reseller')->name('reseller.')->middleware(['auth', 'RoleReseller'])->prefix('reseller')->group(function () {
    // show index page
    Route::get('/', 'HomeController@index')->name('index');
    Route::get('/my-price', 'HomeController@showPriceList')->name('priceList');
    Route::get('/sender-id', 'HomeController@showSenderIdList')->name('senderIDList');
    Route::get('/set-default-sender/{id}', 'HomeController@setDefaultSender')->name('setDefaultSender');
        Route::get('monthly-sms-count-reseller', 'HomeController@getMonthlySmsCount_reseller')
     ->name('monthly-sms-count-reseller');
    Route::get('/transaction-history', 'BalanceController@totalTransactionHistory')->name('transactionHistory');
    Route::get('/change-password', 'ProfileController@showChangePasswordForm')->name('change-password');
    Route::post('/change-password', 'ProfileController@updatePassword');
    Route::get('/profile', 'ProfileController@showProfile')->name('profile');
    Route::post('/profile', 'ProfileController@updateProfile');

    Route::get('/send-sms-to-all-user-and-reseller', 'SendSmsController@send_sms_to_all_view')->name('sendSmsToAll');
    Route::post('/send-sms-to-all-user-and-reseller', 'SendSmsController@send_sms_to_all_process')->name('sendSmsToAll');

    Route::get('/transaction/history', 'BalanceController@getTransactionHistory')->name('transaction.history');



    // user routes
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', 'UserController@index')->name('index');
        Route::get('/create', 'UserController@create')->name('create');
        Route::post('/store', 'UserController@store')->name('store');
        Route::get('/edit/{id}', 'UserController@edit')->name('edit')->where('id', '[0-9]+');
        Route::post('/update/{id}', 'UserController@update')->name('update')->where('id', '[0-9]+');
        Route::get('/suspend-user', 'UserController@suspendUser')->name('suspendUser');
        Route::get('/suspend/{id}', 'UserController@suspend')->name('suspend');
        Route::get('/active/{id}', 'UserController@active')->name('active');
        Route::get('/delete/{id}', 'UserController@delete')->name('delete');
    Route::get('/low-balance-users', 'UserController@lowBalanceUsers')->name('low_balance');
    Route::get('/low-balance-users-data', 'UserController@getLowBalanceUsersData')->name('low_balance_data');
    Route::get('/low-balance-stats', 'UserController@getLowBalanceStats')->name('low_balance_stats');
    Route::get('/low-balance-export', 'UserController@exportLowBalanceUsers')->name('low_balance_export');
    Route::post('/send-reminder', 'UserController@sendReminder')->name('send_reminder');
        Route::get('/price-view/{id}', 'SmsRateController@edit')->name('priceView')->where('id', '[0-9]+');
        Route::post('/price/update/{id}', 'SmsRateController@update')->name('priceUpdate');
        Route::post('/price/bulk-update', 'SmsRateController@bulkUpdate')->name('bulk-update');
        Route::get('/transaction-history/{id}', 'BalanceController@show')->name('transactionHistory')->where('id', '[0-9]+');

        Route::get('/go-to-user/{id}', 'UserController@goToThisAccount')->name('goToThisAccount');
        Route::get('/inactive-user', 'UserController@smsUsersNotLast10Days')->name('inactiveUser');
    Route::get('/get-balance', 'UserController@getUserBalance')->name('getBalance');
    
        Route::get('/sms-activity', 'UserController@smsUsersLastDayActivity')->name('smsActivity');
        Route::get('/sms-activity-without-balance', 'UserController@smsUsersLastDayActivitywithoutbalance')->name('smsActivitywithoutbalance');
         Route::get('/smsMonitoringDashboard/{date?}', 'UserController@smsMonitoringDashboard')->name('smsMonitoringDashboard');
         
         
         
    });

    // reseller employee routes
    Route::prefix('employee')->name('employee.')->group(function(){
        Route::get('/', 'EmployeeController@index')->name('index');
        Route::get('/create', 'EmployeeController@create')->name('create');
        Route::post('/store', 'EmployeeController@store')->name('store');
        Route::get('/edit/{id}', 'EmployeeController@edit')->name('edit');
        Route::post('/update/{id}', 'EmployeeController@update')->name('update');
        Route::get('/pay_balance', 'EmployeeController@pay_balance_create')->name('pay_balance');
        Route::post('/pay_balance', 'EmployeeController@pay_balance_process');

        Route::get('/asignUser', 'EmployeeController@asignUser')->name('asignUser');
        Route::post('/asignUser', 'EmployeeController@asignUserProcess');
        Route::get('/employee-users/{emp_id}', 'EmployeeController@employee_users_list')->name('employee_users_list');
        Route::get('/change-employee-for-a-user', 'EmployeeController@changeEmployeView')->name('change_employee');
        Route::post('/change-employee-for-a-user', 'EmployeeController@changeEmployeeProcess');


    });

    // balance route
    Route::prefix('balance')->name('balance.')->group(function () {
        // balance credit
        Route::prefix('credit')->name('credit.')->group(function () {
            Route::get('/', 'BalanceController@cdtCreate')->name('create');
            Route::post('/store', 'BalanceController@cdtStore')->name('store');
        });

        // balance debit
        Route::prefix('debit')->name('debit.')->group(function () {
            Route::get('/', 'BalanceController@dbtCreate')->name('create');
            Route::post('/store', 'BalanceController@dbtStore')->name('store');
        });

    });
});







/*----------------------------------------------
| -----------End Reseller Route List------------
| ------------------------------------------------
*/



















/*----------------------------------------------
| -----------Start User Route List----------
| ----------------------------------------------
*/

Route::namespace('User')->name('user.')->middleware(['auth', 'RoleUser'])->group(function () {
    Route::get('/', 'HomeController@index')->name('index');
    Route::get('/statistics', 'HomeController@getStatistics')->name('statistics');
    
   
    Route::get('/sender-id', 'SenderIDController@index')->name('senderIDList')->middleware('PermissionSms');
    Route::get('/set-default-sender/{id}', 'SenderIDController@setDefaultSender')->name('setDefaultSender')->middleware('PermissionSms');
    Route::get('/price', 'PriceController@index')->name('priceList')->middleware('PermissionSms');
    Route::get('/price-dynamic', 'PriceController@dynamic')->name('priceListDynamic')->middleware('PermissionDynamic');
    Route::get('/developerApi', 'DeveloperApiController@index')->name('developerApi')->middleware('PermissionSms');
Route::post('/developerApi/update-whitelist-ip', 'DeveloperApiController@updateWhiteListIp')->name('whitelist.ip');
    Route::post('/developerApi/change', 'DeveloperApiController@changeApi')->name('changeApi')->middleware('PermissionSms');

    Route::get('/dynamicApi', 'DesktopApiController@index')->name('desktopApi')->middleware('PermissionDynamic');
    Route::post('/dynamicApi/change', 'DesktopApiController@changeApiDesktop')->name('changeApiDesktop')->middleware('PermissionDynamic');

    Route::get('/change-password', 'ProfileController@showChangePasswordForm')->name('change-password');
    Route::post('/change-password', 'ProfileController@updatePassword');
    Route::post('/update-otp', 'ProfileController@updateOTP')->name('update-otp');
  
    Route::get('/change-flexipin', 'ProfileController@updateFlexipinForm')->name('change-flexipin');
    Route::post('/change-flexipin', 'ProfileController@updateFlexipin');
    Route::get('/profile', 'ProfileController@showProfile')->name('profile');
    Route::post('/profile', 'ProfileController@updateProfile');
    Route::post('forgot-flexipin', 'ProfileController@password_for_pin')->name('forgot-flexipin');
    Route::get('developer-api', 'ProfileController@developerApi')
        ->name('developer.api');

    Route::get('/topup-balance', 'BalanceController@showTopUpForm')->name('balance.topup');
    Route::post('/topup-balance/initiate', 'BalanceController@initiatePayment')->name('balance.initiate');
    Route::post('/topup-balance/success', 'BalanceController@paymentSuccess')->name('balance.success');
    Route::post('/topup-balance/fail', 'BalanceController@paymentFail')->name('balance.fail');
    Route::post('/topup-balance/cancel', 'BalanceController@paymentCancel')->name('balance.cancel');
    Route::post('/topup-balance/ipn', 'BalanceController@paymentIPN')->name('balance.ipn');
    Route::get('/bkash/callback', 'BalanceController@bkashCallback')->name('balance.bkash.callback');

    /*start route group of sms send*/
    Route::prefix('sms')->name('sms.')->middleware('PermissionSms')->group(function () {
        Route::get('/send', 'SmsSendController@create')->name('create');
        Route::post('/send/single-sms', 'SmsSendController@storeSingleSms')->name('storeSingleSms');
        Route::post('/send/upload-file', 'SmsSendController@storeUploadFileSms')->name('storeUploadFileSms');
        Route::post('/send/check-upload-file', 'SmsSendController@checkUploadFile')->name('checkUploadFile');
        /*Route::post('/send/upload-file1', 'SmsSendController@storeUploadFileSms1')->name('storeUploadFileSmsOld');*/
        Route::post('/send/group-contact', 'SmsSendController@storeGroupContactSms')->name('storeGroupContactSms');
        Route::post('/send/dynamic-sms', 'SmsSendController@storeDynamicSms')->name('storeDynamicSms');
        Route::post('/send/check-dynamic-file', 'SmsSendController@checkDynamicFile')->name('checkDynamicFile');
        Route::post('/send/employee-group-contact', 'SmsSendController@storeEmployeeGroupContactSms')->name('storeEmployeeGroupContactSms');
        Route::get('/campaign', 'SmsSendController@campaignCreate')->name('campaignCreate');
        Route::post('/campaign/store', 'SmsSendController@storeCampaignSms')->name('storeCampaignSms');
        Route::post('/change_sms_shedule_time', 'SmsReportController@change_shedule_sms_time')->name('change_shedule_sms_time');

        Route::get('/checkApi', 'SmsSendController@checkApi')->name('checkApi');
    });

    Route::group(['prefix' => 'dynamic-sms' , 'as' => 'dynamic-sms.' , 'middleware' => 'PermissionDynamic'],function(){
        Route::get('/send', 'SmsDesktopSendController@create')->name('send');
        Route::post('/send/single-sms-modem', 'SmsDesktopSendController@storeSingleSms')->name('storeSingleSmsModem');
        Route::post('/send/upload-file-modem', 'SmsDesktopSendController@storeUploadFileSms')->name('storeUploadFileSmsModem');
        Route::post('/send/check-upload-file-modem', 'SmsDesktopSendController@checkUploadFile')->name('checkUploadFileModem');
        Route::post('/send/group-contact', 'SmsDesktopSendController@storeGroupContactSms')->name('storeGroupContactSms');
        Route::post('/send/group-contact-modem', 'SmsDesktopSendController@storeGroupContactSms')->name('storeGroupContactSmsModem');
        
        Route::post('/send/dynamic-sms-modem', 'SmsDesktopSendController@storeDynamicSms')->name('storeDynamicSmsModem');
Route::post('/send/check-dynamic-file-modem', 'SmsDesktopSendController@checkDynamicFile')->name('checkDynamicFileModem');

        Route::get('/campaign','SmsDesktopSendController@campaignCreate')->name('campaign');
        Route::post('/campaign/store', 'SmsDesktopSendController@storeCampaignSms')->name('storeCampaignSms');
    });

        // user Flexiload Routes
    Route::get('package-flexiload', ['uses' => 'Flexiload\LoadController@packageForm'])->name('create');
    Route::post('package-flexiload', ['uses' => 'Flexiload\LoadController@packageFormProcess']);
    
        
        
    Route::post('offer-buy', ['uses' => 'Flexiload\LoadController@buyOfferProcess'])->name('offer.buy');
    Route::get('offer-check', ['uses' => 'Flexiload\LoadController@offerForm'])->name('offer.check');
    Route::post('offer-check', ['uses' => 'Flexiload\LoadController@checkOffer']);
    
   Route::group([
        'prefix' => 'support',
        'as' => 'support.',

    ], function () {

        Route::get('/tickets', 'SupportTicketController@index')
            ->name('tickets');

        Route::get('/tickets/create', 'SupportTicketController@create')
            ->name('tickets.create');

        Route::post('/tickets/store', 'SupportTicketController@store')
            ->name('tickets.store');

        Route::get('/tickets/{id}', 'SupportTicketController@show')
            ->name('tickets.show');

        Route::post('/tickets/{id}/reply', 'SupportTicketController@storeReply')
            ->name('tickets.reply');

        Route::post('/tickets/{id}/close', 'SupportTicketController@closeTicket')
            ->name('tickets.close');

        Route::post('/tickets/{id}/reopen', 'SupportTicketController@reopenTicket')
            ->name('tickets.reopen');

        Route::get('/tickets/stats', 'SupportTicketController@getStats')
            ->name('tickets.stats');
    });


    
    Route::post('show-packages', ['uses'=>'Flexiload\LoadController@showPackagesByAjax'])->name('show-packages-by-ajax');
    Route::get('package-history', ['uses' => 'Flexiload\LoadController@package_history'])->name('package_history');
    

    Route::group(['namespace' => 'Flexiload', 'prefix' => 'flexiload', 'as' => 'flexiload.' , 'middleware' => 'PermissionFlexi'], function(){
        

        Route::get('singleLoadForm', ['uses'=>'LoadController@flexiloadFormView'])->name('flexiloadForm');
        Route::post('singleLoadForm', ['uses'=>'LoadController@flexiloadFormProcess']);

        Route::get('bulkLoadForm', ['uses'=>'LoadController@bulkLoadForm'])->name('bulkLoadForm');
        Route::post('bulkLoadForm', ['uses'=>'LoadController@bulkLoadFormProcess']);

        Route::post('/send/check-dynamic-file', 'LoadController@checkDynamicFile')->name('checkDynamicFile');

        Route::post('send-flexiload-to-a-book', ['uses'=>'LoadController@flexiload_book'])->name('flexiload_book');

        Route::get('history', ['uses' => 'LoadController@history'])->name('history');
        Route::get('download-all-current-month', ['uses' => 'LoadController@downloadAllCurrentMonth'])->name('downloadAllCurrentMonth');
        Route::get('history-archieve', ['uses' => 'LoadController@history_archieve'])->name('history_archieve');


        Route::post('create-flexibook', ['uses'=>'FlexibookController@createFlexibook'])->name('createFlexibook');
        Route::post('update-flexibook', ['uses'=>'FlexibookController@updateFlexibook'])->name('updateFlexibook');

        Route::post('update-status','FlexibookController@updateNumberstatus')->name('number-status');
        
        Route::get('delete-flexibook/{flexibook_id}', ['uses'=>'FlexibookController@deleteFlexibook'])->name('deleteFlexibook');

        // Route::post('import-contacts',['uses'=>'FlexibookController@importContacts'])->name('importContact');

        Route::post('store-a-single-contact', ['uses'=>'FlexibookController@storeSingleNumber'])->name('storeSingleNumber');
        Route::get('flexibook-details/{flexibook_id}', ['uses'=>'FlexibookController@flexibook_details'])->name('flexibook_details');
        Route::post('update-flexibook-contact', ['uses'=>'FlexibookController@updateContact'])->name('updateContact');
        Route::get('delete-flexibook-contact/{contact_id}', ['uses'=>'FlexibookController@deleteContact'])->name('deleteContact');



        Route::get('flexibook', ['uses'=>'FlexibookController@createFlxibookForm'])->name('flexibook_create');
        Route::post('import-contacts', ['uses'=>'FlexibookController@flexibookFileProcess'])->name('importContact');

        Route::post('show-packages', ['uses'=>'LoadController@showPackagesByAjax'])->name('show-packages-by-ajax');

        Route::post('get-current-month-campaign-data-by-ajax', ['uses'=>'LoadController@getCurrentMonthyCampaignHistoryByAjax'])->name('get-current-month-history-by-ajax');
        Route::post('get-campaign-data-by-ajax', ['uses'=>'LoadController@getCampaignHistoryByAjax'])->name('get-history-by-ajax');

        Route::get('download-flexireport', ['uses'=>'LoadController@downloadFlexiReport'])->name('downloadFlexiReport');
        Route::get('download-current-month-flexireport', ['uses'=>'LoadController@downloadCurrentMonthFlexiReport'])->name('downloadCurrentMonthFlexiReport');


        Route::get('developer-api', 'DeveloperFlexiloadApiController@index')->name('developer-api');
        Route::post('developer-api/change', 'DeveloperFlexiloadApiController@changeApi')->name('change-api');
    });
    /*end route group of sms send*/

    /*Start template routing*/
    Route::prefix('templates')->name('template.')->middleware('PermissionSms')->group(function () {
        Route::get('/', 'TemplateController@index')->name('index');
        Route::post('/store', 'TemplateController@store')->name('store');
        Route::post('/update', 'TemplateController@update')->name('update');
        Route::get('/delete/{id}', 'TemplateController@delete')->name('delete');
    });
    /*End template routing*/

    /*start report routing*/
    Route::prefix('reports')->name('reports.')->middleware('PermissionSms')->group(function () {

        /*start view dlr*/
        Route::get('/pending-sms', 'SmsReportController@pending_for_approval_sms_report')->name('pending_sms');
        Route::get('/rejected-sms', 'SmsReportController@rejected_sms_report')->name('rejected_sms');
        Route::get('/todays-sms', 'SmsReportController@todays_sms_report')->name('todays_sms');
        Route::get('/todays-sms/download/{campaign_id}', 'SmsReportController@download_todays_report')->name('download_todays_report');
        Route::get('/archived-sms', 'SmsReportController@archived_sms_report')->name('archived_sms');
        Route::get('/api-reports-download', 'SmsReportController@download_api_report')->name('api-reports-download'); 

        Route::get('/api-today-reports-download','SmsReportController@download_today_api_total_report')->name('api-today-reports-download');
        Route::get('/today-report-csv', 'SmsReportController@downloadTodayReportCsv')->name('today-report-csv');

         Route::get('/api-reports-total-download', 'SmsReportController@download_api_total_report')->name('api-reports-total-download'); 
        
        Route::get('/archived-sms/download/{campaign_id}', 'SmsReportController@download_archived_report')->name('download_archived_report');
        Route::get('/total-report-download', 'SmsReportController@reportDownload')->name('total-report-download');
        Route::get('api-report-ajax','SmsReportController@show_api_report_ajax')->name('api-report-ajax');
        Route::get('today-report-ajax','SmsReportController@show_todays_report_ajax')->name('today-report-ajax');
        
        Route::get('/download-archived-report-csv', 'SmsReportController@downloadArchivedReportCsv')->name('download-archived-report-csv');
        Route::get('/download-api-report-pdf', 'SmsReportController@downloadApiReportPdf')->name('download-api-report-pdf');
        Route::get('/download-archived-report-details-pdf', 'SmsReportController@downloadArchivedReportDetailsPdf')->name('download-archived-report-details-pdf');
        /*end view dlr*/

        /*start campaign dlr*/
        Route::get('/campaign/todays-campaign', 'SmsReportController@todays_campaign_sms_report')->name('todays_campaign');
        Route::get('/campaign/archived-campaign', 'SmsReportController@archived_campaign_report')->name('archived_campaign');
        /*start schedule sms*/
        Route::get('/schedule/pending_sms_report', 'SmsReportController@pending_sms_report')->name('schedule_pending_sms');

        Route::get('/schedule/today_sms_report', 'SmsReportController@today_sms_report')->name('schedule_today_sms');

        Route::get('/schedule/archieved-sms', 'SmsReportController@schedule_archieved_sms_report')->name('schedule_archieved_sms');
        Route::get('/schedule/general-sms', 'SmsReportController@schedule_general_sms_report')->name('schedule_general_sms');
        /*end schedule sms*/

        Route::get('bill-report', 'SmsBillReportController@showBillReport')->name('bill-report');
        Route::get('bill-report-download', 'SmsBillReportController@billReportDownload')->name('bill-report-download');

    });

    Route::group(['prefix' => 'dynamic-reports' , 'as' => 'reports.' , 'middleware' => 'PermissionDynamic'],function(){
        Route::get('/pending-sms', 'SmsReportController@dynamic_pending_sms_report')->name('pending-sms-dynamic');
        Route::post('reshedule-campaign/{id}','SmsReportController@campaignScheduleUpdate')->name('reschedule-campaign');
        Route::get('/reject-sms-campaign/{id}', 'SmsReportController@rejectPendingSmsCampaigns')->name('reject-sms-campaign');

        Route::get('/todays-sms', 'SmsReportController@todays_dynamic_sms_report')->name('todays-sms-dynamic');
        Route::get('today-report-ajax-dynamic','SmsReportController@show_todays_dynamic_report_ajax')->name('today-report-ajax-dynamic');
        Route::get('/todays-sms/download/{campaign_id}', 'SmsReportController@download_dynamic_todays_report')->name('download_dynamic_todays_report');
        Route::get('/dynamic-archived-sms', 'SmsReportController@dynamic_archived_sms_report')->name('dynamic-archived-sms');
        Route::get('api-report-ajax-dynamic','SmsReportController@show_dynamic_api_report_ajax')->name('api-report-ajax-dynamic');
        Route::get('/archived-sms-dynamic/download/{campaign_id}', 'SmsReportController@download_dynamic_archived_report')->name('download_dynamic_archived_report');

        Route::get('/campaign/todays-campaign', 'SmsReportController@todays_dynamic_campaign_sms_report')->name('todays_campaign_dynamic');
        Route::get('/campaign/archived-campaign', 'SmsReportController@archived_dynamic_campaign_report')->name('archived_campaign_dynamic');

    });
    /*end report routing*/

    Route::prefix('phonebook')->name('phonebook.')->middleware('PermissionSms')->group(function () {
        Route::get('/', 'ContactController@index')->name('index');
        Route::post('/store', 'ContactController@storeCategory')->name('storeCategory');
        Route::post('/updateCategory', 'ContactController@updateCategory')->name('updateCategory');
        Route::get('/deleteCategory/{id}', 'ContactController@deleteCategory')->name('deleteCategory');

        Route::get('/{id}', 'ContactController@show')->name('show')->where('id', '[0-9]+');
        Route::post('/storeContact', 'ContactController@storeContact')->name('storeContact');
        Route::post('/updateContact', 'ContactController@updateContact')->name('updateContact');
        Route::post('/importContact', 'ContactController@importContact')->name('importContact');
        Route::get('/deleteContact/{id}', 'ContactController@deleteContact')->name('deleteContact');
    });

    Route::group(['prefix' => 'user-balance-statements' , 'as' => 'user-balance-statements.'],function(){
        Route::get('balance','BalanceStatementsController@balance')->name('balance');
        Route::get('balance-pdf','BalanceStatementsController@balance_report_download')->name('balance-pdf');
    });

    Route::group(['prefix' => 'campaign-report' , 'as' => 'campaign-report.'], function(){
        Route::get('campaign-report','CampaignReportController@campaignReport')->name('campaign-report');
        Route::get('campaign-report-download','CampaignReportController@campaignReportDownloadPdf')->name('campaign-report-download');
        Route::get('campaign-desktop-report-download','CampaignReportController@CampaignreportDesktopDownloadPdf')->name('campaign-desktop-report-download');
    });
    
});

/*----------------------------------------------
| -----------End User Route List------------
| ------------------------------------------------
*/





/*----------------------------------------------
| -----------Start Ajax Route List------------
| ------------------------------------------------
*/
Route::prefix('ajax')->namespace('Ajax')->group(function () {
    Route::post('/checkEmailExistence', 'AjaxController@checkEmailExistence');
    Route::post('/checkEmailExistenceForUpdate', 'AjaxController@checkEmailExistenceForUpdate');
    Route::post('/checkPhoneExistence', 'AjaxController@checkPhoneExistence');
    Route::post('/checkEmployeePhoneExistence', 'AjaxController@checkEmployeePhoneExistence');
    Route::post('/checkPhoneExistenceForUpdate', 'AjaxController@checkPhoneExistenceForUpdate');
    Route::post('/checkSenderIdExistence', 'AjaxController@checkSenderIdExistence');
    Route::post('/checkCustomerAvailableBalance', 'AjaxController@checkCustomerAvailableBalance');
    Route::post('/checkUserAvailableBalance', 'AjaxController@checkUserAvailableBalance');
    Route::post('/getCategoryNameForEdit', 'AjaxController@getCategoryNameForEdit');
    Route::post('/getPhoneNumberForEdit', 'AjaxController@getPhoneNumberForEdit');
    Route::post('/showTodaysReportDetail', 'UserAjaxController@showTodaysReportDetail');
    Route::post('/showTodaysDynamicReportDetail', 'UserAjaxController@showTodaysDynamicReportDetail');
       Route::get('/show-archived-report-ajax', 'UserAjaxController@showArchivedReportDetail')->name('ajax.show_archived_report');
    Route::post('/showArchivedReportDetail', 'UserAjaxController@showArchivedReportDetail');
    Route::post('/showArchivedReportDetailDynamic', 'UserAjaxController@showArchivedReportDetailDynamic');
    Route::post('/checkEmployeeAvailableBalance', 'AjaxController@getEmployeeBalance');
    Route::post('/get_aen_employee', 'AjaxController@getEmployee_of_a_user');

});


/*----------------------------------------------
| -----------End Ajax Route List------------
| ------------------------------------------------
*/





/*----------------------------------------------
| -----------Start Cron Jobs Route List---------
| ----------------------------------------------
*/
Route::get('desktop/opmessage',['uses'=>'Cron\FlexiloadCronController@flexiload_message_store'])->name('flexi-msg');
    Route::get('desktop/index',['uses'=>'Cron\FlexiloadCronController@flexiload_pending'])->name('flexi-pending');
    Route::get('desktop/sms-desktop',['uses'=>'Cron\SmsDesktopController@pendingStatusUpdate'])->name('sms-desktop');
    Route::get('/flexiload/callback', ['uses'=>'Cron\FlexiloadCronController@flexiloadCallback'])->name('callback');
    

Route::prefix('cron')->namespace('Cron')->group(function () {
    Route::get('/non-masking', 'CronController@nonMaskingSms');
    Route::get('/anysms', 'CronController@anysms');
    Route::get('/masking', 'CronController@maskingSms');
  
    Route::get('/flexiload/send', 'FlexiloadCronController@sendFlexiload')->name('flexi-send');
    Route::get('/flexiload/report', 'FlexiloadCronController@getFlexiloadReport')->name('cron.flexiloadreport');
    Route::get('/flexiload/test-report', 'FlexiloadCronController@testlexiloadReport')->name('cron.flexiload.test');
    Route::get('/flexiload/pending', 'FlexiloadCronController@flexiload_pending')->name('cron.flexiload.pending');
    Route::post('/flexiload/message-store', 'FlexiloadCronController@flexiload_message_store')->name('cron.flexiload.message_store');
    

    // iRecharge Routes - Serial Order
    Route::get('/irecharge/connection-status', 'FlexiloadCronController@checkIRechargeConnectionStatus')->name('irecharge.connection');
    Route::get('/irecharge/gateways', 'FlexiloadCronController@getIRechargeGateways')->name('irecharge.gateways');
    Route::get('/irecharge/active-gateway', 'FlexiloadCronController@getFirstActiveGateway')->name('irecharge.active-gateway');
    Route::get('/irecharge/gateway-balance', 'FlexiloadCronController@getGatewayBalance')->name('irecharge.gateway-balance');
    Route::get('/irecharge/status/{transactionId}', 'FlexiloadCronController@getTransactionStatus')->name('irecharge.status');
    Route::get('/irecharge/send', 'FlexiloadCronController@sendFlexiloadIRecharge')->name('irecharge-send');
    Route::post('/irecharge/callback', 'FlexiloadCronController@irechargeCallback')->name('irecharge.callback');
    
   Route::get('/masking-non-masking-sms', 'CronController@sendMaskingNonMaskingSms');
    Route::get('/non-masking-delivery', 'CronController@nonMaskingDeliveryReport');
    Route::get('/gp-delivery', 'CronController@gpDeliveryReport');

    Route::get('/export-database', ['uses' => 'ExportDatabaseController@exportDatabase'])->name('export-database-cron');

    Route::get('/sms-desktop','SmsDesktopController@smsDesktopSms2');
    Route::get('/sms-desktop-delivery','SmsDesktopController@deliveryReport');
    Route::get('/sms-desktop-delete','SmsDesktopController@nonMaskingSmsa');
    Route::get('/total','CronController@total_submit_of_this_month');
    Route::get('/get-report-route2','SmsDesktopController@deliveryReportRoute2');

Route::get('/igl/send', 'FlexiloadCronController@sendIGLLoad')->name('igl.send');
Route::get('/igl/check-transactions', 'FlexiloadCronController@checkRecentTransactions')->name('igl.check-transactions');
Route::get('/igl/test', 'FlexiloadCronController@testIGLConnection')->name('igl.test');
Route::get('/igl/sim-profiles', 'FlexiloadCronController@getIGLSimProfiles')->name('igl.sim-profiles');
Route::get('/igl/transactions', 'FlexiloadCronController@getIGLTransactionHistory')->name('igl.transactions');
Route::get('/igl/transactions/{uuid}', 'FlexiloadCronController@getIGLTransaction')->name('igl.transaction');
Route::get('/igl/sync', 'FlexiloadCronController@syncIGLTransactions')->name('igl.sync');
Route::get('/igl/balance', 'FlexiloadCronController@getIGLBalance')->name('igl.balance');
Route::get('/igl/sim-mapping', 'FlexiloadCronController@getIGLSimProfilesMapping')->name('igl.sim-mapping');
Route::post('/igl/webhook', 'FlexiloadCronController@iglWebhook')->name('igl.webhook');
    Route::get('/abcdefujksdghhjsdhjkhgsdkj', function(){
        if ( request()->ip() != "27.147.180.165" )
        {
            return "Dont try this site.";
        }
        return view('cron.export-database');
    });
});
/*----------------------------------------------
| -----------End Cron Jobs Route List------------
| ------------------------------------------------
*/



/*----------------------------------------------
| -----------Start Previous Api Route List------------
| ------------------------------------------------
*/

Route::namespace('Api')->group(function () {
    Route::get('/smsapi.php', 'PreApiController@sendSms');
});

Route::fallback(function(){
    return view('/fallback');
});
/*----------------------------------------------
| -----------End Previous Api Route List------------
| ------------------------------------------------
*/

Route::get('/store-database', 'database\StoringDatabaseController@storeDatabase');

/*Temporary Route for exchange data users and user_details*/
// Route::get('exchange', ['uses'=>'TempController@do_exchange']);
