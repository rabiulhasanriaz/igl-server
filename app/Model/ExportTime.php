<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ExportTime extends Model
{
    protected $fillable = [
        'id', 'export_hour'
    ];
}
