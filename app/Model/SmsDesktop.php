<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsDesktop extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'sd_cell_no',
        'sd_message',
        'sd_sms_cost',
        'operator_id',
        'sd_campaign_type',
        'sd_deal_type',
        'sd_sms_type',
        'sd_sms_id',
        'sd_sms_text_type',
        'sd_submitted_time',
        'sd_targeted_time',
        'sd_delivery_report',
        'sd_status',
    ];


    protected $dates = [
        'sd_targeted_time',
    ];

    public function sender()
    {
        return $this->belongsTo(SenderIdRegister::class, 'sender_id', 'id');
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class, 'operator_id', 'id');
    }

    public function report_user_name(){
        return $this->belongsTo('App\Model\AssignRoute','user_id','user_id');
    }
}
