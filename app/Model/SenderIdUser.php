<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SenderIdUser extends Model
{
    //
    protected $fillable = [
        'user_id',
        'sender_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(SenderIdRegister::class, 'sender_id', 'id');
    }
}
