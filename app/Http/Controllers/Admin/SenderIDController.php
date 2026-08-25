<?php

namespace App\Http\Controllers\Admin;

use App\Model\SenderIdNonMasking;
use App\Model\SenderIdRegister;
use App\Model\SenderIdUserDefault;
use App\Model\SenderIdVirtualNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Validator;
use stdClass;

class SenderIDController extends Controller
{
    //
    /*show all sender id list*/
    public function index()
    {
        $gpVirtualNumbers = SenderIdVirtualNumber::select(['id', 'sivn_number'])->whereHas('operator', function ($query) {
            $query->where('ope_operator_name', '=', 'GP');
        })->get();
        $blVirtualNumbers = SenderIdVirtualNumber::select(['id', 'sivn_number'])->whereHas('operator', function ($query) {
            $query->where('ope_operator_name', '=', 'Banglalink');
        })->get();
        $robiVirtualNumbers = SenderIdVirtualNumber::select(['id', 'sivn_number'])->whereHas('operator', function ($query) {
            $query->where('ope_operator_name', '=', 'Robi');
        })->get();
        $teletalkVirtualNumbers = SenderIdVirtualNumber::select(['id', 'sivn_number'])->whereHas('operator', function ($query) {
            $query->where('ope_operator_name', '=', 'Teletalk');
        })->get();
        $airtelVirtualNumbers = SenderIdVirtualNumber::select(['id', 'sivn_number'])->whereHas('operator', function ($query) {
            $query->where('ope_operator_name', '=', 'Airtel');
        })->get();

        $senderIds = SenderIdRegister::with(['robi_virtual_number', 'airtel_virtual_number', 'banglalink_virtual_number', 'teletalk_virtual_number', 'gp_virtual_number'])->get();
        return view('admin.senderId.sender_id_list', compact('senderIds', 'gpVirtualNumbers', 'blVirtualNumbers', 'robiVirtualNumbers', 'teletalkVirtualNumbers', 'airtelVirtualNumbers'));
    }


    /*show create sender id form*/
    public function create()
    {

        $gpVirtualNumbers = SenderIdVirtualNumber::select(['id', 'sivn_number'])->whereHas('operator', function ($query) {
            $query->where('ope_operator_name', '=', 'GP');
        })->get();
        $blVirtualNumbers = SenderIdVirtualNumber::select(['id', 'sivn_number'])->whereHas('operator', function ($query) {
            $query->where('ope_operator_name', '=', 'Banglalink');
        })->get();
        $robiVirtualNumbers = SenderIdVirtualNumber::select(['id', 'sivn_number'])->whereHas('operator', function ($query) {
            $query->where('ope_operator_name', '=', 'Robi');
        })->get();
        $teletalkVirtualNumbers = SenderIdVirtualNumber::select(['id', 'sivn_number'])->whereHas('operator', function ($query) {
            $query->where('ope_operator_name', '=', 'Teletalk');
        })->get();
        $airtelVirtualNumbers = SenderIdVirtualNumber::select(['id', 'sivn_number'])->whereHas('operator', function ($query) {
            $query->where('ope_operator_name', '=', 'Airtel');
        })->get();
        $nonMaskingSenderIds = SenderIdNonMasking::select('number')->get();

        return view('admin.senderId.add_sender_id', compact('gpVirtualNumbers', 'blVirtualNumbers', 'robiVirtualNumbers', 'teletalkVirtualNumbers', 'airtelVirtualNumbers', 'nonMaskingSenderIds'));
    }


