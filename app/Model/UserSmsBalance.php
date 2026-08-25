<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserSmsBalance extends Model
{
    protected $table = 'user_sms_balances';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:4',
    ];
}
