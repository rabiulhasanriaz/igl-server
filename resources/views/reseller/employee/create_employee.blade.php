@extends('reseller.master')


@section('employee_menu_class','open')
@section('employee_registration_menu_class','active')

@section('page_location')
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="{{ route('reseller.index') }}">Dashboard</a>
    </li>
    
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
    Dashboard
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        employee / create
    </small>
</h1>
@endsection


@section('main_content')
        <div class="row">
            <div class="col-sm-10">
                @include('reseller.partials.session_messages')

                @if($current_employee_total >= Auth::user()->employee_limit)
                    <p class="alert alert-info">Your Emplyee Limit is exceed, Please contact your reseller.</p>
                @endif

                <form action="{{ route('reseller.employee.store') }}" method="post" class="form-horizontal" role="form"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-company-1"> Employee name : </label>
                        <div class="col-sm-9">
                            <input type="text" id="employeeName" placeholder="Employee name" name="employee_name"
                                   class="col-xs-10 col-sm-5" required="" value=""/>
                            <span class="help-inline col-xs-12 col-sm-7">
                                <span class="middle text-danger" id="employeeShow"> ** </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="employeeEmail"> Email : </label>
                        <div class="col-sm-9">
                            <input type="text" id="employeeEmail" placeholder="Employee Email" name="employee_email"
                                   class="col-xs-10 col-sm-5" required="" value=""/>
                            <span class="help-inline col-xs-12 col-sm-7">
                                <span class="middle text-danger" id="employeeShow"> ** </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-phone-1"> Phone : </label>
                        <div class="col-sm-9">
                            <input type="text" id="mobileNumber" placeholder="Phone" name="employee_phone"
                                   class="col-xs-10 col-sm-5 input-mask-phone" onkeyup="checkPhoneExistence(this.value)"
                                   value="{{ old('phone') }}" data-mask="___________" required="" />
                            <span class="help-inline col-xs-12 col-sm-7">
                                <span class="middle text-danger" id="status"> ** </span>
                                <span class="invalid-phone text-danger"></span>
                                <span class="valid-phone text-success"></span>
                                
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="employeeEmail"> Commision : </label>
                        <div class="col-sm-9">
                            <input type="text" id="employeeCommision" placeholder="Employee Commission" name="employee_commision"
                                   class="col-xs-10 col-sm-5" required="" value=""  />
                            <span class="help-inline col-xs-12 col-sm-7">
                                <span class="middle text-danger" id="employeeShow"> ** </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="employeeEmail"> Password : </label>
                        <div class="col-sm-9">
                            <input type="password" id="employeePassword" placeholder="Employee Password" name="employee_password"
                                   class="col-xs-10 col-sm-5" required="" value=""/>
                            <span class="help-inline col-xs-12 col-sm-7">
                                <span class="middle text-danger" id="employeeShow"> ** </span>
                            </span>
                        </div>
                    </div>


                    
                    <div class="form-group" id="employee_logo" style="">
                        <label class="col-sm-3 control-label no-padding-right" for="form-image-1"> Picture : </label>
                        <div class="col-sm-9">
                            <input type="file" name="image" id="form-image-1">
                        </div>
                    </div>

                    <div class="clearfix form-group">
                        <div class="col-md-offset-3 col-md-9">
                            <input type="submit" class="btn btn-info" value="Registration" 
                                @if($current_employee_total >= Auth::user()->employee_limit)
                                    {{ 'disabled' }}
                                @endif
                            >
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-danger" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div><!-- /.col -->

        </div><!-- /.row -->
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/data-mask.js" type="text/javascript"></script>
    @include('reseller.ajax.check_existance')
@endsection