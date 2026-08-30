<?php

namespace App\Http\Controllers\User\Flexiload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Model\LoadFlexibook;
use App\Model\User;
use Illuminate\Support\Facades\Response;
use App\Model\LoadFlexibooksData;
use App\Model\Operator;
use Importer;
use DB;
use App\Helpers\PhoneNumber;
use App\Helpers\FileRead;

use Auth;
use App\Model\PhonebookCategory;

class FlexibookController extends Controller
{

    public function createFlexibook(Request $request)
    {
        $validateData = validator::make($request->all(), [
            'flexibook_name' => 'required',
        ]);

        $book_name = $request->flexibook_name;

        try{
            $flexibook = new LoadFlexibook();
            $flexibook->user_id = auth()->user()->id;
            $flexibook->name = $book_name;
            $flexibook->status = 1;
            $flexibook->save();
            return redirect()->back()->with(['type'=>'success', 'message'=>'Flexibook created successfully']);
        }catch(\Exception $e){
            return redirect()->back()->with(['type'=>'danger', 'message'=>'Something went wrong']);
        }
    }

    // Update flexibook
    public function updateFlexibook(Request $request)
    {
        $validateData = validator::make($request->all(), [
            'book_id' => 'required|numeric',
            'book_name' => 'required',
        ]);
        $book_id = $request->book_id;
        $book_name = $request->book_name;
        try{
            $flexibook = LoadFlexibook::where('id', $book_id)->first();

            $flexibook->name = $book_name;
            $flexibook->save();

            return redirect()->back()->with(['type'=>'success', 'message'=>'Updated successfully']);
        }catch(\Exception $e){
            return redirect()->back()->with(['type'=>'danger', 'message'=>'Something went wrong']);
        }

    }
// Delete flexibook
    public function deleteFlexibook(Request $request, $flexibook_id)
    {   
        $validateData = validator::make($request->all(), [
            'book_id' => 'required|numeric',
        ]);


        
        try{
            LoadFlexibook::where('id', $flexibook_id)->delete();
            LoadFlexibooksData::where('load_flexibooks_id', $flexibook_id)->delete();
            return redirect()->back()->with(['type'=>'success', 'message'=>'Deleted succssfuly']);
        }catch(\EXception $e){
             return redirect()->back()->with(['type'=>'danger', 'message'=>'Something went wrong']);
        }
    }

    public function updateNumberstatus(Request $request){
        // dd($request->all());
        $contactId = LoadFlexibooksData::where('id',$request->statusValue)->first();
        // dd($contactId->status);
        if ($contactId->status == 1) {
            $contactId->status = 2;
            $contactId->save();
            return response()->json([ 
                'success'=> 'Updated Successfully'
            ]);
        }else{
            $contactId->status = 1;
            $contactId->save();
            return response()->json([ 
                'success'=> 'Updated Successfully'
            ]);
        }
        

    }

// Add a contact to a certain flexibook
    public function storeSingleNumber(Request $request)
    {
//        dd($request->all());
        $validateData = validator::make($request->all(), [
            'contact_name' => 'required',
            'contact_number' => 'required',
            'amount' =>'required|integer|min:10|max:50000',
            'number_type' => 'required|min:1|max:2',
            'book_id' => 'required',
            'contactStatus' => 'required',
        ]);
        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData)->withInput();
        }

        $contact_number = $request->contact_number;
        $operator = $request->operator;
        $contact_number = PhoneNumber::addNumberPrefix($contact_number);
        if ( !PhoneNumber::isValid($contact_number) ) {
            return redirect()->back()->with(['type'=>'danger', 'message'=>'Invalid Number']);
        }

        $book_id = $request->book_id;
        $contact_name = $request->contact_name;
        $amount = $request->amount;
        $number_type = $request->number_type;
        $contact_status = $request->contactStatus;
        $remarks = $request->remarks;

        $all_flexibook = LoadFlexibook::where('user_id', auth()->id())->pluck('id')->toArray();
        $allPreviousNumbers = LoadFlexibooksData::whereIn('load_flexibooks_id',$all_flexibook )->pluck('number')->toArray();
        if ( in_array($contact_number, $allPreviousNumbers) ){
            return redirect()->back()->with(['type'=>'danger', 'message'=>'This number already been taken']);
        }
        

        try{
            $data = new LoadFlexibooksData();
            $data->load_flexibooks_id = $book_id;
            $data->name = $contact_name;
            $data->number = $contact_number;
            if ($request->operator != '') {
                $data->operator = $operator;
            }else{
                $data->operator = PhoneNumber::getOperatorNameForLoadByNumber($contact_number);
            }
            $data->number_type = $number_type;
            $data->remarks = $remarks;
            $data->amount = $amount;

            $data->save();

            return redirect()->back()->with(['type'=>'success', 'message'=>'Added successfully']);

        }catch(\Exception $e){
            return redirect()->back()->with(['type'=>'danger', 'message'=>'Something went wrong'.$e->getMessage()]);
        }
    }
