<?php

namespace App\Http\Controllers\User;

use App\Jobs\insertPhoneNumber;
use App\Model\PhonebookCategory;
use App\Model\PhonebookContact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Importer;
use stdClass;

class ContactController extends Controller
{
    /*view all category*/
    public function index(){
        $contact_groups = PhonebookCategory::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();
        /*foreach ($contact_groups as $contact_group){
            echo $contact_group->name."<br>";
        }
        exit();*/
    	return view('user.phonebook.contact_group_list', compact('contact_groups'));
    }

    /*add new category*/
    public function storeCategory(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'category_name' => 'required'
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }


        try {
            $checkExist = PhonebookCategory::where(['name' => $request->category_name, 'user_id' => Auth::id()])->first();
            if($checkExist){
                session()->flash('type', 'danger');
                session()->flash('message', 'This category name already exist. please try again.....');
                return redirect()->back();
            }
            PhonebookCategory::create([
                'user_id' => Auth::id(),
                'name' => $request->category_name,
            ]);

            session()->flash('type', 'success');
            session()->flash('message', 'Successfully added ' . $request->category_name . ' Category.....');
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to add category. please try again.....');
            return redirect()->back();
        }
    }


    /*update phonebook category*/
    public function updateCategory(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'category_name' => 'required',
            'group_id' => 'required'
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        try {

            $updCategory = PhonebookCategory::where('id', $request->group_id)->first();
            if ($updCategory) {
                $updCategory->name = $request->category_name;
                $updCategory->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Successfully updated Category.....');
                return redirect()->back();
            }
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to update category. please try again.....');
            return redirect()->back();

        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to update category. please try again.....');
            return redirect()->back();
        }
    }


    /*delete phonebook category*/
    public function deleteCategory($id)
    {
        try {
            PhonebookCategory::where(['id'=> $id, 'user_id'=>Auth::id()])->delete();
            session()->flash('type', 'success');
            session()->flash('message', 'phonebook category deleted successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to delete phonebook category' . $e->getMessage());
            return redirect()->back();
        }
    }



    /*show all contact of a category*/
    public function show($id){
        /*$contacts = PhonebookContact::with('Category')
            ->where('user_id', Auth::id())
            ->where('category_id', $id)
            ->get();*/
        $contact_groups = PhonebookCategory::where('user_id', Auth::id())
            ->get();
        $group_id = $id;
        $contact_group = PhonebookCategory::where('id', $id)->first();
        $group_name = $contact_group->name;

//    	return view('user.phonebook.contact_details', compact('contacts', 'contact_groups', 'group_id'));
    	return view('user.phonebook.contact_details', compact( 'contact_groups', 'group_id', 'group_name'));
    }


    /*add new contact*/
    public function storeContact(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'contact_number' => 'required',
            'category_id' => 'required',
            'contactStatus' => 'required'
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData);
        }

        $number = \PhoneNumber::addNumberPrefix($request->contact_number);
        if (\PhoneNumber::isValid($number)) {
            $checkExist = PhonebookContact::where(['category_id' => $request->category_id, 'phone_number' => $number])->first();
            if (!$checkExist) {
                try {
                    PhonebookContact::create([
                        'user_id' => Auth::id(),
                        'category_id' => $request->category_id,
                        'name' => $request->contact_name,
                        'designation' => $request->designation,
                        'phone_number' => $number,
                        'status' => $request->contactStatus,
                    ]);

                    session()->flash('type', 'success');
                    session()->flash('message', 'Number added successfully......!');
                    return redirect()->back();
                } catch (\Exception $e) {
                    session()->flash('type', 'danger');
                    session()->flash('message', 'Something went wrong to add contact. please try again......!');
                    return redirect()->back();
                }
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'Number is already exist......!');
                return redirect()->back();
            }
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'Invalid Number.....!');
            return redirect()->back();
        }
    }



    /*get contacts from file*/
    public function importContact(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'group_id' => 'required',
            'contact_file' => 'required'
        ]);

        if ($validateData->fails()) {
//            return redirect()->back()->withErrors($validateData);
            $res = new stdClass();
            $res->errors = $validateData->errors()->all();
            die(json_encode($res));
        }

        $file = Input::file('contact_file');
        $filename = $request->file('contact_file')->getClientOriginalName();

        $fileType = \FileRead::getFileType($filename);
        if ($fileType == "Excel") {
            $fileContents = Importer::make('Excel')->load($file)->getCollection();
            $allContacts = array();
            foreach ($fileContents as $fileContent) {
                $allContacts[] = $fileContent[0];
                if(isset($fileContent[1])) {
                    $allName[] = $fileContent[1];
                }else{
                    $allName[] = '';
                }
            }
        } elseif ($fileType == "Csv") {
            $fileContents = Importer::make('Csv')->load($file)->getCollection();
            foreach ($fileContents as $fileContent) {
                $allContacts[] = $fileContent[0];
                if(isset($fileContent[1])) {
                    $allName[] = $fileContent[1];
                }else{
                    $allName[] = '';
                }
            }
        } elseif ($fileType == "Text") {
            $fileContent = File::get($file);
            $allContacts = explode(PHP_EOL, $fileContent);
            $allName = array();
        } else {
            /*session()->flash('type', 'danger');
            session()->flash('message', 'Invalid file');
            return redirect()->back();*/
            $res = new stdClass();
            $res->error = 'Invalid file...';
            die(json_encode($res));
        }


        $requestVal = $request->except('contact_file');
        try {
            $redisInfo = \Redis::info();
            $insertPhoneNumberJob = new insertPhoneNumber($allContacts, $allName, $requestVal, Auth::id());
            dispatch($insertPhoneNumberJob->onQueue('insertPhoneNumber'));

            if($allContacts > 0){
                $total_contact_number = PhonebookContact::where('category_id', $request->group_id)->count();
                $res = new stdClass();
                $res->total_contact = $total_contact_number;
                $res->group_id = $request->group_id;
                $res->success = 'Your Valid Number will be added in 10 minutes....!';
            }else{

                $res = new stdClass();
                $res->error = 'All number are invalid or duplicate......!';
            }

        }catch (\Exception $e){
            $res = new stdClass();
            $res->error = 'Something went wrong to add phone number. please contact with admin......!';
        }



        die(json_encode($res));
    }


    /*update a contact*/
    public function updateContact(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'contact_id' => 'required',
            'contact_number' => 'required',
            'category_id' => 'required',
            'contactStatus' => 'required'
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData);
        }

        $number = \PhoneNumber::addNumberPrefix($request->contact_number);

        if (\PhoneNumber::isValid($number)) {
            $checkExist = PhonebookContact::where(['user_id' => Auth::id(), 'category_id' => $request->category_id, 'phone_number' => $number])->whereNotIn('id', [$request->contact_id])->first();
            if (!$checkExist) {

                try {
                    $updContact = PhonebookContact::where('id', $request->contact_id)->first();
                    $updContact->category_id = $request->category_id;
                    $updContact->name = $request->contact_name;
                    $updContact->designation = $request->designation;
                    $updContact->phone_number = $number;
                    $updContact->status = $request->contactStatus;

                    $updContact->save();

                    session()->flash('type', 'success');
                    session()->flash('message', 'Number Updated successfully......!');
                    return redirect()->back();
                } catch (\Exception $e) {
                    session()->flash('type', 'danger');
                    session()->flash('message', 'Something went wrong to update contact. please try again......!');
                    return redirect()->back();
                }
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'Number is already exist......!');
                return redirect()->back();
            }
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'Invalid Number.....!');
            return redirect()->back();
        }
    }



    /*delete contact*/
    public function deleteContact($id)
    {
        try{
            PhonebookContact::where(['user_id'=> Auth::id(), 'id'=> $id])->delete();
            session()->flash('type', 'success');
            session()->flash('message', 'successfully deleted contact.....!');
            return redirect()->back();
        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to delete number. please try again.....!');
            return redirect()->back();
        }
    }
}
