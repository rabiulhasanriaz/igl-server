<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsCampaign extends Model
{
    //
    protected $fillable = [
        'user_id',
        'sender_id',
        'campaign_id',
        'sc_cell_no',
        'sc_message',
        'sc_sms_cost',
        'operator_id',
        'sc_campaign_type',
        'sc_deal_type',
        'sc_sms_type',
        'sc_sms_id',
        'sc_sms_text_type',
        'sc_submitted_time',
        'sc_targeted_time',
        'sc_delivery_report',
        'sc_status',
    ];


    protected $dates = [
        'sc_targeted_time',
    ];

    public function sender()
    {
        return $this->belongsTo(SenderIdRegister::class, 'sender_id', 'id');
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class, 'operator_id', 'id');
    }
}
