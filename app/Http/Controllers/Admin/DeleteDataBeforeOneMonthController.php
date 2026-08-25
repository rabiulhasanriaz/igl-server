<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\SmsCampaign;
use App\Model\SmsDesktop;
use Carbon\Carbon;


class DeleteDataBeforeOneMonthController extends Controller
{
    public function delete_data_before_one_month(Request $request) {
    	SmsCampaign::where('updated_at','<', Carbon::now()->subMonth(1) )->delete();
    	SmsDesktop::where('updated_at','<', Carbon::now()->subMonth(1) )->delete();

    	session()->flash('delete_info', 'The sms info has been updated succesfully');
    	return redirect()->back();
    }
}
