<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    //
    protected  $fillable = [
        'ope_operator_name',
        'ope_country_code',
        'ope_number',
    ];
    
}
