<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadPackage extends Model
{
    protected $fillable = [
    	'operator_id', 'package_name', 'package_category', 'package_price', 'status', 'voice', 'data', 'sms', 'validity'
    ];

    public function operator()
    {
    	return $this->belongsTo('App\Model\Operator');
    }
}
