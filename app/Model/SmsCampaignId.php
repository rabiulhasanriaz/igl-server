<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsCampaignId extends Model
{
    protected $fillable = [
        'user_id',
        'sender_id',
        'sci_campaign_id',
        'sci_campaign_title',
        'sci_total_submitted',
        'sci_total_cost',
        'sci_campaign_type',
        'sci_deal_type',
        'sci_sms_type',
        'sci_dynamic_type',
        'sci_sender_operator',
        'sci_targeted_time',
        'sci_campaign_status',
        'sci_browser',
        'sci_mac_address',
        'sci_ip_address',
        'sci_from_api',
    ];

    protected $dates = ['sci_targeted_time'];

    public function pendingSmsData()
    {
        return $this->hasMany(SmsCamPending::class, 'campaign_id', 'id');
    }

    public function sentSmsData()
    {
        return $this->hasMany(SmsCampaign_24h::class, 'campaign_id', 'id');
    }

    public function archivedSmsData()
    {
        return $this->hasMany(SmsCampaign::class, 'campaign_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(SenderIdRegister::class, 'sender_id', 'id');
    }

    public function creditHistory()
    {
        return $this->hasOne(AccUserCreditHistory::class, 'campaign_id', 'id');
    }

    /**
     * Report count comes only from the credit-history row whose campaign_id
     * matches this campaign's internal id. Missing history is displayed as 0.
     */
    public function getReportSmsCountAttribute()
    {
        $creditHistory = $this->relationLoaded('creditHistory')
            ? $this->getRelation('creditHistory')
            : null;

        return $creditHistory && $creditHistory->uch_sms_count !== null
            ? (int) $creditHistory->uch_sms_count
            : 0;
    }

    public function getReportRecipientCountAttribute()
    {
        return (int) ($this->pending_sms_data_count ?? 0)
            + (int) ($this->sent_sms_data_count ?? 0)
            + (int) ($this->archived_sms_data_count ?? 0);
    }
}