// Flexibook details
    public function flexibook_details(Request $request, $flexibook_id)
    {
        $flexibook_data = LoadFlexibook::find($flexibook_id);
        $flexibook_name = $flexibook_data->name;
        $datas = LoadFlexibooksData::where('load_flexibooks_id', $flexibook_id)->get();
        $operator = Operator::all();

        return view('user.flexiload.showABooksData', ['flexibook_id'=>$flexibook_id, 'flexibook_name'=>$flexibook_name, 'contacts'=>$datas],compact('operator'));
    }
// Update a flexibook contact
    public function updateContact(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'contact_number' => 'required',
            'contact_name' => 'required',
            'amount' =>'required|integer|min:10|max:50000',
            'remarks' => 'required',
            'contactStatus' => 'required',
            'contact_id' => 'required',
        ]);
        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData)->withInput();
        }
        
        try{
            $edited_contact = LoadFlexibooksData::find($request->contact_id);
            $edited_contact->name = $request->contact_name;
            $edited_contact->number = $request->contact_number;
            if ($request->operator != '') {
                $edited_contact->operator = $request->operator;
            }else{
                $edited_contact->operator = PhoneNumber::getOperatorNameForLoadByNumber($request->contact_number);
            }
            $edited_contact->amount = $request->amount;
            $edited_contact->remarks = $request->remarks;
            $edited_contact->status = $request->contactStatus;
            $edited_contact->save();
        }catch(\Exception $e){
            return redirect()->back()->with(['type'=>'danger', 'message'=> $e->getMessage()]);
        }
        
        return redirect()->back()->with(['type'=>'success', 'message'=>'Updated Successfuly']);


    }

// Delete a flexibook contact
    public function deleteContact($contact_id)
    {
        try{
            LoadFlexibooksData::where('id', $contact_id)->delete();
            return redirect()->back()->with(['type'=>'success', 'message'=>'Contact Deleted successfully']);
        }catch(\Exception $e){
            return redirect()->back()->with(['type'=>'danger', 'message'=>'Something went wrong']);
        }
    }

