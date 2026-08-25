<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadApiReg extends Model
{
    protected $fillable = [
        'operator_id',
        'operator_name',
        'operator_subname',
        'operator_ip',
        'op_id',
        'operator_port',
        'operator_user',
        'operator_password',
        'operator_user_port',
        'operator_balance',
        'operator_status',
        'operator_date_register'
    ];
    protected $table='load_api_registration';
}
