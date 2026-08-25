<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    //
    protected $fillable = [
        'user_id',
        'st_name',
        'st_content',
        'st_total_sms',
        'st_content_type',
    ];
}
