<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class SmsCampaign_24h extends Model
{
    //
    protected $fillable = [
        'user_id',
        'sender_id',
        'campaign_id',
        'sct_cell_no',
        'sct_message',
        'sct_sms_cost',
        'operator_id',
        'sct_campaign_type',
        'sct_deal_type',
        'sct_sms_type',
        'sct_sms_id',
        'sct_sms_text_type',
        'sct_target_time',
        'sct_delivery_report',
        'sct_status',
        'created_at',
    ];

    protected $dates = [
        'sct_target_time',
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
}
