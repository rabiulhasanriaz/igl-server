<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadApiNewBalance extends Model
{
    protected $fillable = [
        'airtel',
        'blink',
        'gp',
        'robi',
        'teletalk',
        'status'
    ];

    protected $table = 'load_api_new_balance';
}
