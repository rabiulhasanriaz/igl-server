<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\SenderIdUser;
use App\Model\SenderIdUserDefault;
use Illuminate\Support\Facades\Auth;

class SenderIDController extends Controller
{
    //
    public function index(){
        $senderIds = SenderIdUser::where('user_id',Auth::id())->get();
        $defaultSenderId = SenderIdUserDefault::where('user_id',Auth::id())->first();
    	return view('user.messaging.sender_id_list', compact('senderIds', 'defaultSenderId'));
    }

    public function setDefaultSender($id)
    {
        $userSender = SenderIdUser::where(['id'=>$id, 'user_id'=>Auth::id()])->first();
        if($userSender){
            try{

                $userDefaultSender = SenderIdUserDefault::where('user_id',Auth::id())->first();
                if($userDefaultSender){
                    $userDefaultSender->sender_id = $userSender->sender_id;
                    $userDefaultSender->save();

                    session()->flash('type', 'success');
                    session()->flash('message', 'Successfully updated default sender id');
                    return redirect()->back();
                }else{
                    SenderIdUserDefault::create([
                        'user_id'=>Auth::id(),
                        'sender_id'=>$userSender->sender_id,
                    ]);

                    session()->flash('type', 'success');
                    session()->flash('message', 'Successfully added default sender id');
                    return redirect()->back();
                }

            }catch (\Exception $e){
                session()->flash('type', 'danger');
                session()->flash('message', 'Unknown Sender...!');
                return redirect()->back();
            }
        }else{
            session()->flash('type', 'danger');
            session()->flash('message', 'Unknown Sender...!');
            return redirect()->back();
        }
    }
}
