<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'topup-balance/success',
        'topup-balance/fail', 
        'topup-balance/cancel',
        'topup-balance/ipn',
        'user/topup-balance/success',
        'user/topup-balance/fail',
        'user/topup-balance/cancel',
        'user/topup-balance/ipn',
           'irecharge/callback',
    'cron/irecharge/callback',
    'cron/igl/webhook',
       'api/webhook/*',  // This should be there
    'webhook/*',
    ];
}

