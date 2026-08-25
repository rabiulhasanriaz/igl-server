<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\UserDetail;

class CampaignPermissionController extends Controller
{
    public function total_user(){
        $total_user = User::with('userDetail')->where('role',5)
                        ->get();
                            //   dd($api_user);
        return view('admin.campaign-permission',compact('total_user'));
    }
    public function campaign_permission_active($id){
        $active = UserDetail::where('user_id',$id)->update(['campaign_permission' => 1]);
        return redirect()->back()->with(['success' => 'Permission Given Successfully']);
    }
    public function campaign_permission_suspend($id){
        $active = UserDetail::where('user_id',$id)->update(['campaign_permission' => 0]);
        return redirect()->back()->with(['suspend' => 'Permission Rejected!']);
    }
}
