<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    //
    protected $fillable = [
    	'aa_create_by',
    	'aa_com_domain',
    	'aa_limit',
    	'aa_company_name',
    	'aa_user_name',
    	'aa_email',
    	'aa_cellphone',
    	'aa_password',
    	'aa_designation',
    	'aa_address',
    	'aa_logo',
    	'aa_status',
    	'aa_user_type',
    	'aa_reg_date',
    	'aa_exp_date',
    	'aa_last_log_ip',
    	'aa_last_log_os',
    	'aa_api_key',
    	'aa_facebookid',
    	'aa_senderId',
    	'aa_hotline',
    	'aa_logout_url',
    ];
}
