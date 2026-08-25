<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\UserDetail;
use App\Model\AssignRoute;
use App\Model\RouteRegister;
use App\Model\SmsDesktopCampaignId;
use DB;

class DynamicPermissionController extends Controller
{
    public function dynamic_user(){
        $api = UserDetail::where('api_key','!=','')
                         ->pluck('id')
                         ->toArray();
        $api_user = User::with('userDetail')->whereIn('id',$api)
                              ->where('role',5)
                              ->get();
                            //   dd($api_user);
        return view('admin.dynamic-permission',compact('api_user'));
    }
    public function dynamic_user_active($id){
        $active = UserDetail::where('user_id',$id)->update(['dynamic_permission' => 1]);
        return redirect()->back()->with(['success' => 'Permission Given Successfully']);
    }
    public function dynamic_user_suspend($id){
        $active = UserDetail::where('user_id',$id)->update(['dynamic_permission' => 0]);
        return redirect()->back()->with(['suspend' => 'Permission Suspended!']);
    }

    public function route_registers(){
        $routes = RouteRegister::where('status',1)->get();
        return view('admin.english.route-registers',compact('routes'));
    }

    public function route_register_store(Request $request){

        $request->validate([
            'route_name' => 'required',
            'api_username' => 'required|unique:route_registers,user_name',
            'api_password' => 'required'
        ]);

            $english = new RouteRegister();
            $english->route_name = $request->route_name;
            $english->user_name = $request->api_username;
            $english->password = $request->api_password;
            $english->save();

            session()->flash('type', 'success');
            session()->flash('message', 'Entry Succeed');
            return redirect()->back();
        

    }

    public function assign_route(){
        $routes = RouteRegister::where('status',1)->get();
        $assinged_routes = AssignRoute::with('userDetail','routeDetail')->where('status',1)->get();
        $users = User::where('role',5)
                     ->whereNotExists(function($query){
                        $query->select(DB::raw(1))
                        ->from('assign_routes')
                        ->whereRaw('assign_routes.user_id = users.id');
                     })
                     ->get();
        
        return view('admin.english.assign-route',compact('routes','users','assinged_routes'));
    }

    public function assign_route_store(Request $request){

        $request->validate([
            'route_name' => 'required',
            'user_name' => 'required|unique:assign_routes,user_id',
        ]);
        $route = new AssignRoute();
        $route->route = $request->route_name;
        $route->user_id = $request->user_name;
        $route->save();

        session()->flash('type', 'success');
        session()->flash('message', 'Route Assigned Successfully');
        return redirect()->back();
    }

    public function route_edit($id){
        $route = RouteRegister::where('id',$id)->first();
        return view('admin.english.route-edit',compact('route'));
    }

    public function route_update(Request $request,$id){
        RouteRegister::where('id',$id)->update([
            'route_name' => $request->route_name,
            'user_name' => $request->api_username,
            'password' => $request->api_password,
            'status' => $request->status
        ]);

        session()->flash('type', 'success');
        session()->flash('message', 'Route Updated Successfully');
        return redirect()->route('admin.english.route-registers');
    }

    public function route_delete($id){
        // dd($id);
        RouteRegister::where('id',$id)->delete();
        session()->flash('type', 'success');
        session()->flash('message', 'Route Delete Successfully');
        return redirect()->back();

    }

    public function assign_route_edit($id){
        $assinged = AssignRoute::where('id',$id)->first();
        $routes = RouteRegister::where('status',1)->get();
        $users = User::where('role',5)->get();
        return view('admin.english.assign-route-edit',compact('assinged','routes','users'));
    }

    public function assign_route_update(Request $request,$id){
        AssignRoute::where('id',$id)->update([
            'route' => $request->route_name,
            'user_id' => $request->user_name,
            'status' => $request->status
        ]);

        session()->flash('type', 'success');
        session()->flash('message', 'Assigned Route Updated Successfully');
        return redirect()->route('admin.english.assign-route');
    }

    public function assigned_route_delete($id){
        // dd($id);
        AssignRoute::where('id',$id)->delete();
        session()->flash('type', 'success');
        session()->flash('message', 'Assigned Route Deleted Successfully');
        return redirect()->back();

    }


    public function route_2_report(){
        $sms_report = SmsDesktopCampaignId::with('user')->select(DB::raw('count(*) as total,user_id'),DB::raw('sum(sdci_total_submitted) as total_submit'), DB::raw('sum(sdci_total_cost) as total_cost'))
                                     ->groupBy('user_id')
                                     ->get();

                                    //  dd($sms_report);
        return view('admin.route2',compact('sms_report'));
    }

    public function route2DetailAjax(Request $request){
        $details = SmsDesktopCampaignId::where('user_id',$request->user)
                                        ->get();
        return view('admin.ajax.route2details',compact('details'));
    }


    public function dynamic_schedule(){
        $api = UserDetail::where('api_key','!=','')
                         ->pluck('id')
                         ->toArray();
        $api_user = User::with('userDetail')->whereIn('id',$api)
                              ->where('role',5)
                              ->get();
                            //   dd($api_user);
        return view('admin.campaign-reshedule',compact('api_user'));
    }

    public function scheduleUpdate(Request $request){
        $active = UserDetail::where('user_id',$request->statusValue)->first();
        // dd($active);
        if ($active->campaign_reschedule == 1) {
            $active->campaign_reschedule = 0;
            $active->save();
            return response()->json([ 
                'success'=> 'Updated Successfully'
            ]);
        }else{
            $active->campaign_reschedule = 1;
            $active->save();
            return response()->json([ 
                'success'=> 'Updated Successfully'
            ]);
        }
    }
    
}
