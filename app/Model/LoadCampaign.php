<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadCampaign extends Model
{
    protected $fillable = [
        'user_id',
        'operator_id',
        'sms_id',
        'campaign_id',
        'targeted_number',
        'owner_name',
        'package_id',
        'number_type',
        'campaign_type',
        'campaign_price',
        'remarks',
        'status'
    ];

    public $timestamps = false;
}
