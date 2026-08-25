<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadSimAvailablleBalance extends Model
{
    protected $fillable = [
        'airtel',
        'blink',
        'gp',
        'robi',
        'teletalk',
        'status'
    ];
}
