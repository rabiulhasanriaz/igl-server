<?php

namespace App\Http\Controllers\Admin;

use App\Model\SenderIdRegister;
use App\Model\SenderIdUser;
use App\Model\User;
use App\Model\Operator; // Add this
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserSenderIDController extends Controller
{
    /*show user sender id list*/
    public function index(){
        $userSenders = SenderIdUser::with('user.userDetail', 'sender')->get();
        $senders = SenderIdRegister::get();
        $users = User::with('userDetail')->where('role', '5')->get();
        $operators = Operator::all(); // Get all operators for matching
        
        return view('admin.senderId.user_sender_id', compact('userSenders', 'senders', 'users', 'operators'));
    }

    /*store new user sender id*/
    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'senderId' => 'required',
            'User_id' => 'required',
        ]);

        if($validateData->fails()){
            return redirect()->back()->withErrors($validateData);
        }

        $checkExistSenderId = SenderIdUser::where(['user_id' => $request->User_id, 'sender_id' => $request->senderId])->first();
        if($checkExistSenderId){
            session()->flash('type', 'danger');
            session()->flash('message', 'Already Exist......');
            return redirect()->back();
        }
        else {
            try {
                SenderIdUser::create([
                    'user_id' => $request->User_id,
                    'sender_id' => $request->senderId,
                    'status' => '1',
                ]);

                session()->flash('type', 'success');
                session()->flash('message', 'Successfully added user sender id....');
                return redirect()->back();
            } catch (\Exception $e) {
                session()->flash('type', 'danger');
                session()->flash('message', 'Something went wrong to add sender id......' . $e->getMessage());
                return redirect()->back();
            }
        }
    }

    /*show edit form of user sender id*/
public function edit($id)
{
    $senders = SenderIdRegister::get();
    $users = User::with('userDetail')->get();
    $operators = Operator::all();
    $userSenders = SenderIdUser::with('user.userDetail', 'sender')->get();
    $preIds = SenderIdUser::find($id);
    
    return view('admin.senderId.edit_user_sender_id', compact('preIds', 'senders', 'users', 'operators', 'userSenders'));
}

    /*update user sender id*/
    public function update(Request $request, $id)
    {
        $validateData = Validator::make($request->all(), [
            'sender_id' => 'required',
            'user_id' => 'required',
        ]);

        if($validateData->fails()){
            return redirect()->back()->withErrors($validateData);
        }

        $checkExist = SenderIdUser::where(['user_id'=>$request->user_id, 'sender_id'=>$request->sender_id])->whereNotIn('id', [$id])->get();
        if($checkExist->count()>0){
            session()->flash('type', 'danger');
            session()->flash('message', 'This Sender Id already exist....');
            return redirect()->route('admin.senderID.userSenderID.index');
        }

        try{
            $updSenderIdUser = SenderIdUser::find($id);
            $updSenderIdUser->user_id = $request->user_id;
            $updSenderIdUser->sender_id = $request->sender_id;
            $updSenderIdUser->save();
            session()->flash('type', 'success');    
            session()->flash('message', 'User Sender id Updated successfully');
            return redirect()->route('admin.senderID.userSenderID.index');
        }catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to update user sender id. {'.$e.'}... please try again');
            return redirect()->route('admin.senderID.userSenderID.index');
        }
    }

    /*delete user sender id*/
    public function delete($id)
    {
        try{
            SenderIdUser::where('id',$id)->delete();
            session()->flash('type','success');
            session()->flash('message', 'User Sender id deleted successfully');
            return redirect()->back();
        }catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to delete sender id. {'.$e.'}... please try again');
            return redirect()->route('admin.senderID.userSenderID.index');
        }
    }
}