<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ErrorNotification extends Model
{
    protected $fillable = [
        'user_id',
        'sender_id',
        'campaign_id',
        'error_message',
        'created_at',
        'updated_at'
    ];
}
