<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
class ApiLog extends Model
{
    protected $table = 'api_logs';

    protected $fillable = [
        'user_id',
        'api_name',
        'ip_address',
        'contacts_count',
        'status',
        'response_code',
        'error_message',
        'processing_time_ms',
    ];
    public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
}