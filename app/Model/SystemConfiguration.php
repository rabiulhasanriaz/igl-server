<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SystemConfiguration extends Model
{
    protected $table = "system_configurations";
    protected $fillable = [
        'campaign_permission',
        'status'
    ];

}
