<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserMobile implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        $number = trim($value);
        if ( !is_numeric($number) ){
            return false;
        }elseif ( !(substr($number,0,2) == "01" && strlen($number) == 11) ){
            return false;
        }
        elseif( (substr($number,0,3) != "015") && (substr($number,0,3) != "014") && (substr($number,0,3) != "013") && (substr($number,0,3) != "016") && (substr($number,0,3) != "017") && (substr($number,0,3) != "018") && (substr($number,0,3) != "019")){
            return false;
        }
        else{
            return $value == $value;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be an bangladesh cell-phone number(example: 01800000000)';
    }
}
