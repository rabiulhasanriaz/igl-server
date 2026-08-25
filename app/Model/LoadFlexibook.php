<?php

namespace App\Model;
use \App\Model\LoadFlexibooksData;
use Illuminate\Database\Eloquent\Model;

class LoadFlexibook extends Model
{
    protected $fillable = [
    	'id',
    	'user_id',
    	'name',
    	'status',
    	'created_at',
    	'updated_at',
    ];

    public static function book_price($id)
    {
    	return LoadFlexibooksData::where('load_flexibooks_id',$id)
                                    ->where('status', 1)
                                    ->sum('amount');
    }
}
