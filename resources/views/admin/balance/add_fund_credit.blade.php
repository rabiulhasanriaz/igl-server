@extends('admin.master')

@section('account_menu_class','open')
@section('add_fund_credit_menu_class', 'active')
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
                <form action="{{ route('admin.balance.credit.store') }}" method="post" class="form-horizontal"
                      role="form">
                    @csrf
                    <div class="form-group">
                        <label for="form-field-select-3"> Company name </label>
                        <br/>
                        <select class="chosen-select form-control" id="form-field-select-3"
                                data-placeholder="Company name.." name="user_id" required=""
                                onchange="customer_balance(this.value)">
                            <option value=""></option>
                            @foreach($resellers as $reseller)
                                <option value="{{ $reseller->id }}"> {{ $reseller->company_name }}- ( {{ $reseller->cellphone }}
                                    )
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="credit">Credit amount <span class="text-success" id="CustomerBalance"></span></label>
                        <input type="text" name="credit_ammount" id="credit" value="" class="form-control input-mask-numberTk" placeholder="00.00" maxlength="10" required>
                    </div>

                    <div class="form-group">
                        <label for="payReference"> Payment reference :</label>
                        <input type="text" name="payment_reference" id="payReference" value="" class="form-control"
                               placeholder="......" maxlength="32" required>
                    </div>

                    <div class="form-group">
                        <label for="payMethod"> Payment method :</label>
                        <select class="form-control" name="payment_method" required="" onchange="show_terget_time(this.value)">
                            <option value="">Select method</option>
                            <option value="1">Cash</option>
                            <option value="2">Bank deposit</option>
                            <option value="3">Check</option>
                        </select>
                    </div>

                    <div class="form-group" id="target_time" style="display: none;">
                        <label for="target"> Target time </label>
                        <div class='input-group date' id='datetimepicker2'>
                            <input type="text" name="target_time" id="datetimepicker1" type="text" class="form-control" placeholder="d-m-yyyyy">
                            <span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
                        </div>
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
    <link href="{{ asset('assets') }}/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js//moment.min.js"></script>
    <script src="{{ asset('assets') }}/js//bootstrap-datetimepicker.min.js"></script>
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#datetimepicker1').datetimepicker();
        });


        $('.chosen-select').chosen({allow_single_deselect: true});
        function show_terget_time(value) {
            if (value == '1') {
                $('#target_time').hide();
            }
            else if (value == '2') {
                $('#target_time').show();
            }
            else if (value == '3') {
                $('#target_time').show();
            }

        }
    </script>
    @include('admin.ajax.check_customer_available_balance')
@endsection