<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PhonebookContact extends Model
{
    //
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'designation',
        'phone_number',
        'status',
    ];


    public function Category()
    {
        return $this->belongsTo(PhonebookCategory::class, 'category_id', 'id');
    }
}
