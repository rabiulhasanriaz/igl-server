<?php

namespace App\Http\Controllers\database;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoringDatabaseController extends Controller
{
    public function storeDatabase()
    {
		$server_filelist = glob('assets/db_sms/*');
		$file_names = [];
		foreach ($server_filelist as $file) {
			$exp = explode('/', $file);
			$file_names[] = end($exp);
		}
		return response()->json(['filelists' => $file_names]);
    }
}
