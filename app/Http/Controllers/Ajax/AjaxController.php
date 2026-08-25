<?php

namespace App\Http\Controllers\Ajax;

use App\Model\AccSmsBalance;
use App\Model\EmployeeUser;
use App\Model\PhonebookCampaignCategory;
use App\Model\PhonebookCampaignContact;
use App\Model\SenderIdRegister;
use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    //check duplicate email
    public function checkEmailExistence(Request $request)
    {
        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            // invalid emailaddress
            return 'invalidEmail';
        }
        else{
            $checkEmail = User::where('email', $request->email)->first();
            if($checkEmail){
                return 'exist';
            }else{
                return 'valid';
            }
        }
    }

    /*check duplicate email for user update*/
    public function checkEmailExistenceForUpdate(Request $request)
    {
        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            // invalid emailaddress
            return 'invalidEmail';
        }
        else{
            $checkEmail = User::where('email', $request->email)->whereNotIn('id', [$request->user_id])->first();
            if($checkEmail){
                return 'exist';
            }else{
                return 'valid';
            }
        }
    }

    //check duplicate phone
    public function checkPhoneExistence(Request $request)
    {
        $number = trim($request->phone);
        if ( !is_numeric($number) ){
            return 'invalidPhone';
        }else if ( !(substr($number,0,2) == "01" && strlen($number) == 11) ){
            return 'invalidPhone';
        }
        elseif( (substr($number,0,3) != "015") && (substr($number,0,3) != "016") && (substr($number,0,3) != "017") && (substr($number,0,3) != "018") && (substr($number,0,3) != "019") && (substr($number,0,3) != "013") && (substr($number,0,3) != "014")){
            return 'invalidPhone';
        }
        else{
            $checkPhone = User::where('cellphone', $request->phone)->first();
            if($checkPhone){
                return 'exist';
            }else{
                return 'valid';
            }
        }
    }

//check duplicate phone
    public function checkEmployeePhoneExistence(Request $request)
    {
        $number = trim($request->phone);
        if ( !is_numeric($number) ){
            return 'invalidPhone';
        }else if ( !(substr($number,0,2) == "01" && strlen($number) == 11) ){
            return 'invalidPhone';
        }
        elseif( (substr($number,0,3) != "015") && (substr($number,0,3) != "013") && (substr($number,0,3) != "014") && (substr($number,0,3) != "016") && (substr($number,0,3) != "017") && (substr($number,0,3) != "018") && (substr($number,0,3) != "019")){
            return 'invalidPhone';
        }
        else{
            $checkPhone = EmployeeUser::where('phone', $request->phone)->first();
            if($checkPhone){
                return 'exist';
            }else{
                return 'valid';
            }
        }
    }

    //check duplicate phone
    public function checkPhoneExistenceForUpdate(Request $request)
    {
        $number = trim($request->phone);
        if ( !is_numeric($number) ){
            return 'invalidPhone';
        }else if ( !(substr($number,0,2) == "01" && strlen($number) == 11) ){
            return 'invalidPhone';
        }
        elseif( (substr($number,0,3) != "015") && (substr($number,0,3) != "016") && (substr($number,0,3) != "017") && (substr($number,0,3) != "018") && (substr($number,0,3) != "019")){
            return 'invalidPhone';
        }
        else{
            $checkPhone = User::where('cellphone', $request->phone)->whereNotIn('id', [$request->user_id])->first();
            if($checkPhone){
                return 'exist';
            }else{
                return 'valid';
            }
        }
    }


    //check duplicate sender id
    public function checkSenderIdExistence(Request $request)
    {
        $senderId = trim($request->senderId);
        $checkSenderId = SenderIdRegister::where('sir_sender_id', $senderId)->first();
        if($checkSenderId){
            return 'exist';
        }else{
            return 'valid';
        }
    }


    /*check customer available balance*/
    public function checkCustomerAvailableBalance(Request $request)
    {
        try {
            $checkReseller = User::where(['id' => $request->cId, 'role' => '4', 'position' => 1])->first();
            if($checkReseller){
                $customerCredit = AccSmsBalance::where(['asb_pay_to'=>$request->cId])->sum('asb_credit');
                $customerDebit = AccSmsBalance::where(['asb_pay_to'=>$request->cId])->sum('asb_debit');
                return $customerCredit-$customerDebit;
            }
            return '0';
        }
        catch(\Exception $e){
            return '0';
        }
    }

    /*check reseller or customer available balance for reseller panel*/
    public function checkUserAvailableBalance(Request $request)
    {

        try {
            $checkReseller = User::where(['id' => $request->cId, 'create_by' => Auth::id()])->first();
            if($checkReseller){
                $customerCredit = AccSmsBalance::where(['asb_pay_to'=>$request->cId])->sum('asb_credit');
                $customerDebit = AccSmsBalance::where(['asb_pay_to'=>$request->cId])->sum('asb_debit');
                return $customerCredit-$customerDebit;
            }
            return '0';
        }
        catch(\Exception $e){
            return '0';
        }
    }


    /*get category name for edit in modal*/
    public function getCategoryNameForEdit(Request $request)
    {
        try{
            $category = PhonebookCampaignCategory::where('id', $request->cat_id)->first();
            if($category){
                return $category->name;
            }
            return '';
        }
        catch (\Exception $e){
            return '';
        }
    }

    public function getPhoneNumberForEdit(Request $request)
    {
        try{
            $contact = PhonebookCampaignContact::where('id', $request->contact_id)->first();
            if($contact){
                return $contact->phone_number;
            }
            return '';
        }
        catch (\Exception $e){
            return '';
        }
    }

    public function getEmployeeBalance(Request $request) {

    $total_credit = DB::table('employee_user_commissions')->where('eu_id', $request->cId)->sum('euc_credit');
    $total_debit = DB::table('employee_user_commissions')->where('eu_id', $request->cId)->sum('euc_debit');

    return ($total_credit - $total_debit);
        
    }
}
