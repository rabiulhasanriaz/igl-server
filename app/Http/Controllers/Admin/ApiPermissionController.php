<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\ApiLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\UserDetail;

class ApiPermissionController extends Controller
{
    public function api_user(){
        $api = UserDetail::where('api_key','!=','')
                         ->pluck('id')
                         ->toArray();
                         //dd($api);
        $api_user = User::with('userDetail')
                              //->whereIn('id',$api)
                              ->where('role',5)
                              ->get();
                            //dd($api_user);
        return view('admin.api_permission',compact('api_user'));
    }
  public function apiMonitor()
{
    $logs = ApiLog::with('user')->orderBy('id', 'desc')->limit(100)->get();

    $threadsConnectedData = DB::select("SHOW STATUS LIKE 'Threads_connected'");
    $maxConnectionsData = DB::select("SHOW VARIABLES LIKE 'max_connections'");

    $threadsConnected = isset($threadsConnectedData[0])
        ? $threadsConnectedData[0]->Value
        : 0;

    $maxConnections = isset($maxConnectionsData[0])
        ? $maxConnectionsData[0]->Value
        : 0;

    $errorCount = ApiLog::where('status', 'error')
        ->whereDate('created_at', Carbon::today())
        ->count();

    return view('admin.api-monitor', compact(
        'logs',
        'threadsConnected',
        'maxConnections',
        'errorCount'
    ));
}
public function apiLogDelete($id)
{
    ApiLog::where('id', $id)->delete();

    return redirect()->back()->with('success', 'API log deleted successfully');
}

public function apiLogDeleteAll()
{
    ApiLog::truncate();

    return redirect()->back()->with('success', 'All API logs deleted successfully');
}
    public function api_user_active($id){
        $active = UserDetail::where('user_id',$id)->update(['api_permission' => 1]);
        return redirect()->back()->with(['success' => 'Api Activated Successfully']);
    }
    public function api_user_suspend($id){
        $active = UserDetail::where('user_id',$id)->update(['api_permission' => 2]);
        return redirect()->back()->with(['suspend' => 'Api Suspended!']);
    }
}
