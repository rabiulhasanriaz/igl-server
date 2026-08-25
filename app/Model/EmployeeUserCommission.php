<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmployeeUserCommission extends Model
{
    protected $fillable = [
    	'eu_id',
    	'eu_ref_id',
    	'euc_credit',
    	'euc_debit',
    	'euc_status',
    ];
}
