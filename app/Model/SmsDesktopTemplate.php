<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsDesktopTemplate extends Model
{
    protected $table = 'sms_desktop_templates';
    protected $fillable = 
    [
        'template_title',
        'template_content',
        'user_access',
        'created_at',
        'updated_at'
    ];
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
}
