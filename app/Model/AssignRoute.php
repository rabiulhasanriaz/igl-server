<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AssignRoute extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'route',
        'api_password'
    ];
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function userDetail()
    {
        return $this->belongsTo('App\Model\User','user_id','id');
    }

    public function routeDetail()
    {
        return $this->belongsTo('App\Model\RouteRegister','route','id');
    }
}
