<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class EmployeeUser extends Authenticatable
{
    protected $fillable = [
        'create_by',
        'name',
        'email',
        'phone',
        'commission',
        'password',
        'employee_p',
        'avatar',
        'status',
    ];
}
