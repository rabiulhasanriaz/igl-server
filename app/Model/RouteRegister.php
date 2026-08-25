<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RouteRegister extends Model
{
    protected $fillable = 
    [
        'route_name',
        'user_name',
        'password',
        'status'
    ];
    protected $primaryKey = 'id';
    public $timestamps = true;
}
