<?php

namespace App\Http\Controllers\Reseller;

use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\Controller;
use App\Rules\UserMobile;
use App\Model\EmployeeUser;
use App\Model\EmployeeUserCommission;
use App\Model\User;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['allEmployee'] = EmployeeUser::where('create_by', Auth::id())->get();


        return view('reseller.employee.employee_list')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $current_employee_total = EmployeeUser::where('create_by', Auth::id())->count();

        return view('reseller.employee.create_employee', compact('current_employee_total'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'employee_name' => ['required'],
            'employee_email' => ['required', 'email', 'unique:employee_users, email'],
            'employee_phone' => ['required', 'unique:employee_users,phone', new UserMobile],
            'password' => ['required', 'min:3'],
        ]);

        $current_employee_total = EmployeeUser::where('create_by', Auth::id())->count();

        if ( $current_employee_total >= Auth::user()->employee_limit ) {
            session()->flash('message', 'Employee limit exceed');
            session()->flash('type', 'danger');
            return redirect()->back();
        }

        $employee_name = $request->employee_name;
        $employee_email = $request->employee_email;
        $employee_phone = $request->employee_phone;
        $employee_commision = $request->employee_commision;
        $employee_password = $request->employee_password;

        $reseller_id = Auth()->user()->id;

        if ($request->hasFile('image')) {
            $files = $request->file('image');
            $name = str_random(20) . 'iglweb' . '.' . $files->getClientOriginalExtension();
            $destinationPath = 'assets/uploads/User_Logo';
            $url = $destinationPath . "/" . $name;
            $files->move($destinationPath, $name);
        }else{
            $name = "";
        }

        $create_employee = EmployeeUser::create([
            'create_by' => $reseller_id,
            'name' => $employee_name,
            'email' => $employee_email,
            'phone' => $employee_phone,
            'commission' => $employee_commision,
            'password' => bcrypt($employee_password),
            'employee_p' => $employee_password,
            'avatar' => $name,
            ]);

        if( $create_employee ){
            session()->flash('message', 'Employee Successfully created');
            session()->flash('type', 'success');
            return view('reseller.employee.create_employee', compact('current_employee_total'));
        }else{
            session()->flash('message', 'Something wrong');
            session()->flash('type', 'danger');
            return view('reseller.employee.create_employee' ,compact('current_employee_total'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = EmployeeUser::find($id);
        return view('reseller.employee.edit_employee', compact('employee', $employee));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validateData = Validator::make($request->all(), [
            'employee_name' => ['required'],
            'employee_email' => ['required', 'email', 'unique:employee_users, email'],
            'employee_phone' => ['required', 'unique:employee_users,phone', new UserMobile],
            'password' => ['required', 'min:3'],

        ]);

        $employee_name = $request->employee_name;
        $employee_email = $request->employee_email;
        $employee_phone = $request->employee_phone;
        $employee_commision = $request->employee_commision;
        $employee_password = $request->employee_password;

        $create_employee = EmployeeUser::where('id', $id)->update([
            'name' => $employee_name,
            'email' => $employee_email,
            'phone' => $employee_phone,
            'commission' => $employee_commision,
            'password' => bcrypt($employee_password),
            'employee_p' => $employee_password,
            ]);

        if( $create_employee ){
            session()->flash('message','Employee Updated Successfully');
            session()->flash('type','success');
            return redirect( route('reseller.employee.index') );
        }else{
            session()->flash('message','Something went wrong');
            session()->flash('type','danger');
            return redirect()->back();
        }

    }

    public function asignUser(Request $request) {
        $data['allEmployees'] = EmployeeUser::where('create_by', Auth::user()->id )->get();
        $data['allUsers'] = User::where('create_by', Auth::user()->id)
            ->where(function ($query) {
                $query->where('employee_user_id', '');
                $query->orWhere('employee_user_id', NULL);
                $query->orWhere('flexi_emp_comission', NULL);
                $query->orWhere('flexi_cus_comission', NULL);
            })
            ->get();
        return view('reseller.employee.assign_user_to_employee', compact('data', $data));
    }

    public function asignUserProcess(Request $request) {
        $emp_id = $request->employee_id;
        $user_id = $request->user_id;
        $emp_commission = $request->emp_comission;
        $cus_commission = $request->cus_comission;

        $asign_user = User::where('id', $user_id)->update([
            'employee_user_id' => $emp_id,
            'flexi_emp_comission' => $emp_commission,
            'flexi_cus_comission' => $cus_commission,
            ]);

        if ( $asign_user ){
            return redirect()->route('reseller.employee.index');
        }
    }

    public function pay_balance_create() {
        $data = EmployeeUser::where('create_by', Auth::user()->id)->get();

        return view('reseller.employee.pay_balance', compact('data', $data));
    }

    public function pay_balance_process(Request $request) {
        try{
            $pay_to_employee = EmployeeUserCommission::create([
                'eu_ref_id' => 'paid by-'.Auth::id(),
                'eu_id' => $request->employee_id,
                'euc_credit' => 0,
                'euc_debit' => $request->pay_amount,
                'euc_status' => 3,
                ]);

            if ( $pay_to_employee ){
                session()->flash('message', 'Payment Succesfull');
                session()->flash('type', 'success');
                return redirect()->back();
            }
        }catch(\Exception $e){
            session()->flash('message', 'Something Wrong');
            session()->flash('type', 'danger');
            return redirect()->back();
        }

    }

    public function employee_users_list($emp_id)
    {
        $users = User::where('employee_user_id', $emp_id)
                        
                        ->get();
        return view('reseller.employee.employee_users_list', compact('users'));
    }

    public function changeEmployeView()
    {
        $all_employeed_users = \OtherHelpers::get_all_users(Auth::id());
        $all_employees = \OtherHelpers::get_all_employees(Auth::id());
        return view('reseller.employee.change_employee', compact('all_employeed_users','all_employees'));
    }

    public function changeEmployeeProcess(Request $request) {
        $user_id = $request->user_id;
        $employee_id = $request->employee_id;

        try
        {
            $user = User::where('id', $user_id)->first();
            $user->employee_user_id = $employee_id;
            $user->save();

            session()->flash('message', "The employee for this user has been changed Successfully");
            session()->flash('type', 'success');

        }catch(\Exception $e){
            session()->flash('message', 'Something went wrong');
            session()->flash('type', 'danger');
        }
        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