// Create flexibook form
    public function createFlxibookForm()
    {


        // $flexibooks = Auth::user()->flexibooks;
        $flexibooks = LoadFlexibook::where('user_id',Auth::id())->orderBy('name','asc')->get();

        $contact_groups = PhonebookCategory::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();
        /*foreach ($contact_groups as $contact_group){
            echo $contact_group->name."<br>";
        }
        exit();*/

    	return view('user.flexiload.createFlexibook', ['flexibooks'=>$flexibooks, 'contact_groups'=>$contact_groups]);
    }




    public function flexibookFileProcess(Request $request)
    {
        

    	$validateData = Validator::make($request->all(), [
            'flexibook_id' => 'required',
            'sms_file' => 'required',
            'flexipin' => 'required',
        ]);
    	
        $user = auth()->user();
        $flexipin = $request->flexipin;

        // Checking flexipin
        if ( $user->flexipin != $flexipin ){
            return redirect()->back()->with(['type'=>'danger', 'message'=>'Wrong Flexipin !']);
        }

        $flexibook_id = $request->flexibook_id;
       
        $file = Input::file('sms_file');
        $filename = $request->file('sms_file')->getClientOriginalName();

        $fileType = FileRead::getFileType($filename);
        $allNames = array();
        $allContacts = array();
        $allAmount = array();
        $allNumberTypes = array();
        $allRemarks = array();
        $allOperator = array();
        $validOperator = [
            'airtel',
            'robi',
            'gp',
            'teletalk',
            'blink',
            'gpst'
        ];

        $all_flexibook = LoadFlexibook::where('user_id', auth()->id())->pluck('id')->toArray();
        $allPreviousNumbers = LoadFlexibooksData::whereIn('load_flexibooks_id',$all_flexibook )->pluck('number')->toArray();

        if ($fileType == "Excel") {
            $fileContents = Importer::make('Excel')->load($file)->getCollection();
            $f = 0;
            
            foreach ($fileContents as $fileContent) {
            	$allNames[$f] = $fileContent[0];
                $allContacts[$f] = PhoneNumber::addNumberPrefix($fileContent[1]);
                $allOperator[$f] = $fileContent[5];
                $allAmount[$f] = (int)$fileContent[2];
                $allNumberTypes[$f] = (int)$fileContent[3];
                $allRemarks[$f] = $fileContent[4];
        
                if( $allNumberTypes[$f] < 1 || $allNumberTypes[$f] > 2 ) {
                    $allNames[$f] = '';
                    $allContacts[$f] = '';
                    $allOperator[$f] = '';
                    $allAmount[$f] = '';
                    $allNumberTypes[$f] = '';
                    $allRemarks[$f] = '';
                    continue;
                }
                if( $allAmount[$f] < 10 || $allAmount[$f] > 50000 )  {
                    $allNames[$f] = '';
                    $allContacts[$f] = '';
                    $allOperator[$f] = '';
                    $allAmount[$f] = '';
                    $allNumberTypes[$f] = '';
                    $allRemarks[$f] = '';
                    continue;
                }
                
                if ( in_array($allContacts[$f], $allPreviousNumbers) )  {
                    $allNames[$f] = '';
                    $allContacts[$f] = '';
                    $allOperator[$f] = '';
                    $allAmount[$f] = '';
                    $allNumberTypes[$f] = '';
                    $allRemarks[$f] = '';
                    continue;
                }

                $allPreviousNumbers[] = $allContacts[$f];

                $f++;
            }
        }

        DB::beginTransaction();

        try{
        	for( $i = 0; $i < count($allContacts); $i++ ){
                if( !PhoneNumber::isValid($allContacts[$i]) ) continue;

        		$f_data = new LoadFlexibooksData();
        		$f_data->load_flexibooks_id = $flexibook_id;
                $f_data->name = $allNames[$i];
        		$f_data->number = $allContacts[$i];
                if (in_array($allOperator[$i],$validOperator)) {
                    $f_data->operator = $allOperator[$i];
                }else{
                    $f_data->operator = PhoneNumber::getOperatorNameForLoadByNumber($allContacts[$i]);                            
                }
        		$f_data->amount = $allAmount[$i];
        		$f_data->number_type = PhoneNumber::checkOperator($allContacts[$i])->id == 3 ? 1 : $allNumberTypes[$i];
        		$f_data->remarks = $allRemarks[$i];
        		$f_data->status = 1;

        		$f_data->save();

        	}
        }catch(\Exception $e){
        	DB::rollBack();
        	return redirect()->back()->with(['type'=>'danger', 'message'=>'Error']);
        }

        DB::commit();
        if ( $f == 0 ){
            $msg = "All numbers are taken before";
        }else{

        }
        return redirect()->back()->with(['type'=>'success', 'message'=>$f.' numbers saved successfully']);

    }
}
