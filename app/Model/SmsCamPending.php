<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsCamPending extends Model
{
    //
    protected $fillable = [
        'user_id',
        'sender_id',
        'campaign_id',
        'scp_cell_no',
        'scp_message',
        'scp_sms_cost',
        'operator_id',
        'scp_campaign_type',
        'scp_deal_type',
        'scp_sms_type',
        'scp_sms_id',
        'scp_tried',
        'scp_picked',
        'scp_sms_text_type',
        'scp_target_time',
        'scp_campaign_status',
        'scp_status',
    ];


    /*create relation with sender id*/
    public function sender()
    {
        return $this->belongsTo(SenderIdRegister::class, 'sender_id', 'id');
    }

}
