<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadFlexibooksData extends Model
{
    protected $fillable = [
    	'id',
        'load_flexibooks_id',
        'name',
		'number',
		'operator',
    	'number_type',
        'amount',
    	'remarks',
    	'status',
    	'created_at',
    	'updated_at',
    ];
}
