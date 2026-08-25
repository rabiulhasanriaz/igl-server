<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SenderIdUserDefault extends Model
{
    //
    protected $fillable = [
        'user_id',
        'sender_id',
    ];

    public function sender()
    {
        return $this->belongsTo(SenderIdRegister::class, 'sender_id', 'id');
    }
}
