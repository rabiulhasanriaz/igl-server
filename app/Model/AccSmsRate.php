<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AccSmsRate extends Model
{
    //
    protected $fillable = [
        'country_id',
        'user_id',
        'operator_id',
        'asr_masking',
        'asr_nonmasking',
         'asr_nonmasking_iptsp',
        'asr_dynamic',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}
