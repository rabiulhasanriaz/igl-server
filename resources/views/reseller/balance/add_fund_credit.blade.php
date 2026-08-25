@extends('reseller.master')

@section('add_fund_credit_menu_class','active')
@section('acc_details_menu_class','open')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('reseller.index') }}">Dashboard</a>
        </li>
        <li class="active">Price Sms</li>
    </ul>
@endsection

@section('page_header')
    <h1>
        Price & Coverage
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Price List (<span style="color: forestgreen;">Your Payment Balance is: {{ number_format( $paymentable_balance, 2) }}</span>)
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="row bg-container">
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            {{--  --}}
        </div>
        <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">

            @include('reseller.partials.all_error_messages')
            @include('reseller.partials.session_messages')

            <form action="{{ route('reseller.balance.credit.store') }}" method="post" class="form-horizontal"
                      role="form">
                    @csrf
                    <div class="form-group">
                        <label for="form-field-select-3" style="font-size: 20px;">Company name </label>
                        <br/>
                        
                        <select id="employeeName" class="select2 form-control" name="user_id" required="" onchange="user_balance(this.value)">
                            <option value="" hidden>Select an User</option>
                            @foreach($resellers as $reseller)
                                <option value="{{ $reseller->id }}" data-user-id="{{ $reseller->id }}">
                                    {{ $reseller->company_name }}- ({{ $reseller->cellphone }})
                                </option>
                            @endforeach
                        </select>
                    </div>          

                    <div class="form-group">
                        <label for="credit" style="font-size: 20px;">Credit amount <span class="text-success"
                                                                id="CustomerBalance"></span></label>
                        <input type="text" name="credit_ammount" id="credit" value=""
                               class="form-control input-mask-numberTk" style="font-size: 20px;" onkeyup="submit_button_control(this)"
                               placeholder="00.00" maxlength="10" required>
                    </div>

                    <div class="form-group">
                        <label for="payReference" style="font-size: 20px;">Payment reference :</label>
                        <input type="text" name="payment_reference" id="payReference" value="" class="form-control"
                               placeholder="Reference" maxlength="32" style="font-size: 20px;" required>
                    </div>

                    <div class="form-group">
                        <label for="payMethod" style="font-size: 20px;">Payment method :</label>
                        <select style="font-size: 20px;" class="form-control" name="payment_method" required=""
                                onchange="show_terget_time(this.value)">
                            <option value="">Select method</option>
                            <option value="1">Cash</option>
                            <option value="2">Bank deposit</option>
                            <option value="3">Check</option>
                        </select>
                    </div>

                    <div class="form-group" id="target_time" style="display: none;">
                        <label for="target" style="font-size: 20px;">Target time </label>
                        <div class='input-group date' id='datetimepicker2'>
                            <input type="text" name="target_time" id="datetimepicker1" type="text" class="form-control"
                                   placeholder="d-m-yyyyy">
                            <span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
                        </div>
                    </div>

                    <div class="clearfix form-group" id="submit_btn_debit">
                        <input type="submit" id="credit_submit_btn" class="btn btn-info" value="Submit">
                        &nbsp; &nbsp; &nbsp;
                        <button class="btn btn-danger" type="reset">
                            <i class="ace-icon fa fa-undo bigger-110"></i>
                            Reset
                        </button>
                    </div>
                </form>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-left: 20px;">
            <div class="row" style="margin-left: 20px;">
                
                    <div id="transaction-history">
                        <!-- Transaction history will be displayed here -->
                    </div>

            </div>
        </div>
    </div>


@endsection

@section('custom_style')
    <link href="{{ asset('assets') }}/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
@endsection


@section('custom_script')
    <script src="{{ asset('assets') }}/js//moment.min.js"></script>
    <script src="{{ asset('assets') }}/js//bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">

    // =========
        $(document).ready(function() {
            $('.select2').select2();

            $('#employeeName').on('change', function() {
                var selectedUserId = $(this).find('option:selected').data('user-id');
                fetchTransactionHistory(selectedUserId);
            });

            function fetchTransactionHistory(userId) {
                $.ajax({
                    url: '{{ route('reseller.transaction.history') }}',
                    type: 'GET',
                    data: {
                        userId: userId 
                    },
                    success: function(data) {
                        $('#transaction-history').html(data);
                    }
                });
            }
        });
    // ==========

        $(function () {
            $('#datetimepicker1').datetimepicker();
        });

        $(document).ready(function() {
            $('.select2').select2();
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

        function submit_button_control(credit) {

            var resellerBalance = parseFloat("{{$paymentable_balance}}");

            var now_balance = credit.value;
            var bal = now_balance.replace(/,/g,'');
            // alert(resellerBalance);
            if (resellerBalance >= bal && bal > -1) {
                $("#credit_submit_btn").css("display", "inline-block");
            } else {
                $("#credit_submit_btn").css("display", "none");
            }
        }
    </script>
    <script type="text/javascript">
        $("#credit").on('keyup',function(){
            var n = parseInt($(this).val().replace(/\D/g,''),10);
            $(this).val(n.toLocaleString());
        })
    </script>

    @include('admin.ajax.check_customer_available_balance')
@endsection
