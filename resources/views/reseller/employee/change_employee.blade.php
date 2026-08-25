@extends('reseller.master')

@section('employee_menu_class', 'open')
@section('change_employee', 'active')

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
        employee / asign
    </small>
</h1>
@endsection


@section('main_content')
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                @include('reseller.partials.session_messages')

                <form action="{{ route('reseller.employee.change_employee') }}" method="post" class="form-horizontal" role="form"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="userName"> User Name : <span style="color: red;">**</span></label>
                        <br />
                        <select id="userName" name="user_id"
                               class="chosen-select form-control" data-placeholder="Select an User" required="" onchange="get_employee(this.value)">
                               <option value="" hidden></option>
                                @foreach($all_employeed_users as $employeed_user)     
                                    <option value="{{ $employeed_user->id }}">{{ $employeed_user->company_name }} - ( {{ $employeed_user->cellphone }} )</option>
                               @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="form-field-select-3"> Employee name :  <span style="color: red;">**</span></label>
                        <br />    
                        <select id="form-field-select-3" name="employee_id"
                               class="chosen-select form-control" data-placeholder="Select an Employee" required="" >
                               <option value="" hidden></option>
                               @foreach($all_employees as $employee)
                               		<option value="{{ $employee->id }}">{{ $employee->name.' - '.$employee->phone }}</option>
                               @endforeach
                        </select>
                    </div>
                    
                    <div class="clearfix form-group">
                        <div class="col-md-9">
                            <input type="submit" class="btn btn-info" value="Asign">
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
@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
@endsection


@section('custom_script')
@include('reseller.ajax.employee')
    
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect: true});
    </script>

@endsection