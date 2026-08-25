<?php

namespace App\Http\Controllers\Admin;

use App\Model\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Support\Facades\Validator;


class ResellerLimitController extends Controller
{
    //
    /*show reseller limit apply form*/
    public function limitApplyForm()
    {
        $users = User::with('userDetail')->whereIn('role', [4])->get();
        return view('admin.reseller.reseller_balance_limit', compact('users'));
    }

    /*update reseller limit*/
    public function limitUpdateForm(Request $request, $id){
        $validateData = Validator::make($request->all(), [
            'balanceLimit' => 'required|numeric',
            'employeeLimit' => 'required|numeric',
        ]);

        if($validateData->fails()){
            return redirect()->back()->withErrors($validateData);
        }

        try{
            $updLimit = UserDetail::where('user_id', $id)->first();
            $updEmployeeLimit = User::where('id', $id)->first();
            if($updLimit){
                $updLimit->limit = $request->balanceLimit;
                $updLimit->save();

                $updEmployeeLimit->employee_limit = $request->employeeLimit;
                $updEmployeeLimit->save();

                session()->flash('type', 'success');
                session()->flash('message', 'successfully updated limit.....!');
                return redirect()->back();
            }
            else{
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find user. please try again.......!');
                return redirect()->back();
            }
        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to update limit. please try again........!'.$e);
            return redirect()->back();
        }
    }
}
