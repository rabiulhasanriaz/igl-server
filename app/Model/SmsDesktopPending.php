<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsDesktopPending extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'sdp_cell_no',
        'sdp_message',
        'sdp_sms_cost',
        'operator_id',
        'sdp_campaign_type',
        'sdp_deal_type',
        'sdp_sms_type',
        'sdp_sms_id',
        'sdp_tried',
        'sdp_picked',
        'sdp_sms_text_type',
        'sdp_priority',
        'sdp_target_time',
        'sdp_campaign_status',
        'sdp_status',
    ];

    public function api_user_name(){
        return $this->belongsTo('App\Model\AssignRoute','user_id','user_id');
    }

     public function campaignId(){
        return $this->belongsTo('App\Model\SmsDesktopCampaignId','campaign_id','id');
    }

}
