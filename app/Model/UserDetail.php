<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    //
    protected $fillable = [
        'user_id',
        'domain_name',
        'limit',
        'name',
        'designation',
        'address',
        'nid',
        'dob',
        'logo',
        'exp_date',
        'user_p',
        'last_log_ip',
        'last_log_os',
        'api_key',
        'flexi_api_key',
        'facebookid',
        'hotline',
        'logout_url',
        'white_listed_ip',
        'otp_check'
    ];
}
