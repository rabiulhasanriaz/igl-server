<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SenderIdVirtualNumber extends Model
{
    //
    protected $fillable = [
        'id',
        'operator_id',
        'sivn_number',
        'sivn_name',
        'sivn_status',
        'sivn_api_user_name',
        'sivn_api_password',
        'sivn_load_amount',
        'last_load_time',
    ];

    public function operator(){
        return $this->belongsTo(Operator::class);
    }

    public function Robi(){
        return $this->hasMany(SenderIdRegister::class, 'sir_robi_vn', 'id');
    }

    public function Airtel(){
        return $this->hasMany(SenderIdRegister::class, 'sir_airtel_vn', 'id');
    }

    public function Banglalink(){
        return $this->hasMany(SenderIdRegister::class, 'sir_banglalink_vn', 'id');
    }

    public function Teletalk(){
        return $this->hasMany(SenderIdRegister::class, 'sir_teletalk_vn', 'id');
    }

    public function GP(){
        return $this->hasMany(SenderIdRegister::class, 'sir_gp_vn', 'id');
    }



}
