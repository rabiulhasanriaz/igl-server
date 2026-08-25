<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadFailed extends Model
{
    protected $table = "load_campaign_faileds";

    protected $fillable = [
    	'user_id',
        'operator_id',
        'sms_id',
        'campaign_id',
        'targeted_number',
        'owner_name',
        'package_id',
        'number_type',
        'campaign_type',
        'campaign_price',
        'api_port',
        'transaction_id',
        'remarks',
        'status'
    ];
    public function package_info()
    {
    	return $this->belongsTo("App\Model\LoadPackage", 'package_id', 'id');
    }

    public function trx_user(){
        return $this->belongsTo('App\Model\User','user_id','id');
    }
}
