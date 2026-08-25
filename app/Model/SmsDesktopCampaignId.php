<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsDesktopCampaignId extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'sdci_campaign_id',
        'sdci_campaign_title',
        'sdci_total_submitted',
        'sdci_total_cost',
        'sdci_campaign_type',
        'sdci_deal_type',
        'sdci_sms_type',
        'sdci_dynamic_type',
        'sdci_sender_operator',
        'sdci_targeted_time',
        'sdci_campaign_status',
        'sdci_browser',
        'sdci_mac_address',
        'sdci_ip_address',
        'sdci_from_api',

    ];

    protected $dates = ['sdci_targeted_time'];

    public function campaignData()
    {
        return $this->hasMany(SmsDesktop::class, 'campaign_id', 'id');
    }

    public function pendingSmsData()
    {
        return $this->hasMany(SmsDesktopPending::class, 'campaign_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(SenderIdRegister::class, 'sender_id', 'id');
    }
}
