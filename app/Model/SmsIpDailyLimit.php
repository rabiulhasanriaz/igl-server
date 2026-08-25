<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsIpDailyLimit extends Model
{
    protected $table = 'sms_ip_daily_limits';

    protected $fillable = [
        'ip_address',
        'user_id',
        'sms_count',
        'limit_date'
    ];
}