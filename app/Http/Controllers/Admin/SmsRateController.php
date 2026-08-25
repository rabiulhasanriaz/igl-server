<?php

namespace App\Http\Controllers\Admin;

use App\Model\AccSmsRate;
use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SmsRateController extends Controller
{
    //
    /*show price edit form*/
    public function edit($id)
    {
        $smsRates = AccSmsRate::with('country', 'user', 'operator')->where('user_id', $id)->get();
        $user = User::with('userDetail')->where('id', $id)->first();
        return view('admin.reseller.reseller_price_view', compact('smsRates', 'user'));
    }

    /*update user sms rate*/
    public function update(Request $request, $id)
    {

        $validateData = Validator::make($request->all(), [
            'masking_price' => 'required|numeric',
            'non_masking_price' => 'required|numeric',
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData);
        }

        try {
            $updSmsRate = AccSmsRate::where('id', $id)->first();
            if ($updSmsRate) {
                $updSmsRate->asr_masking = $request->masking_price;
                $updSmsRate->asr_nonmasking = $request->non_masking_price;

                $updSmsRate->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Successfully updated user sms rate......!');
                return redirect()->back();
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find sms rate id. please try again......!');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to update user sms rate......!');
            return redirect()->back();
        }
    }
}
