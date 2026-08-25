<?php

namespace App\Http\Controllers\User;

use App\Model\SmsTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    /*view all template*/
    public function index(){
        $templates = SmsTemplate::where('user_id', Auth::user()->id)->orderBy('st_name', 'asc')->get();
        return view('user.messaging.templates', compact('templates'));
    }

    /*add new template - REQUIRES PASSWORD*/
    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'tmp_name' => 'required',
            'tmp_message' => 'required',
            'password' => 'required',
        ]);

        if($validateData->fails()){
            return redirect()->back()->withErrors($validateData)->withInput();
        }

        // Verify password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->with('type', 'danger')->with('message', 'Incorrect password. Template not saved.');
        }

        if(\SmsHelper::is_unicode($request->tmp_message)){
            $smsType = 2; //unicode
            $sms_number = \SmsHelper::unicode_sms_count($request->tmp_message);

        }else{
            $smsType = 1; //text
            $sms_number = \SmsHelper::text_sms_count($request->tmp_message);
        }

        try{
            SmsTemplate::create([
                'user_id' => Auth::id(),
                'st_name' => $request->tmp_name,
                'st_content' => $request->tmp_message,
                'st_total_sms' => $sms_number,
                'st_content_type' => $smsType,
            ]);

            return redirect()->back()->with('type', 'success')->with('message', 'Successfully added Template....');
        }catch (\Exception $e){
            return redirect()->back()->with('type', 'danger')->with('message', 'Oops! Something went wrong to add template. Please try again....!');
        }
    }

    /*update template - REQUIRES PASSWORD*/
    public function update(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'tmp_name' => 'required',
            'tmp_message' => 'required',
            'template_id' => 'required',
            'password' => 'required',
        ]);

        if($validateData->fails()){
            return redirect()->back()->withErrors($validateData)->withInput();
        }

        // Verify password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->with('type', 'danger')->with('message', 'Incorrect password. Template not updated.');
        }

        $editTemplate = SmsTemplate::where(['id'=>$request->template_id, 'user_id'=>Auth::id()])->first();

        if($editTemplate) {
            if (\SmsHelper::is_unicode($request->tmp_message)) {
                $smsType = 2; //unicode
                $sms_number = \SmsHelper::unicode_sms_count($request->tmp_message);

            } else {
                $smsType = 1; //text
                $sms_number = \SmsHelper::text_sms_count($request->tmp_message);
            }

            try {

                $editTemplate->st_name = $request->tmp_name;
                $editTemplate->st_content = $request->tmp_message;
                $editTemplate->st_total_sms = $sms_number;
                $editTemplate->st_content_type = $smsType;

                $editTemplate->save();

                return redirect()->back()->with('type', 'success')->with('message', 'Successfully updated Template....');
            } catch (\Exception $e) {
                return redirect()->back()->with('type', 'danger')->with('message', 'Oops! Something went wrong to update template. Please try again....!');
            }
        }else{
            return redirect()->back()->with('type', 'danger')->with('message', 'Oops! Can\'t find your template for edit. Please try again....!');
        }
    }

    /*delete a template - NO PASSWORD REQUIRED*/
    public function delete($id)
    {
        $deleteTemplate = SmsTemplate::where(['id'=>$id, 'user_id'=>Auth::id()])->first();

        if($deleteTemplate) {
            $deleteTemplate->delete();
            return redirect()->back()->with('type', 'success')->with('message', 'Successfully deleted Template....');
        }else{
            return redirect()->back()->with('type', 'danger')->with('message', 'Oops! Can\'t find your template for delete. Please try again....!');
        }
    }
}
