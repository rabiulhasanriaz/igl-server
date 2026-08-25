<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PhonebookCampaignCategory extends Model
{
    //
    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    public function CampaignContacts()
    {
        return $this->hasMany(PhonebookCampaignContact::class, 'category_id', 'id');
    }
}
