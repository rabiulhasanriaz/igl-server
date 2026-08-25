<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //
    protected $fillable = [
        'country_name',
        'country_code',
    ];
}
