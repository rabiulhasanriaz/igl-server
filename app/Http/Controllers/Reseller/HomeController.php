<?php

namespace App\Http\Controllers\Reseller;

use App\Model\AccSmsRate;
use App\Model\AccUserCreditHistory;
use App\Model\LoadCampaignId;
use App\Model\SenderIdUser;
use App\Model\SenderIdUserDefault;
use App\Model\AccSmsBalance;
use App\Model\LoadCampaign30day;
use App\Model\SmsCampaign_24h;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\BalanceHelper;

class HomeController extends Controller
{
    //
    public function index(){


        $myUsers = Auth::user()->myUsers;
        $users = array();

        $users[] = Auth::id();
        
        foreach ($myUsers as $user){
                    if ( $user['role'] == 5 ){
                      $users[] = $user['id']; // taking user
                    }else{
                      $users[] = $user['id']; // taking a reseller
                      $these_users = \App\Model\User::withTrashed()->where('create_by', $user['id'])->get(); // getting all reseller and users under this resseler (step 1)

                      foreach( $these_users as $this_user ){
                          if ( $this_user['role'] == 5 ){
                            $users[] = $this_user['id'];
                          }else{
                            $users[] = $this_user['id'];
                            $these_users1 = \App\Model\User::withTrashed()->where('create_by', $this_user['id'])->get();

                            foreach($these_users1 as $this_user1) {
                              if ( $this_user1['role'] == 5 ){
                                $users[] = $this_user1['id'];
                              }else{
                                $users[] = $this_user1['id'];
                                $these_users2 = \App\Model\User::withTrashed()->where('create_by', $this_user1['id'])->get();

                                foreach($these_users2 as $this_user2){
                                  if( $this_user2['role'] == 5 ){
                                    $users[] = $this_user2['id'];
                                  }else{
                                    $users[] = $this_user2['id'];
                                    $these_users3 = \App\Model\User::withTrashed()->where('create_by', $this_user2['id'])->get();

                                    foreach($these_users3 as $this_user3){
                                      if($this_user3['role'] == 5){
                                        $users[] = $this_user3['id'];
                                      }else{
                                        $users[] = $this_user3['id'];
                                        $these_users4 = \App\Model\User::withTrashed()->where('create_by', $this_user3['id'])->get();

                                        foreach($these_users4 as $this_user4){
                                          if($this_user4['role'] == 5){
                                            $users[] = $this_user4['id'];
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                      }
                    }

                }
        $balance_bd = BalanceHelper::user_available_balance(Auth::id());

        $data['sms_credit'] = cache('sms_credit', function(){
          return AccSmsRate::with('operator')->where('user_id', Auth::id())->get();
        });
        $data['transactions'] = AccSmsBalance::whereIn('asb_pay_mode', [1,2,3])->where('asb_pay_to', Auth::id())->orderBy('id', 'DESC')->take(5)->get();

        $data['last_week_sms'] = AccUserCreditHistory::where('created_at', '>', Carbon::now()->subWeek(1))->whereIn('user_id', $users)->sum('uch_sms_count');
        $data['last_week_cost'] = AccUserCreditHistory::where('created_at', '>', Carbon::now()->subWeek(1))->whereIn('user_id', $users)->sum('uch_sms_cost');

        $data['last_month_sms'] = AccUserCreditHistory::where('created_at', '>', Carbon::now()->subMonth(1))->whereIn('user_id', $users)->sum('uch_sms_count');
        $data['last_month_cost'] = AccUserCreditHistory::where('created_at', '>', Carbon::now()->subMonth(1))->whereIn('user_id', $users)->sum('uch_sms_cost');
        $dateS = Carbon::now()->startOfMonth()->subMonth(11);
        $dateE = Carbon::now();
        $data['monthly_sms'] = AccUserCreditHistory::select(DB::raw('sum(uch_sms_count) as total_sms'), DB::raw('sum(uch_sms_cost) as total_sms_cost'),  DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
            ->whereBetween('created_at',[$dateS,$dateE])
            ->whereIn('user_id',$users)
            ->groupby('year','month')
            ->orderBy('id', 'desc')
            ->get();
        // dd($data['monthly_sms']);
        $data['monthly_flexiload'] = LoadCampaignId::select(DB::raw('sum(total_number) as total_flexi_number'), DB::raw('sum(total_amount) as total_flexi_amount'),  DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                              ->whereBetween('created_at',[$dateS,$dateE])
                              ->whereIn('user_id',$users)
                              ->groupby('year','month')
                              ->orderBy('id', 'desc')
                              ->get();
        // dd($data['monthly_flexiload']);
        return view('reseller.index',compact('data','balance_bd'));
    }

    public function sms_flexi_reports(){
      $create = Auth::user()->id;
      $user = User::where('create_by',Auth::user()->id)->pluck('id');
      
      $sms_user = SmsCampaign_24h::whereIn('user_id',$user)
                                  ->groupBy('user_id')
                                  ->get();
      
      $flexi_user = LoadCampaign30day::whereIn('user_id',$user)
                                     ->groupBy('user_id')
                                     ->where('created_at','>=', Carbon::now()->subDay())
                                     ->get();
      return view('reseller.reports.sms_flexi_reports',compact('sms_user','flexi_user'));
    }

    public function showPriceList(){
        $smsRates = AccSmsRate::with('country','user','operator')->where('user_id',Auth::id())->get();
    	return view('reseller.price.price_list', compact('smsRates'));
    }

    public function showSenderIdList(){
        $senderIds = SenderIdUser::where('user_id',Auth::id())->get();
        $defaultSenderId = SenderIdUserDefault::where('user_id',Auth::id())->first();
    	return view('reseller.sender_id.sender_id_list', compact('senderIds', 'defaultSenderId'));
    }
    
    /*set default sender id*/
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
