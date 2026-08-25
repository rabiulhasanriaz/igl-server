<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadCampaignId extends Model
{
    protected $fillable = [
    	'campaign_id', 'campaign_name', 'status'
    ];
    protected $dates = 
    [
    	'created_at'
    ];


    public function package(){
        return $this->belongsTo('App\Model\LoadCampaign30day','campaign_id','campaign_id');
    }
}
