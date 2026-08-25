@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_history_menu_class','open')
@section('flexiload_history_summary_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.flexiload.history-summary') }}">Flexiload</a>
        </li>
        <li class="active">Load History Summary</li>
    </ul><!-- /.breadcrumb -->
@endsection

@section('page_header')
    <h1>
        Flexiload
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Load History Summary
        </small>
    </h1>
@endsection

@section('main_content')
    <div class="space-6"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @include('admin.partials.session_messages')
            
            <!-- Filter Section -->
            <div class="widget-box">
                <div class="widget-header widget-header-small">
                    <h5 class="widget-title lighter">Filter Summary</h5>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <form method="GET" class="form-inline">
                            <div class="form-group">
                                <label for="user_id">User:</label>
                                <select name="user_id" class="form-control input-sm select2-user">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->company_name }} ({{ $user->cellphone }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-left: 10px;">
                                <label for="from_date">From Date:</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="from_date" class="form-control input-sm datepicker" 
                                           placeholder="dd-mm-yyyy" value="{{ request('from_date') }}">
                                    <span class="input-group-addon">
                                        <i class="ace-icon fa fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group" style="margin-left: 10px;">
                                <label for="to_date">To Date:</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="to_date" class="form-control input-sm datepicker" 
                                           placeholder="dd-mm-yyyy" value="{{ request('to_date') }}">
                                    <span class="input-group-addon">
                                        <i class="ace-icon fa fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" style="margin-left: 10px;">
                                <i class="ace-icon fa fa-filter"></i>
                                Filter
                            </button>
                            <a href="{{ route('admin.flexiload.history-summary') }}" class="btn btn-sm btn-default" style="margin-left: 10px;">
                                <i class="ace-icon fa fa-refresh"></i>
                                Reset
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="space-6"></div>

            <!-- Quick Links -->
            <div class="row">
                <div class="col-sm-3">
                    <a href="{{ route('admin.flexiload.user-wise-history') }}" class="btn btn-app btn-primary btn-block">
                        <i class="ace-icon fa fa-users bigger-230"></i>
                    
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ route('admin.flexiload.number-wise-history') }}" class="btn btn-app btn-info btn-block">
                        <i class="ace-icon fa fa-phone bigger-230"></i>
                   
                    </a>
                </div>
            </div>

            <div class="space-6"></div>

            <!-- Summary Statistics -->
            <div class="row">
                <div class="col-sm-3">
                    <div class="widget-box">
                        <div class="widget-header widget-header-flat widget-header-small">
                            <h5 class="widget-title">
                                <i class="ace-icon fa fa-exchange"></i>
                                Total Transactions
                            </h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="pull-left">Count</span>
                                    <span class="pull-right badge badge-primary">{{ $summary->total_transactions ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="widget-box">
                        <div class="widget-header widget-header-flat widget-header-small header-color-green">
                            <h5 class="widget-title">
                                <i class="ace-icon fa fa-money"></i>
                                Total Amount
                            </h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="pull-left">Amount</span>
                                    <span class="pull-right text-success"><strong>৳{{ number_format($summary->total_amount ?? 0, 2) }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="widget-box">
                        <div class="widget-header widget-header-flat widget-header-small header-color-blue">
                            <h5 class="widget-title">
                                <i class="ace-icon fa fa-users"></i>
                                Total Users
                            </h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="pull-left">Count</span>
                                    <span class="pull-right badge badge-info">{{ $summary->total_users ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="widget-box">
                        <div class="widget-header widget-header-flat widget-header-small header-color-orange">
                            <h5 class="widget-title">
                                <i class="ace-icon fa fa-phone"></i>
                                Total Numbers
                            </h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="pull-left">Count</span>
                                    <span class="pull-right badge badge-warning">{{ $summary->total_numbers ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-6"></div>

            <!-- Recent Transactions -->
            <div class="widget-box">
                <div class="widget-header widget-header-flat">
                    <h4 class="widget-title smaller">
                        <i class="ace-icon fa fa-clock-o"></i>
                        Recent Transactions
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        @if($recentTransactions->count() > 0)
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>User</th>
                                    <th>Phone Number</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>
                                        <small>{{ $transaction->transaction_id ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $transaction->trx_user->company_name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->targeted_number }}</td>
                                    <td class="text-success">৳{{ number_format($transaction->campaign_price, 2) }}</td>
                                    <td>
                                        @if($transaction->status == 1)
                                            <span class="label label-success">Success</span>
                                        @else
                                            <span class="label label-danger">Failed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->created_at)
                                            <small>{{ $transaction->created_at->format('d M Y h:i A') }}</small>
                                        @else
                                            <small class="text-muted">N/A</small>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center text-danger">
                            <p>No recent transactions found</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datepicker3.min.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/css/select2.min.css"/>
    <style>
        .select2-container--default .select2-selection--single {
            height: 32px !important;
            padding: 5px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
        }
        .datepicker {
            z-index: 9999 !important;
        }
    </style>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize Select2 for user dropdown
            $('.select2-user').select2({
                placeholder: "Select User",
                allowClear: true,
                width: '200px'
            });

            // Initialize Datepicker
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true
            });

            // Link datepickers
            $('input[name="from_date"]').datepicker().on('changeDate', function(selected){
                var startDate = new Date(selected.date.valueOf());
                $('input[name="to_date"]').datepicker('setStartDate', startDate);
            });

            $('input[name="to_date"]').datepicker().on('changeDate', function(selected){
                var endDate = new Date(selected.date.valueOf());
                $('input[name="from_date"]').datepicker('setEndDate', endDate);
            });
        });
    </script>
@endsection