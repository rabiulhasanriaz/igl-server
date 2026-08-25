<?php

namespace App\Http\Controllers;

use App\Model\AccSmsRate;
use App\Model\Operator;
use App\Model\SenderIdRegister;
use App\Model\SenderIdUser;
use App\Model\SenderIdUserDefault;
use App\Model\User;
use App\Model\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreUserController extends Controller
{
    public function store(Request $request)
    {
        // dd($request);

        try {
            $returnText = DB::transaction(function () use ($request) {

                $authId = $request->auth_id;
                $authUser = User::with('userDetail')->where('id', $authId)->first();

                if (empty($authUser)) {
                    return "Reseller Account Not Found";
                }

                if ($request->role == '1') {
                    $role = '4';
                } elseif ($request->role == '2') {
                    $role = '5';
                } else {
                    $role = '6';
                }

                /*insert reseller information*/
                $createReseller = User::create([
                    'create_by' => $authId,
                    'name' => $request->name,
                    'email' => $request->email,
                    'cellphone' => $request->phone,
                    'password' => bcrypt($request->password),
                    'status' => '1',
                    'role' => $role,
                    'position' => ($authUser->position + 1),
                ]);

                if ($createReseller == true) {
                    $randid = time();
                    $api_key = '445' . $randid . $createReseller->id . $randid;
                    /*insert reseller details information*/
                    $resellerDetUpd = UserDetail::create([
                        'user_id' => $createReseller->id,
                        'domain_name' => $authUser->userDetail->domain_name,
                        'company_name' => $request->company_name,
                        'designation' => $request->designation,
                        'address' => $request->address,
                        'user_p' => $request->password,
                        'api_key' => $api_key,
                        'logo' => $request->logo,
                    ]);


                    if ($resellerDetUpd == true) {
                        /*insert initial user sms rate as 0*/

                        AccSmsRate::create([
                            'country_id' => '1',
                            'user_id' => $createReseller->id,
                            'operator_id' => '1',
                            'asr_masking' => $request->sram,
                            'asr_nonmasking' => $request->sran,
                        ]);

                        AccSmsRate::create([
                            'country_id' => '1',
                            'user_id' => $createReseller->id,
                            'operator_id' => '2',
                            'asr_masking' => $request->srbm,
                            'asr_nonmasking' => $request->srbn,
                        ]);

                        AccSmsRate::create([
                            'country_id' => '1',
                            'user_id' => $createReseller->id,
                            'operator_id' => '3',
                            'asr_masking' => $request->srgm,
                            'asr_nonmasking' => $request->srgn,
                        ]);

                        AccSmsRate::create([
                            'country_id' => '1',
                            'user_id' => $createReseller->id,
                            'operator_id' => '4',
                            'asr_masking' => $request->srrm,
                            'asr_nonmasking' => $request->srrn,
                        ]);

                        AccSmsRate::create([
                            'country_id' => '1',
                            'user_id' => $createReseller->id,
                            'operator_id' => '5',
                            'asr_masking' => $request->srtm,
                            'asr_nonmasking' => $request->srtn,
                        ]);

                        $senderId = SenderIdRegister::where('sir_active', '1')->orderBy('id', 'desc')->first();
                        SenderIdUser::create([
                            'user_id' => $createReseller->id,
                            'sender_id' => $senderId->id,
                        ]);
                        SenderIdUserDefault::create([
                            'user_id' => $createReseller->id,
                            'sender_id' => $senderId->id,
                        ]);

                        return "success";

                    } else {
                        return "error 20000";
                    }
                } else {
                    return "error 1001";
                }
            });

            return $returnText;

        } catch (\Exception $e) {
            return "error" . $e->getMessage();
        }

    }


    public function getId(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            return response()->json(['id' => $user->id, 'code' => '222']);
        } catch (\Exception $e) {
            return response()->json(['code' => '444' . $e->getMessage()]);
        }
    }
}