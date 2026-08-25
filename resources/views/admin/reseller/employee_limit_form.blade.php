@extends('admin.master')

@section('reseller_menu_class','open')
@section('assign_employee_limit', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="#">Balance</a>
        </li>
        <li class="active">Credit</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Credit
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Add
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">

                @include('admin.partials.all_error_messages')
                @include('admin.partials.session_messages')

                <!-- PAGE CONTENT BEGINS -->
                <form action="{{ route('admin.reseller.employee_limit') }}" method="post" class="form-horizontal"
                      role="form">
                    @csrf
                    <div class="form-group">
                        <label for="form-field-select-3"> Company name  <span class="text-success" id="current_employee"></span></label>
                        <br/>
                        <script type="text/javascript">
                            var reseller_limit = [];
                        </script>
                        <select class="chosen-select form-control" id="form-field-select-3"
                                data-placeholder="Company name.." name="user_id" required=""
                                onchange="show_current_limit(this.value)">
                            <option value=""></option>
                            @foreach($resellers as $reseller)
                                <option value="{{ $reseller->id }}"> {{ $reseller->company_name }}- ( {{ $reseller->cellphone }}
                                    )
                                </option>
                                <script type="text/javascript">
                                    var arrInd = "{{ $reseller->id }}";
                                    reseller_limit[arrInd] = "{{ $reseller->employee_limit }}";
                                </script>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="employee_limit">Max Employee Number </label>
                        <input type="number" name="employee_limit_amount" id="employee_limit" value="" class="form-control " placeholder="" min="2" max="30"  required>
                    </div>

                    
                    <div class="clearfix form-group" id="submit_btn_debit">
                        <input type="submit" class="btn btn-info" value="Submit">
                        &nbsp; &nbsp; &nbsp;
                        <button class="btn btn-danger" type="reset">
                            <i class="ace-icon fa fa-undo bigger-110"></i>
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->


@endsection


@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js//moment.min.js"></script>
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
        
    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect: true});    
          function show_current_limit(reseller_id)
          {
            $('#current_employee').text( "Current employee limit is "+reseller_limit[reseller_id] );
            $('#employee_limit').attr('min', reseller_limit[reseller_id])
          }
        
    </script>

@endsection



