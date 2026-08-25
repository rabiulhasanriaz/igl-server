@extends('reseller.master')


@section('employee_menu_class','open')
@section('pay_to_employee','active')

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
        employee / Pay to Employee
    </small>
</h1>
@endsection


@section('main_content')
        <div class="row">

            <div class="col-sm-10 col-sm-offset-1">
                @include('reseller.partials.all_error_messages')
                @include('reseller.partials.session_messages')

                  <p id="CustomerBalance" class="text-info"></p>
                    <form action="{{ route('reseller.employee.pay_balance') }}" method="post" class="form-horizontal" role="form"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="employeeName"> Employee name :  <span style="color: red;">**</span></label>
                            <br />    
                            <select id="employeeName" name="employee_id" data-placeholder="Select an Employee"
                                   class="chosen-select col-xs-10 col-sm-6"  required="" onchange="get_employee_payable_balance(this.value)" value="">
                                   <option value="" hidden></option>
                                   @foreach($data as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name.' - '.$employee->phone }}</option>
                                    @endforeach
                                   
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="" for="employee_pay_amount"> Amount : </label>
                            <br />    
                            <input type="number" id="employee_pay_amount" name="pay_amount"
                                       class="col-xs-10 col-sm-6" required="" placeholder="0.00 tk" min="0" step=".1" max="">
                                
                            
                        </div>
               
                        <div class="clearfix form-group">
                            <div class="">
                                <input type="submit" class="btn btn-info" value="Pay this amount">
                                &nbsp; &nbsp; &nbsp;
                                <button class="btn btn-danger" type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>

            </div>
        </div>

@include('reseller.ajax.employee_balance')
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