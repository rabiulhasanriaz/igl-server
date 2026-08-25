<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsDesktop24h extends Model
{
    protected $fillable = [
        'user_id',
        'sender_id',
        'campaign_id',
        'sdt_cell_no',
        'sdt_message',
        'sdt_sms_cost',
        'operator_id',
        'sdt_campaign_type',
        'sdt_deal_type',
        'sdt_sms_type',
        'sdt_sms_id',
        'sdt_sms_text_type',
        'sdt_target_time',
        'sdt_delivery_report',
        'sdt_status',
        'created_at',
    ];

    protected $dates = [
        'sdt_target_time',
    ];

    public function sender()
    {
        return $this->belongsTo(SenderIdRegister::class, 'sender_id', 'id');
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class, 'operator_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function report_user_name(){
        return $this->belongsTo('App\Model\AssignRoute','user_id','user_id');
    }
}
