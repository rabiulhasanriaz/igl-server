<?php

namespace App\Http\Controllers\Admin;

use App\Model\AccSmsBalance;
use App\Model\AccUserCreditHistory;
use App\Model\SmsCamPending;
use App\Model\SmsDesktopPending;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\SmsCampaignId;
use App\Model\SmsDesktopCampaignId;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SmsCampaignController extends Controller
{
    public function showPendingSmsCampaigns()
    {
        $pending_campaigns = SmsCampaignId::with('sender')
            ->whereIn('sci_deal_type', ['1','2'])
            ->where('sci_campaign_status', 0)
            ->orderBy('id', 'desc')
            ->get();
        // $dynamic_campaigns = SmsDesktopCampaignId::where('sdci_deal_type', '1')
        //     ->where('sdci_campaign_status', 0)
        //     ->orderBy('id', 'desc')
        //     ->get();
        // dd($pending_campaigns);
        return view('admin.pending_for_approval_campaign_sms_report', compact('pending_campaigns'));
    }

    public function acceptPendingSmsCampaigns(Request $request,$campaign_id)
    {
        DB::beginTransaction();
        try {
            $campaign_details = SmsCampaignId::where('id', $campaign_id)->first();
            if (!empty($campaign_details)) {
                SmsCampaignId::where('id', $campaign_id)
                    ->where('sci_campaign_status', 0)
                    ->where('sci_route', 0)
                    ->update([
                        'sci_campaign_status' => 1,
                        'sci_route' => $request->route,
                    ]);
                SmsCamPending::where('campaign_id', $campaign_id)
                    ->where('scp_campaign_status', 0)
                    ->where('scp_route', 0)
                    ->update([
                        'scp_campaign_status' => 1,
                        'scp_route' => $request->route
                    ]);
            } else {
                DB::rollBack();

                session()->flash('type', 'danger');
                session()->flash('message', 'Invalid Campaign');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to accept campaign');
            return redirect()->back();
        }
        DB::commit();

        session()->flash('type', 'success');
        session()->flash('message', 'successfully accepted campaign');
        return redirect()->back();
    }

public function acceptDynamicCampaigns($campaign_id)
    {
        DB::beginTransaction();
        try {
            $campaign_details = SmsDesktopCampaignId::where('id', $campaign_id)->first();
            if (!empty($campaign_details)) {
                SmsDesktopCampaignId::where('id', $campaign_id)
                    ->where('sdci_campaign_status', 0)
                    ->update([
                        'sdci_campaign_status' => 1
                    ]);
                SmsDesktopPending::where('campaign_id', $campaign_id)
                    ->where('sdp_campaign_status', 0)
                    ->update([
                        'sdp_campaign_status' => 1
                    ]);
            } else {
                DB::rollBack();

                session()->flash('type', 'danger');
                session()->flash('message', 'Invalid Campaign');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to accept campaign');
            return redirect()->back();
        }
        DB::commit();

        session()->flash('type', 'success');
        session()->flash('message', 'successfully accepted campaign');
        return redirect()->back();
    }
    public function rejectPendingSmsCampaigns($campaign_id)
    {
        DB::beginTransaction();
        try {
            $campaign_details = SmsCampaignId::where('id', $campaign_id)->first();
            if (!empty($campaign_details)) {
                SmsCampaignId::where('id', $campaign_id)
                    ->where('sci_campaign_status', 0)
                    ->update([
                        'sci_campaign_status' => 2
                    ]);
                SmsCamPending::where('campaign_id', $campaign_id)
                    ->where('scp_campaign_status', 0)
                    ->delete();


                /*credit user balance*/
                $user_id = $campaign_details->user_id;
                $user_det = User::where('id', $campaign_details->user_id)->first();
                $user_position = $user_det->position;

                while ($user_position >= 1) {

                    /*find cost details*/
                    $pre_acc_sms_balance = AccSmsBalance::where('asb_pay_to', $user_det->id)
                        ->where('asb_pay_ref', $campaign_details->sci_campaign_id)
                        ->where('asb_pay_mode', '4')
                        ->where('asb_deal_type', '2')
                        ->orderBy('id', 'desc')
                        ->first();

                    if (!empty($pre_acc_sms_balance)) {
                        /*refund cost*/
                        \App\Helpers\BalanceHelper::addCredit(
                            $user_det->create_by,
                            $user_det->id,
                            $campaign_details->sci_campaign_id,
                            $pre_acc_sms_balance->asb_debit,
                            6,
                            1,
                            1,
                            Carbon::now(),
                            0
                        );
                    }


                    $user_det = User::where('id', $user_det->create_by)->first();
                    $user_position = $user_det->position;
                }

                /*delete user credit history*/
                AccUserCreditHistory::where('campaign_id', $campaign_id)->where('user_id', $campaign_details->user_id)->delete();


            } else {
                DB::rollBack();

                session()->flash('type', 'danger');
                session()->flash('message', 'Invalid Campaign');
                return redirect()->back();
            }


        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to reject campaign');
            return redirect()->back();
        }
        DB::commit();

        session()->flash('type', 'info');
        session()->flash('message', 'successfully rejected campaign');
        return redirect()->back();
    }

    public function rejectDynamicPendingSmsCampaigns($campaign_id)
    {
        DB::beginTransaction();
        try {
            $campaign_details = SmsDesktopCampaignId::where('id', $campaign_id)->first();
            if (!empty($campaign_details)) {
                SmsDesktopCampaignId::where('id', $campaign_id)
                    ->where('sdci_campaign_status', 0)
                    ->update([
                        'sdci_campaign_status' => 2
                    ]);
                SmsDesktopPending::where('campaign_id', $campaign_id)
                    ->where('sdp_campaign_status', 0)
                    ->delete();


                /*credit user balance*/
                $user_id = $campaign_details->user_id;
                $user_det = User::where('id', $campaign_details->user_id)->first();
                $user_position = $user_det->position;

                while ($user_position >= 1) {

                    /*find cost details*/
                    $pre_acc_sms_balance = AccSmsBalance::where('asb_pay_to', $user_det->id)
                        ->where('asb_pay_ref', $campaign_details->sci_campaign_id)
                        ->where('asb_pay_mode', '4')
                        ->where('asb_deal_type', '2')
                        ->orderBy('id', 'desc')
                        ->first();

                    if (!empty($pre_acc_sms_balance)) {
                        /*refund cost*/
                        \App\Helpers\BalanceHelper::addCredit(
                            $user_det->create_by,
                            $user_det->id,
                            $campaign_details->sci_campaign_id,
                            $pre_acc_sms_balance->asb_debit,
                            6,
                            1,
                            1,
                            Carbon::now(),
                            0
                        );
                    }

                    $user_det = User::where('id', $user_det->create_by)->first();
                    $user_position = $user_det->position;
                }

                /*delete user credit history*/
                AccUserCreditHistory::where('campaign_id', $campaign_id)->where('user_id', $campaign_details->user_id)->delete();


            } else {
                DB::rollBack();

                session()->flash('type', 'danger');
                session()->flash('message', 'Invalid Campaign');
                return redirect()->back();
            }


        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to reject campaign');
            return redirect()->back();
        }
        DB::commit();

        session()->flash('type', 'info');
        session()->flash('message', 'successfully rejected campaign');
        return redirect()->back();
    }
}
