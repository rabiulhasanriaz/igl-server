<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadCamPending extends Model
{
     protected $fillable = [
        'user_id', 
        'operator_id',
        'sms_id', 
        'campaign_id', 
        'targeted_number', 
        'owner_name', 
        'number_type', 
        'package_id', 
        'campaign_price',
        'transaction_id',
        'gateway_transaction_id',
        'remarks',
        'api_port',
        'status'
    ];

    public function package_info()
    {
        return $this->belongsTo("App\Model\LoadPackage", 'package_id', 'id');
    }

    public function trx_user(){
        return $this->belongsTo('App\Model\User','user_id','id');
    }

    public function number(){
        return $this->belongsTo('App\Model\LoadCampaign30day','campaign_id','campaign_id');
    }

    public function loadCampaign(){
        return $this->belongsTo(LoadCampaignId::class, 'campaign_id','campaign_id');
    }
}