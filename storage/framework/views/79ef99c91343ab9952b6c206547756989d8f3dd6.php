

<?php $__env->startSection('developer_api_progress_menu_class','active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
        </li>
        <li class="active">API Documentation</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        <?php echo e(Auth::user()->company_name); ?>

        <i class="ace-icon fa fa-angle-double-right"></i>
        Developer API
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Documentation
        </small>
    </h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="space-6"></div>
        
        <div class="widget-box">
            <div class="widget-header widget-header-blue widget-header-flat">
                <h4 class="widget-title lighter">SMS API Documentation</h4>
            </div>
  <div class="alert alert-info">
                        <strong>Your Current API Key:</strong> <?php echo e(Auth::user()->userDetail->api_key); ?>

                        <br>
                        <strong>Sample CURL Request:</strong>
                        <pre>curl -X GET "http://<?php echo e($domain_url); ?>/api/v1/balance?api_key=<?php echo e(Auth::user()->userDetail->api_key); ?>"</pre>
                    </div>
            <div class="widget-body">
                <div class="widget-main">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Base URL</h4>
                            <p>All requests should be made to the following endpoint:</p>
                            <pre>http://<?php echo e($domain_url); ?>/api/v1/send</pre>
                            
                            <h4>Request Methods</h4>
                            <p>The API supports both GET and POST methods.</p>
                            
                            <h4>Authentication</h4>
                            <p>The API requires an <code>api_key</code> to authenticate requests. Obtain the <code>api_key</code> from your account on the SMS platform.</p>
                            
                            <h4>Request Parameters</h4>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>api_key</td>
                                        <td>String</td>
                                        <td>Your unique API key for authentication.</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td>contacts</td>
                                        <td>String</td>
                                        <td>The recipient's phone number(s). Separate multiple numbers with commas.</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td>senderid</td>
                                        <td>String</td>
                                        <td>An approved sender ID for the SMS.</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td>msg</td>
                                        <td>String</td>
                                        <td>The content of the SMS message.</td>
                                        <td>Yes</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <h4>GET Request Example</h4>
                            <pre>GET http://<?php echo e($domain_url); ?>/api/v1/send?api_key=(API KEY)&contacts=(NUMBER)&senderid=(Approved Sender ID)&msg=(Message Content)</pre>
                            
                            <h4>POST Request Example</h4>
                            <pre>POST http://<?php echo e($domain_url); ?>/api/v1/send
Content-Type: application/x-www-form-urlencoded

api_key=your_api_key&contacts=8801958666961,88019XXXXXXXX&senderid=FelnaDMA&msg=Hello%20from%20FelnaDMA</pre>
                            
                            <h4>Response Example (Success)</h4>
                            <pre>{
    "code": "445000",
    "message": "Message has been sent...",
    "campaign_id": "123456789012345"
}</pre>
                            
                            <h4>Response Codes</h4>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>445000</td>
                                        <td>Message sent successfully.</td>
                                    </tr>
                                    <tr>
                                        <td>445010</td>
                                        <td>Missing api_key.</td>
                                    </tr>
                                    <tr>
                                        <td>445020</td>
                                        <td>Missing contact number(s).</td>
                                    </tr>
                                    <tr>
                                        <td>445030</td>
                                        <td>Missing senderid.</td>
                                    </tr>
                                    <tr>
                                        <td>445040</td>
                                        <td>Invalid api_key.</td>
                                    </tr>
                                    <tr>
                                        <td>445050</td>
                                        <td>Your account was suspended.</td>
                                    </tr>
                                    <tr>
                                        <td>445060</td>
                                        <td>Your account has expired.</td>
                                    </tr>
                                    <tr>
                                        <td>445070</td>
                                        <td>Only a user can send sms.</td>
                                    </tr>
                                    <tr>
                                        <td>445080</td>
                                        <td>Invalid sender id.</td>
                                    </tr>
                                    <tr>
                                        <td>445090</td>
                                        <td>You have no access to this sender id.</td>
                                    </tr>
                                    <tr>
                                        <td>445110</td>
                                        <td>All numbers are invalid.</td>
                                    </tr>
                                    <tr>
                                        <td>445120</td>
                                        <td>Insufficient balance.</td>
                                    </tr>
                                    <tr>
                                        <td>445130</td>
                                        <td>Reseller insufficient balance.</td>
                                    </tr>
                                    <tr>
                                        <td>445170</td>
                                        <td>You are not a user.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="space-6"></div>
        
        <div class="widget-box">
            <div class="widget-header widget-header-blue widget-header-flat">
                <h4 class="widget-title lighter">Flexi API Documentation</h4>
            </div>

            <div class="widget-body">
                <div class="widget-main">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Authentication</h4>
                            <p>The API requires an <code>api_key</code> to authenticate requests. Obtain the <code>api_key</code> from your account on the SMS platform.</p>
                            
                            <h4>Request Parameters</h4>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>api_key</td>
                                        <td>String</td>
                                        <td>Your unique API key for authentication.</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td>pin</td>
                                        <td>Integer</td>
                                        <td>Your secret PIN.</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td>number</td>
                                        <td>String</td>
                                        <td>The mobile number to which the load will be sent. Format: 88017XXXXXXXX.</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td>amount</td>
                                        <td>Integer</td>
                                        <td>Amount to send. Minimum: 10, Maximum: 50000.</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td>number_type</td>
                                        <td>Integer</td>
                                        <td>Type of number. 1 = Prepaid, 2 = Postpaid.</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td>operator</td>
                                        <td>String</td>
                                        <td>Operator for the number. Valid values: gp = Grameenphone, gpst = Skitto, blink = Banglalink, robi = Robi, airtel = Airtel.</td>
                                        <td>Yes</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <h4>GET Request Example</h4>
                            <pre>GET http://<?php echo e($domain_url); ?>/api/v1/send-load?api_key=(API KEY)&pin=(Your Secret Pin)&number=(NUMBER)&amount=(Amount)&number_type=(Prepaid/Postpaid)&operator=(Number Operator)</pre>
                            
                            <h4>POST Request Example</h4>
                            <pre>POST http://<?php echo e($domain_url); ?>/api/v1/send-load 
                                Content-Type: application/x-www-form-urlencoded 

                                api_key=44501712757951151712757951&pin=1234&number=8801712345678&amount=100&number_type=1&operator=gp</pre>
                                                            
                                                            <h4>Response Example</h4>
                                                            <pre>{
                                    "api_key": "44501712757951151712757951",
                                    "pin": 1234,
                                    "number": "01958666961",
                                    "amount": 10,
                                    "number_type": 1,
                                    "operator": "blink"
                                }</pre>
                            
                            <h4>Response Codes</h4>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>445900</td>
                                        <td>Load Request Received Successfully.</td>
                                    </tr>
                                    <tr>
                                        <td>445901</td>
                                        <td>Missing api key.</td>
                                    </tr>
                                    <tr>
                                        <td>445902</td>
                                        <td>Missing phone number.</td>
                                    </tr>
                                    <tr>
                                        <td>445903</td>
                                        <td>Missing number type.</td>
                                    </tr>
                                    <tr>
                                        <td>445904</td>
                                        <td>Missing operator.</td>
                                    </tr>
                                    <tr>
                                        <td>445905</td>
                                        <td>Missing flexipin.</td>
                                    </tr>
                                    <tr>
                                        <td>445906</td>
                                        <td>Missing Amount.</td>
                                    </tr>
                                    <tr>
                                        <td>445907</td>
                                        <td>Amount should be in 10 to 50000.</td>
                                    </tr>
                                    <tr>
                                        <td>445908</td>
                                        <td>Invalid amount.</td>
                                    </tr>
                                    <tr>
                                        <td>445909</td>
                                        <td>Invalid operator.</td>
                                    </tr>
                                    <tr>
                                        <td>445910</td>
                                        <td>Invalid number.</td>
                                    </tr>
                                    <tr>
                                        <td>445911</td>
                                        <td>Invalid api key.</td>
                                    </tr>
                                    <tr>
                                        <td>445912</td>
                                        <td>Invalid flexipin.</td>
                                    </tr>
                                    <tr>
                                        <td>445913</td>
                                        <td>Insufficient balance.</td>
                                    </tr>
                                    <tr>
                                        <td>445914</td>
                                        <td>Insufficient reseller balance.</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <h4>Notes</h4>
                            <ul>
                                <li>Ensure that all parameters are correctly encoded when sending the request.</li>
                                <li>Contacts should follow the international phone number format, prefixed with the country code (e.g., 880 for Bangladesh).</li>
                                <li>The senderid must be pre-approved by FelnaDMA before use.</li>
                         <li class="menu-help">
    <a>
        <i class="menu-icon fa fa-support"></i>
        <span class="menu-text"> Support </span>
        <small class="menu-help-text">
            Contact: <a href="mailto:support@felnadma.com">support@felnadma.com</a><br>
            Call: <a href="tel:+8801958666999">+880-1958-666999</a>
        </small>
    </a>
</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-6"></div>

        <div class="widget-box">
            <div class="widget-header widget-header-blue widget-header-flat">
                <h4 class="widget-title lighter">SMS Campaign Report API Documentation</h4>
            </div>

            <div class="widget-body">
                <div class="widget-main">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Endpoint</h4>
                            <p>Use this API to check whether SMS messages from an API campaign are sent, pending, successful, or failed.</p>
                            <pre>http://<?php echo e($domain_url); ?>/api/v1/sms-campaign-report</pre>

                            <h4>Request Methods</h4>
                            <p>The API supports both GET and POST methods.</p>

                            <h4>Authentication</h4>
                            <p>The API requires an <code>api_key</code> to authenticate requests. The report is only returned for campaigns created under the authenticated account.</p>

                            <h4>Request Parameters</h4>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>api_key</td>
                                        <td>String</td>
                                        <td>Your unique API key for authentication.</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td>campaign_id</td>
                                        <td>String</td>
                                        <td>The campaign ID returned from the SMS Send API success response.</td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td>contacts</td>
                                        <td>String</td>
                                        <td>Optional comma-separated mobile numbers to filter the report for specific recipients.</td>
                                        <td>No</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h4>GET Request Example</h4>
                            <pre>GET http://<?php echo e($domain_url); ?>/api/v1/sms-campaign-report?api_key=<?php echo e(Auth::user()->userDetail->api_key); ?>&campaign_id=123456789012345</pre>

                            <h4>GET Request Example With Contact Filter</h4>
                            <pre>GET http://<?php echo e($domain_url); ?>/api/v1/sms-campaign-report?api_key=<?php echo e(Auth::user()->userDetail->api_key); ?>&campaign_id=123456789012345&contacts=88017XXXXXXXX,88019XXXXXXXX</pre>

                            <h4>POST Request Example</h4>
                            <pre>POST http://<?php echo e($domain_url); ?>/api/v1/sms-campaign-report
Content-Type: application/x-www-form-urlencoded

api_key=<?php echo e(Auth::user()->userDetail->api_key); ?>&campaign_id=123456789012345</pre>

                            <h4>Response Example (Success)</h4>
                            <pre>{
    "code": "445000",
    "campaign_id": "123456789012345",
    "summary": {
        "submitted": 2,
        "uch_sms_count": 2,
        "sent": 1,
        "success": 1,
        "failed": 0,
        "pending": 1
    },
    "reports": [
        {
            "number": "88017XXXXXXXX",
            "message": "This is a test message",
       
            "send_status": "sent",
            "success": true,
            "gateway_status": "SUCCESS",
            "delivery_report": "DELIVERED",
            "created_at": "2026-06-16 10:30:00",
            "updated_at": "2026-06-16 10:30:10"
        },
        {
            "number": "88019XXXXXXXX",
            "message": "This is a test message",
         
            "send_status": "pending",
            "success": false,
            "gateway_status": "1",
            "delivery_report": "PENDING",
            "created_at": "2026-06-16 10:30:00",
            "updated_at": "2026-06-16 10:30:00"
        }
    ]
}</pre>

                            <h4>Response Fields</h4>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>summary.submitted</td>
                                        <td>Total SMS submitted in the campaign.</td>
                                    </tr>
                                    <tr>
                                        <td>summary.uch_sms_count</td>
                                        <td>SMS quantity recorded in the user credit history for this campaign.</td>
                                    </tr>
                                    <tr>
                                        <td>summary.sent</td>
                                        <td>Total SMS already processed from the pending queue.</td>
                                    </tr>
                                    <tr>
                                        <td>summary.success</td>
                                        <td>Total SMS identified as successful or delivered.</td>
                                    </tr>
                                    <tr>
                                        <td>summary.failed</td>
                                        <td>Total processed SMS that are not marked successful.</td>
                                    </tr>
                                    <tr>
                                        <td>summary.pending</td>
                                        <td>Total SMS still waiting in the pending queue.</td>
                                    </tr>
                                    <tr>
                                        <td>reports[].send_status</td>
                                        <td><code>sent</code> means the SMS has been processed; <code>pending</code> means it is still waiting to be sent.</td>
                                    </tr>
                                    <tr>
                                        <td>reports[].success</td>
                                        <td>Boolean value showing whether the processed SMS is considered successful.</td>
                                    </tr>
                                    <tr>
                                        <td>reports[].gateway_status</td>
                                        <td>Status returned by the SMS gateway or internal queue status for pending SMS.</td>
                                    </tr>
                                    <tr>
                                        <td>reports[].delivery_report</td>
                                        <td>Delivery report value such as <code>DELIVERED</code>, <code>SUCCESS</code>, or <code>PENDING</code>.</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h4>Response Codes</h4>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>445000</td>
                                        <td>Campaign report retrieved successfully.</td>
                                    </tr>
                                    <tr>
                                        <td>445010</td>
                                        <td>Missing api key.</td>
                                    </tr>
                                    <tr>
                                        <td>445040</td>
                                        <td>Invalid api key.</td>
                                    </tr>
                                    <tr>
                                        <td>445050</td>
                                        <td>Your account was suspended.</td>
                                    </tr>
                                    <tr>
                                        <td>445060</td>
                                        <td>Your account was expired.</td>
                                    </tr>
                                    <tr>
                                        <td>445190</td>
                                        <td>Missing campaign id.</td>
                                    </tr>
                                    <tr>
                                        <td>445200</td>
                                        <td>Campaign not found.</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h4>Notes</h4>
                            <ul>
                                <li>Use the <code>campaign_id</code> returned by the SMS Send API success response.</li>
                                <li>Pending SMS may become sent after the cron process handles the campaign queue.</li>
                                <li>The optional <code>contacts</code> parameter is useful when you need the report for selected recipients only.</li>
                                <li>The response will always return JSON format.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="space-6"></div>
        <div class="widget-box">
    <div class="widget-header widget-header-blue widget-header-flat">
        <h4 class="widget-title lighter">Credit Balance API Documentation</h4>
    </div>

    <div class="widget-body">
        <div class="widget-main">
            <div class="row">
                <div class="col-md-12">
                    <h4>Authentication</h4>
                    <p>The API requires an <code>api_key</code> to authenticate requests. Your API key is: 
                        <strong><?php echo e(Auth::user()->userDetail->api_key); ?></strong></p>
                    
                    <h4>Request Parameters</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>api_key</td>
                                <td>String</td>
                                <td>Your unique API key for authentication</td>
                                <td>Yes</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <h4>GET Request Example</h4>
                    <pre>GET http://<?php echo e($domain_url); ?>/api/v1/balance?api_key=<?php echo e(Auth::user()->userDetail->api_key); ?></pre>
                    
                    <h4>Response Example (Success)</h4>
                    <pre>{
    "code": "445000",
    "message": "Success",
    "balance": "500.50",
    "currency": "BDT"
}</pre>

                    <h4>Response Example (Error)</h4>
                    <pre>{
    "code": "445040",
    "message": "Invalid api_key"
}</pre>
                    
                    <h4>Response Codes</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>445000</td>
                                <td>Balance retrieved successfully</td>
                            </tr>
                            <tr>
                                <td>445010</td>
                                <td>Missing api_key parameter</td>
                            </tr>
                            <tr>
                                <td>445040</td>
                                <td>Invalid api_key</td>
                            </tr>
                            <tr>
                                <td>445050</td>
                                <td>Account suspended</td>
                            </tr>
                            <tr>
                                <td>445060</td>
                                <td>Account expired</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <h4>Notes</h4>
                    <ul>
                        <li>This API only supports GET requests</li>
                        <li>The response will always return JSON format</li>
                        <li>Balance is returned in your account currency (BDT by default)</li>
                        <li>For security reasons, always call this API over HTTP</li>
                        <li>If you need further assistance, contact our support team at support@felnadma.com</li>
                    </ul>
                    
                  
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>