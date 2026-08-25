<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Url;

class ChangeLoginBackgroundController extends Controller
{
    public function changeLoginBackground(Request $request) {
        
        try {
            if ($request->hasFile('bg_image')) {
                $files = $request->file('bg_image');
                $name = 'login_bg.png';
                $destinationPath = 'assets/uploads/';
                $url = $destinationPath . "/" . $name;
                $files->move($destinationPath, $name);
                
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'Empty Image File');
                return redirect()->back();
            }
            
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong !'. $e->getMessage());
            return redirect()->back();
        }
    	
        session()->flash('type', 'success');
        session()->flash('message', 'Updated Success');
        return redirect()->back();
    } 
}
