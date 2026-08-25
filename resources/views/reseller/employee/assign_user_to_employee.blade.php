@extends('reseller.master')

@section('employee_menu_class', 'open')
@section('user_assign_to_employee', 'active')

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

                <form action="{{ route('reseller.employee.asignUser') }}" method="post" class="form-horizontal" role="form"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="form-field-select-3"> Employee name :  <span style="color: red;">**</span></label>
                        <br />    
                        <select id="form-field-select-3" name="employee_id"
                               class="chosen-select form-control" data-placeholder="Select an Employee" required="" >
                               <option value="" hidden></option>
                               @foreach($data['allEmployees'] as $employee)
                               		<option value="{{ $employee->id }}">{{ $employee->name.' - '.$employee->phone }}</option>
                               @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="userName"> User Name : <span style="color: red;">**</span></label>
                        <br />
                        <select id="userName" name="user_id"
                               class="chosen-select form-control" data-placeholder="Select an User" required="" >
                               <option value="" hidden></option>
                               @foreach( $data['allUsers'] as $user )
                               		<option value="{{ $user->id }}">{{ $user->company_name.' - '.$user->cellphone }}</option>
                               @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="userName"> Employee Comission : </label>
                        <br />
                        <input type="text" name="emp_comission" class="form-control" placeholder="Enter Employee Comission">
                    </div>
                    <div class="form-group">
                        <label for="userName"> Customer Comission :</label>
                        <br />
                        <input type="text" name="cus_comission" class="form-control" placeholder="Enter Customer Comission">
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
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect: true});
    </script>
@endsection