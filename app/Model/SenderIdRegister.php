<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SenderIdRegister extends Model
{
    //
    protected $fillable = [
        'sir_sender_id',
        'sir_reg_date',
        'sir_robi_vn',
        'sir_robi_confirmation',
        'sir_airtel_vn',
        'sir_airtel_confirmation',
        'sir_banglalink_vn',
        'sir_banglalink_confirmation',
        'sir_teletalk_vn',
        'sir_teletalk_confirmation',
        'sir_teletalk_user_name',
        'sir_teletalk_user_password',
        'sir_gp_vn',
        'sir_gp_confirmation',
        'sir_confirmation_date',
        'sir_status',
        'sir_active',
    ];

    public function robi_virtual_number(){
        return $this->belongsTo(SenderIdVirtualNumber::class, 'sir_robi_vn', 'id');
    }

    public function airtel_virtual_number(){
        return $this->belongsTo(SenderIdVirtualNumber::class, 'sir_airtel_vn', 'id');
    }

    public function banglalink_virtual_number(){
        return $this->belongsTo(SenderIdVirtualNumber::class, 'sir_banglalink_vn', 'id');
    }

    public function teletalk_virtual_number(){
        return $this->belongsTo(SenderIdVirtualNumber::class, 'sir_teletalk_vn', 'id');
    }

    public function gp_virtual_number(){
        return $this->belongsTo(SenderIdVirtualNumber::class, 'sir_gp_vn', 'id');
    }
}
