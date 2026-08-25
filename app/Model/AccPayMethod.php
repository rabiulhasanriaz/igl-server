<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AccPayMethod extends Model
{
    //
    protected $fillable = [
        'apm_name',
        'apm_status',
    ];
}
