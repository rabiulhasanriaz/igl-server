@extends('admin.master')

@section('sms_flexi_report_class', 'open')
@section('balance_report_active_class', 'active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Reseller Balance Transaction Reports</li>
    </ul>
@endsection

@section('main_content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
            
            <!-- Beautiful Calendar & Filter Section -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <h4 class="panel-title">
                            <i class="ace-icon fa fa-calendar"></i> Filter Transactions
                        </h4>
                    </div>
                    <div class="panel-body">
                        <form method="GET" action="#" id="filterForm" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Start Date</label>
                                        <div class="col-sm-8">
                                            <div class="input-group date" id="startDatePicker">
                                                <input type="text" name="start_date" class="form-control" value="{{ $start ?? date('Y-m-d') }}" id="start_date">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">End Date</label>
                                        <div class="col-sm-8">
                                            <div class="input-group date" id="endDatePicker">
                                                <input type="text" name="end_date" class="form-control" value="{{ $end ?? date('Y-m-d') }}" id="end_date">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Quick</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="quick_date">
                                                <option value="">Select Range</option>
                                                <option value="today">Today</option>
                                                <option value="yesterday">Yesterday</option>
                                                <option value="last7days">Last 7 Days</option>
                                                <option value="last15days">Last 15 Days</option>
                                                <option value="last30days">Last 30 Days</option>
                                                <option value="this_month">This Month</option>
                                                <option value="last_month">Last Month</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Payment</label>
                                        <div class="col-sm-8">
                                            <select name="payment_mode" class="form-control" id="payment_mode">
                                                <option value="">All Modes</option>
                                                <option value="1">Cash</option>
                                                <option value="2">Bank Deposit</option>
                                                <option value="3">Check</option>
                                                <option value="4">Send SMS</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Deal Type</label>
                                        <div class="col-sm-8">
                                            <select name="deal_type" class="form-control" id="deal_type">
                                                <option value="">All Types</option>
                                                <option value="sms">SMS Campaign</option>
                                                <option value="flexi">Flexi Campaign</option>
                                                <option value="refund">Refund</option>
                                                <option value="purchase">Purchase</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Status</label>
                                        <div class="col-sm-8">
                                            <select name="payment_status" class="form-control" id="payment_status">
                                                <option value="">All Status</option>
                                                <option value="1">Completed</option>
                                                <option value="0">Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Reseller</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="paid_by" class="form-control" placeholder="Search by name or phone" id="paid_by">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Reference</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="reference_id" class="form-control" placeholder="Reference ID" id="reference_id">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Per Page</label>
                                        <div class="col-sm-8">
                                            <select name="per_page" class="form-control" id="per_page">
                                                <option value="25">25</option>
                                                <option value="50" selected>50</option>
                                                <option value="100">100</option>
                                                <option value="200">200</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn btn-primary" id="applyFilters">
                                        <i class="ace-icon fa fa-search"></i> Apply Filters
                                    </button>
                                    <button type="button" class="btn btn-danger" id="resetFilters">
                                        <i class="ace-icon fa fa-refresh"></i> Reset
                                    </button>
                                    <div class="loader" style="display: none; margin-left: 10px;">
                                        <i class="ace-icon fa fa-spinner fa-spin"></i> Loading...
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Summary Cards -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row" id="summaryCards">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="well text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <h4><i class="fa fa-arrow-up"></i> Total Credit</h4>
                            <h3 id="total_credit">0.00</h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="well text-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                            <h4><i class="fa fa-arrow-down"></i> Total Debit</h4>
                            <h3 id="total_debit">0.00</h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="well text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                            <h4><i class="fa fa-balance-scale"></i> Net Balance</h4>
                            <h3 id="net_balance">0.00</h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="well text-center" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                            <h4><i class="fa fa-exchange"></i> Transactions</h4>
                            <h3 id="total_records">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Transaction Details Table -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div id="transactionsTable">
                    <div class="text-center" style="padding: 50px;">
                        <i class="fa fa-calendar fa-3x"></i>
                        <p>Select date range and click "Apply Filters" to load data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        .well {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .well:hover {
            transform: translateY(-5px);
        }
        .panel {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .datepicker {
            cursor: pointer;
        }
        .input-group-addon {
            cursor: pointer;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .input-group-addon:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 8px 20px;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .btn-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            padding: 8px 20px;
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        }
        .table {
            margin-top: 20px;
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px;
        }
        .loader {
            display: inline-block;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border: none;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%) !important;
            color: white !important;
        }
        .label {
            padding: 5px 10px;
            border-radius: 5px;
        }
        .label-success {
            background-color: #28a745;
        }
        .label-warning {
            background-color: #ffc107;
        }
        .label-info {
            background-color: #17a2b8;
        }
        .label-default {
            background-color: #6c757d;
        }
        .label-primary {
            background-color: #007bff;
        }
        .text-success {
            color: #28a745 !important;
            font-weight: bold;
        }
        .text-danger {
            color: #dc3545 !important;
            font-weight: bold;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        .table-bordered {
            border: 1px solid #ddd;
        }
        .table-bordered > thead > tr > th,
        .table-bordered > tbody > tr > td {
            border: 1px solid #ddd;
        }
        .form-control {
            border-radius: 5px;
        }
        .panel-body {
            padding: 20px;
        }
        .control-label {
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px 10px;
            margin-left: 5px;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            margin: 0 5px;
        }
    </style>
@endsection
@section('custom_script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize date pickers
            $('#startDatePicker').datetimepicker({
                format: 'YYYY-MM-DD',
                maxDate: moment(),
                allowInputToggle: true,
                showTodayButton: true
            });
            
            $('#endDatePicker').datetimepicker({
                format: 'YYYY-MM-DD',
                maxDate: moment(),
                allowInputToggle: true,
                showTodayButton: true
            });
            
            // Link date pickers
            $('#startDatePicker').on('dp.change', function(e) {
                $('#endDatePicker').data('DateTimePicker').minDate(e.date);
            });
            
            $('#endDatePicker').on('dp.change', function(e) {
                $('#startDatePicker').data('DateTimePicker').maxDate(e.date);
            });
            
            // Quick date selector
            $('#quick_date').change(function() {
                var today = moment();
                var start_date = '';
                var end_date = '';
                
                switch($(this).val()) {
                    case 'today':
                        start_date = today.format('YYYY-MM-DD');
                        end_date = today.format('YYYY-MM-DD');
                        break;
                    case 'yesterday':
                        start_date = today.clone().subtract(1, 'days').format('YYYY-MM-DD');
                        end_date = today.clone().subtract(1, 'days').format('YYYY-MM-DD');
                        break;
                    case 'last7days':
                        start_date = today.clone().subtract(7, 'days').format('YYYY-MM-DD');
                        end_date = today.format('YYYY-MM-DD');
                        break;
                    case 'last15days':
                        start_date = today.clone().subtract(15, 'days').format('YYYY-MM-DD');
                        end_date = today.format('YYYY-MM-DD');
                        break;
                    case 'last30days':
                        start_date = today.clone().subtract(30, 'days').format('YYYY-MM-DD');
                        end_date = today.format('YYYY-MM-DD');
                        break;
                    case 'this_month':
                        start_date = today.clone().startOf('month').format('YYYY-MM-DD');
                        end_date = today.format('YYYY-MM-DD');
                        break;
                    case 'last_month':
                        start_date = today.clone().subtract(1, 'month').startOf('month').format('YYYY-MM-DD');
                        end_date = today.clone().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
                        break;
                }
                
                if (start_date && end_date) {
                    $('#start_date').val(start_date);
                    $('#end_date').val(end_date);
                    loadTransactions(1);
                }
            });
            
            // Apply filters
            $('#applyFilters').click(function() {
                loadTransactions(1);
            });
            
            // Reset filters
            $('#resetFilters').click(function() {
                $('#start_date').val(moment().format('YYYY-MM-DD'));
                $('#end_date').val(moment().format('YYYY-MM-DD'));
                $('#payment_mode').val('');
                $('#deal_type').val('');
                $('#payment_status').val('');
                $('#paid_by').val('');
                $('#reference_id').val('');
                $('#per_page').val('50');
                $('#quick_date').val('');
                loadTransactions(1);
            });
            
            // Per page change
            $('#per_page').change(function() {
                loadTransactions(1);
            });
            
            // Global function to load transactions via AJAX
            window.loadTransactions = function(page = 1) {
                $('.loader').show();
                $('#transactionsTable').html('<div class="text-center" style="padding: 50px;"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading data...</p></div>');
                
                var formData = {
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    payment_mode: $('#payment_mode').val(),
                    deal_type: $('#deal_type').val(),
                    payment_status: $('#payment_status').val(),
                    paid_by: $('#paid_by').val(),
                    reference_id: $('#reference_id').val(),
                    per_page: $('#per_page').val(),
                    page: page,
                    _token: '{{ csrf_token() }}'
                };
                
                $.ajax({
                    url: '{{ route("admin.reports.balance-transaction-reports") }}',
                    type: 'GET',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#transactionsTable').html(response.data);
                            $('#total_credit').text(response.total_credit);
                            $('#total_debit').text(response.total_debit);
                            $('#net_balance').text(response.net_balance);
                            $('#total_records').text(response.total_records);
                            
                            // Re-attach pagination event handlers
                            attachPaginationEvents();
                        } else {
                            $('#transactionsTable').html('<div class="alert alert-danger text-center">Error loading data. Please try again.</div>');
                        }
                        $('.loader').hide();
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                        $('#transactionsTable').html('<div class="alert alert-danger text-center">Error loading data. Please try again.</div>');
                        $('.loader').hide();
                    }
                });
            };
            
            // Function to attach pagination click events
            function attachPaginationEvents() {
                $('.pagination a').off('click').on('click', function(e) {
                    e.preventDefault();
                    var page = $(this).data('page');
                    if (page) {
                        loadTransactions(page);
                    }
                });
            }
            
            // Initial load
            loadTransactions(1);
        });
    </script>
@endsection