    /*store new sender id*/
    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'sender_id' => ['required', 'min:3', 'unique:sender_id_registers,sir_sender_id'],
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }
        
        $createSenderId = SenderIdRegister::create([
            'sir_sender_id' => $request->sender_id,
            'sir_reg_date' => Carbon::now(),
            'sir_robi_vn' => $request->robi_virtual_number,
            'sir_robi_confirmation' => '2',
            'sir_airtel_vn' => $request->airtel_virtual_number,
            'sir_airtel_confirmation' => '2',
            'sir_banglalink_vn' => $request->bl_virtual_number,
            'sir_banglalink_confirmation' => '2',
            'sir_teletalk_vn' => $request->teletalk_virtual_number,
            'sir_teletalk_confirmation' => '2',
            'sir_teletalk_user_name' => $request->teletalk_user_name,
            'sir_teletalk_user_password' => $request->teletalk_user_password,
            'sir_gp_vn' => $request->gp_virtual_number,
            'sir_gp_confirmation' => '2',
            'sir_confirmation_date' => Carbon::now(),
            'sir_status' => '1',
            'sir_active' => '0',
        ]);
        

        if ($createSenderId) {
            session()->flash('type', 'success');
            session()->flash('message', 'Sender Id Register  Successfuly Completed...');
            return redirect()->back();
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to register sender id...');
            return redirect()->back();
        }
    }


    /*Update Sender id*/
    public function update(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'sender_id' => ['required', 'min:3', Rule::unique('sender_id_registers', 'sir_sender_id')->ignore($request->sender_ids_id)],
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData);
        }

        //dd($request);
        $updateSenderId = SenderIdRegister::where('id', $request->sender_ids_id)->first();
        $updateSenderId->sir_sender_id = $request->sender_id;
        $updateSenderId->sir_robi_vn = $request->robi_virtual_number;
        $updateSenderId->sir_airtel_vn = $request->airtel_virtual_number;
        $updateSenderId->sir_banglalink_vn = $request->bl_virtual_number;
        $updateSenderId->sir_teletalk_vn = $request->teletalk_virtual_number;
        $updateSenderId->sir_teletalk_user_name = $request->teletalk_user_name;
        $updateSenderId->sir_teletalk_user_password = $request->teletalk_user_password;

        $updateSenderId->sir_gp_vn = $request->gp_virtual_number;

        $updateSenderId->save();


        if ($updateSenderId) {
            session()->flash('type', 'success');
            session()->flash('message', 'SenderId Updated Successfully...');
            return redirect()->back();
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong update sender id...');
            return redirect()->back();
        }
    }


    /*Update sender id status*/
    public function updateStatus($id)
    {
        try {
            SenderIdRegister::where('sir_active', '1')->update(['sir_active' => '0']);
            $senderIdStatus = SenderIdRegister::find($id);
            if ($senderIdStatus->sir_active == 0) {
                $senderIdStatus->sir_active = 1;
            }

            $senderIdStatus->save();

            if ($senderIdStatus) {
                session()->flash('type', 'success');
                session()->flash('message', 'SenderId Status Updated Successfully...');
                return redirect()->back();
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'Something went wrong update sender id status...');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong update sender id status...' . $e->getMessage());
            return redirect()->back();
        }
    }

    /*show delivery sender id list*/
    public function deliverySenderIDList()
    {
        $senderIds = SenderIdRegister::get();

        return view('admin.senderId.delivery_sender_id_list', compact('senderIds'));
    }


    /*check delivery sender id*/
    public function checkDeliverySenderID($id)
    {
        $senderIdDetails = SenderIdRegister::where('id', $id)->first();
        return view('admin.senderId.check_delivery_sender_id_list', compact('senderIdDetails'));
    }


    /*update delivery sender id status*/
    public function updateDeliverySenderId(Request $request, $id)
    {
        try {
            $senderId = SenderIdRegister::where('id', $id)->first();
            if ($senderId) {
                $senderId->sir_robi_confirmation = $request->status_robi;
                $senderId->sir_airtel_confirmation = $request->status_Airtel;
                $senderId->sir_banglalink_confirmation = $request->status_BanglaLink;
                $senderId->sir_teletalk_confirmation = $request->status_teletalk;
                $senderId->sir_gp_confirmation = $request->status_grameen;

                $senderId->save();

                $ret = new stdClass();
                $ret->success = 'successfully updated status';
                return json_encode($ret);
            }
        } catch (\Exception $e) {
            $ret = new stdClass();
            $ret->success = 'something went wrong. please try again later.....';
            return json_encode($ret);
        }

    }


  public function panelCheckDeliverySenderId($id, $operator, $number)
{
    try {
        $virtualNumber = SenderIdRegister::with('robi_virtual_number', 'airtel_virtual_number', 'banglalink_virtual_number', 'teletalk_virtual_number', 'gp_virtual_number')
            ->where('id', $id)
            ->first();
        
        if ($virtualNumber) {
            // API Central key from your working code
            $apiCentral = 'myQ1uzu3mRVWdjVq4A1mV5GscebslZ4y';
            $transType = 'T'; // 'T' for transactional (from your working code)
            
            // Determine message type based on SMS content
            $sms_text_raw = $virtualNumber->sir_sender_id . ' sender id test ' . $operator;
            
            if (\SmsHelper::is_unicode($sms_text_raw)) {
                $msgType = '3'; // Unicode
            } else {
                $msgType = '1'; // Text
            }
            
            $sms_text = $sms_text_raw; // Don't urlencode - the helper handles it
            $numbers = [$number];
            
            if ($operator == 'robi') {
                $user_name = $virtualNumber->robi_virtual_number->sivn_api_user_name;
                $password = $virtualNumber->robi_virtual_number->sivn_api_password;
                $sender = $virtualNumber->sir_sender_id;
                $billMsisdn = $virtualNumber->robi_virtual_number->sivn_number;
                
                $text = \SmsHelper::ra_masking_infozillion_sms(
                    $user_name, 
                    $password, 
                    $sms_text, 
                    $numbers, 
                    $sender, 
                    $billMsisdn, 
                    $apiCentral, 
                    $msgType, 
                    $transType
                );
                return response()->json(['result' => $text]);

            } elseif ($operator == 'grameen') {
                $user_name = $virtualNumber->gp_virtual_number->sivn_api_user_name;
                $password = $virtualNumber->gp_virtual_number->sivn_api_password;
                $sender = $virtualNumber->sir_sender_id;
                $billMsisdn = $virtualNumber->gp_virtual_number->sivn_number;
                
                $text = \SmsHelper::gp_masking_infozillion_sms(
                    $user_name, 
                    $password, 
                    $sms_text, 
                    $numbers, 
                    $sender, 
                    $billMsisdn, 
                    $apiCentral, 
                    $transType, 
                    $msgType
                );
                return response()->json(['result' => $text]);

            } elseif ($operator == 'teletalk') {
                $user_name = $virtualNumber->teletalk_virtual_number->sivn_api_user_name;
                $password = $virtualNumber->teletalk_virtual_number->sivn_api_password;
                $sender = $virtualNumber->sir_sender_id;
                
                $text = \SmsHelper::send_masking_teletalk_sms(
                    $user_name, 
                    $password, 
                    $sms_text, 
                    $number, // Single number, not array for teletalk
                    $sender
                );
                return response()->json(['result' => $text]);

            } elseif ($operator == 'airtel') {
                $user_name = $virtualNumber->airtel_virtual_number->sivn_api_user_name;
                $password = $virtualNumber->airtel_virtual_number->sivn_api_password;
                $sender = $virtualNumber->sir_sender_id;
                $billMsisdn = $virtualNumber->airtel_virtual_number->sivn_number;
                
                $text = \SmsHelper::ra_masking_infozillion_sms(
                    $user_name, 
                    $password, 
                    $sms_text, 
                    $numbers, 
                    $sender, 
                    $billMsisdn, 
                    $apiCentral, 
                    $msgType, 
                    $transType
                );
                return response()->json(['result' => $text]);

            } elseif ($operator == 'banglalink') {
                $user_name = $virtualNumber->banglalink_virtual_number->sivn_api_user_name;
                $password = $virtualNumber->banglalink_virtual_number->sivn_api_password;
                $sender = $virtualNumber->sir_sender_id;
                $billMsisdn = $virtualNumber->banglalink_virtual_number->sivn_number;
                
                $text = \SmsHelper::bl_masking_infozillion_sms(
                    $user_name, 
                    $password, 
                    $sms_text, 
                    $numbers, 
                    $sender, 
                    $billMsisdn, 
                    $apiCentral, 
                    $msgType, 
                    $transType
                );
                return response()->json(['result' => $text]);
            }

        } else {
            return response()->json(['result' => 'can\'t find sender id']);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'something went wrong. please try again ' . $e->getMessage()]);
    }
}

}
