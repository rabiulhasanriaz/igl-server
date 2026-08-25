<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AccUserCreditHistory extends Model
{
    //
    protected $fillable = [
        'campaign_id',
        'user_id',
        'uch_sms_count',
        'uch_sms_cost',
    ];
}
