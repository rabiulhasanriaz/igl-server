<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadApiController extends Model
{
    protected $fillable = [
        'api_port_name',
        'api_one_status',
        'api_two_status'
    ];

    protected $table = 'load_api_controller';
}
