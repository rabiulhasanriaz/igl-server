<?php

namespace App\Http\Controllers\reseller;

use App\Model\AccSmsRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\User;

class SmsRateController extends Controller
{
    //
public function edit($id)
{
    $user = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
    if($user) {
        $smsRates = AccSmsRate::with('country', 'operator')->where('user_id', $id)->get();
        if ($smsRates) {
            return view('reseller.users.user_price_view', compact('smsRates', 'user'));
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'unknown user. please try again.....');
            return redirect()->route('reseller.user.index');
        }
    } else {
        session()->flash('type', 'danger');
        session()->flash('message', 'unknown user. please try again.....');
        return redirect()->route('reseller.user.index');
    }
}

public function update(Request $request, $id)
{
    try {
        $smsRate = AccSmsRate::where('id', $id)->first();
        if($smsRate) {
            $checkUser = User::where(['create_by' => Auth::id(), 'id' => $smsRate->user_id])->first();
            if($checkUser) {
                $smsRate->asr_masking = $request->masking_price;
                $smsRate->asr_nonmasking = $request->non_masking_price;
                $smsRate->asr_nonmasking_iptsp = $request->non_masking_iptsp_price;
                $smsRate->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Successfully updated price');
                return redirect()->back();
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'Unknown user. Please try again.');
                return redirect()->route('reseller.user.index');
            }
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'Unknown SMS rate. Please try again.');
            return redirect()->route('reseller.user.index');
        }
    } catch (\Exception $e) {
        session()->flash('type', 'danger');
        session()->flash('message', 'Something went wrong while updating price. Please try again.');
        return redirect()->back();
    }
}

public function bulkUpdate(Request $request)
{
    try {
        // Get the authenticated reseller
        $reseller = Auth::user();
        
        // Find the user that belongs to this reseller
        $user = User::where('create_by', $reseller->id)
                  ->where('id', $request->user_id)
                  ->first();
        
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found or you are not authorized to update prices for this user'
            ], 403);
        }

        if(empty($request->rates)) {
            return response()->json([
                'success' => false,
                'message' => 'No rates provided for update.'
            ], 400);
        }

        $updatedCount = 0;
        foreach($request->rates as $rate) {
            $smsRate = AccSmsRate::where('id', $rate['id'])
                        ->where('user_id', $user->id)
                        ->first();
            
            if($smsRate) {
                $smsRate->update([
                    'asr_masking' => $rate['masking_price'],
                    'asr_nonmasking' => $rate['non_masking_price'],
                    'asr_nonmasking_iptsp' => $rate['non_masking_iptsp_price']
                ]);
                $updatedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updatedCount} rates for {$user->company_name}",
            'updated_count' => $updatedCount
        ]);

    } catch (\Exception $e) {
        \Log::error("Bulk price update error: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong while updating prices. Please try again.'
        ], 500);
    }
}
}

