<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SenderIdNonMasking extends Model
{
    protected $fillable = [
        'number',
        'operator_id'
    ];

    public function operator()
    {
        return $this->belongsTo(Operator::class, 'operator_id');
    }
}
