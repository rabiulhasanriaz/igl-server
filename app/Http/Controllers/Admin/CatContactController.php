<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\insertRootPhoneNumber;
use App\Model\PhonebookCampaignCategory;
use App\Model\PhonebookCampaignContact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Support\Facades\Input;
use Importer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class CatContactController extends Controller
{
    //
    /*show phonebook category list*/
    public function index()
    {
        $categories = PhonebookCampaignCategory::with('CampaignContacts')->get();
        return view('admin.cat_contact.cat_contact_list', compact('categories'));
    }

    /*add new category*/
    public function storeCategory(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'category_name' => 'required|unique:phonebook_campaign_categories,name'
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        try {
            $slug = str_slug($request->category_name);
            PhonebookCampaignCategory::create([
                'name' => $request->category_name,
                'slug' => $slug,
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

            $updCategory = PhonebookCampaignCategory::where('id', $request->group_id)->first();
            if ($updCategory) {
                $slug = str_slug($request->category_name);
                $updCategory->name = $request->category_name;
                $updCategory->slug = $slug;
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
            PhonebookCampaignCategory::where('id', $id)->delete();
            session()->flash('type', 'success');
            session()->flash('message', 'phonebook category deleted successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to delete phonebook category' . $e->getMessage());
            return redirect()->back();
        }
    }


    /*show all contact of a catergory id*/
    public function show($slug)
    {
        try {
            $ids = PhonebookCampaignCategory::where('slug', $slug)->select('id', 'slug')->first();
            $category_id = $ids->id;
            $contacts = PhonebookCampaignContact::with('category')->where('category_id', $category_id)->get();
            $categories = PhonebookCampaignCategory::all();
            return view('admin.cat_contact.cat_contact_details', compact('contacts', 'categories', 'category_id'));
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'can\'t find your category. please try again......');
            $categories = PhonebookCampaignCategory::with('CampaignContacts')->get();
            return redirect()->route('admin.categoryContact.index', compact('categories'));
        }
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
            $checkExist = PhonebookCampaignContact::where(['category_id' => $request->category_id, 'phone_number' => $number])->first();
            if (!$checkExist) {
                try {
                    PhonebookCampaignContact::create([
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
            return redirect()->back()->withErrors($validateData);
        }

        $file = Input::file('contact_file');
        $filename = $request->file('contact_file')->getClientOriginalName();

        $fileType = \FileRead::getFileType($filename);
        if ($fileType == "Excel") {
            $fileContents = Importer::make('Excel')->load($file)->getCollection();
            foreach ($fileContents as $fileContent) {
                $allContacts[] = $fileContent[0];
            }
        } elseif ($fileType == "Csv") {
            $fileContents = Importer::make('Csv')->load($file)->getCollection();
            foreach ($fileContents as $fileContent) {
                $allContacts[] = $fileContent[0];
            }
        } elseif ($fileType == "Text") {
            $fileContent = File::get($file);
            $allContacts = explode(PHP_EOL, $fileContent);
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'Invalid file');
            return redirect()->back();
        }


        $requestVal = $request->except('contact_file');
        try {
            Log::info('first');
            $redisInfo = \Redis::info();
            $insertRootPhoneNumberJob = new insertRootPhoneNumber($allContacts, $requestVal, Auth::id());
            dispatch($insertRootPhoneNumberJob->onQueue('insertPhoneNumber'));

            if($allContacts > 0){
                /*$total_contact_number = PhonebookContact::where('category_id', $request->group_id)->count();
                $res = new stdClass();
                $res->total_contact = $total_contact_number;
                $res->group_id = $request->group_id;
                $res->success = 'Your Valid Number will be added in 10 minutes....!';*/
            }else{

                /*$res = new stdClass();
                $res->error = 'All number are invalid or duplicate......!';*/
                session()->flash('type', 'danger');
                session()->flash('message', 'Something went wrong to add contact. please try again......!');
                return redirect()->back();
            }

        }catch (\Exception $e){
            /*$res = new stdClass();
            $res->error = 'Something went wrong to add phone number. please contact with admin......!';*/
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to add contact. please try again......!');
            return redirect()->back();
        }

        session()->flash('type', 'success');
        session()->flash('message', 'Your valid unique number will be add in 10 min');
        return redirect()->back();

        /*$total_number = 0;
        $add_number = 0;
        foreach ($allContacts as $contact) {
            $total_number++;
            $number = \PhoneNumber::addNumberPrefix($contact);
            if (\PhoneNumber::isValid($number)) {
                $checkExist = PhonebookCampaignContact::where(['category_id' => $request->group_id, 'phone_number' => $number])->first();
                if (!$checkExist) {
                    try {
                        PhonebookCampaignContact::create([
                            'category_id' => $request->group_id,
                            'name' => null,
                            'designation' => null,
                            'phone_number' => $number,
                            'status' => '1',
                        ]);
                        $add_number++;
                    } catch (\Exception $e) {
                        session()->flash('type', 'danger');
                        session()->flash('message', 'Something went wrong to add contact. please try again......!');
                        return redirect()->back();
                    }
                }
            }
        }

        if($add_number > 0){
            session()->flash('type', 'success');
            session()->flash('message', 'Total Number is '.$total_number.' and added number is '.$add_number.'......!');
        }else{
            session()->flash('type', 'danger');
            session()->flash('message', 'All number are invalid or duplicate......!');
        }
        return redirect()->back();*/

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
            $checkExist = PhonebookCampaignContact::where(['category_id' => $request->category_id, 'phone_number' => $number])->whereNotIn('id', [$request->contact_id])->first();
            if (!$checkExist) {
                try {
                    $updContact = PhonebookCampaignContact::where('id', $request->contact_id)->first();
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
    public function deleteContact($slug, $id)
    {
        try{
            PhonebookCampaignContact::where('id', $id)->delete();
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
