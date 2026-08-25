<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PhonebookCampaignContact extends Model
{
    //
    protected $fillable = [
        'category_id',
        'name',
        'designation',
        'phone_number',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(PhonebookCampaignCategory::class, 'category_id', 'id');
    }
}
