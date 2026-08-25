<?php

return [
    'projectPath' => '/',

    // LIVE API DOMAIN
    'apiDomain' => "https://securepay.sslcommerz.com",

    // DIRECT STORE CREDENTIALS (no env)
    'apiCredentials' => [
        'store_id' => "felnatech0live",
        'store_password' => "6815E61C133C384115",
    ],

    // SSLCommerz API URLs
    'apiUrl' => [
        'make_payment' => "/gwprocess/v4/api.php",
        'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
        'order_validate' => "/validator/api/validationserverAPI.php",
        'refund_payment' => "/validator/api/merchantTransIDvalidationAPI.php",
        'refund_status' => "/validator/api/merchantTransIDvalidationAPI.php",
        'payment_detail' => "/validator/api/merchantTransIDvalidationAPI.php",
    ],

    // Localhost handling (set false for live)
    'connect_from_localhost' => false,

    // Direct URLs (no env)
    'success_url' => '/user/topup-balance/success',
    'failed_url' => '/user/topup-balance/fail',
    'cancel_url' => '/user/topup-balance/cancel',
    'ipn_url' => '/user/topup-balance/ipn',
];

