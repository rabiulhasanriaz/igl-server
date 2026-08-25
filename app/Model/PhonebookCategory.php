<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PhonebookCategory extends Model
{
    //
    protected $fillable = [
        'user_id',
        'name',
    ];


    public function Contacts()
    {
        return $this->hasMany(PhonebookContact::class, 'category_id', 'id');
    }
}
