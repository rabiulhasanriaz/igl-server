<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TempController extends Controller
{
    public function do_exchange() {

    	$user_ids = \App\Model\User::all();
    	
    	foreach ($user_ids as $user_id ) {
    		$user_name =  $user_id->name;
    		$company_name = \App\Model\UserDetail::find($user_id->id)->company_name;

    		/*Update User*/
    		$user_column = \App\Model\User::find( $user_id->id );
    		$user_column->name = $company_name;
    		$user_column->save();
    		/*-------*/

    		/*Update user_details*/
    		$user_detail_column = \App\Model\UserDetail::find( $user_id->id );
    		$user_detail_column->company_name = $user_name;
    		$user_detail_column->save();

    	}
    	echo "Seccessfull";

    }
}

