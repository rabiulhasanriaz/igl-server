<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminSuper extends Model
{
    //
    protected $fillable = [
    	'as_user_name',
    	'as_email',
    	'as_cellphone',
    	'as_password',
    	'as_designation',
    	'as_address',
    	'as_image',
    	'as_status',
    	'as_user_type',
    	'as_last_login_time',
    	'as_last_log_ip',
    	'as_last_log_os',
    ];

    protected $hidden = [
    	
    ];
}
