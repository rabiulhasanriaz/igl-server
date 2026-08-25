@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_history_menu_class','open')
@section('flexiload_user_wise_history_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.flexiload.user-wise-history') }}">Flexiload</a>
        </li>
        <li class="active">User-wise Load History</li>
    </ul><!-- /.breadcrumb -->
@endsection

@section('page_header')
    <h1>
        Flexiload
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            User-wise Load History
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
                    <h5 class="widget-title lighter">Filter History</h5>
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
                            <a href="{{ route('admin.flexiload.user-wise-history') }}" class="btn btn-sm btn-default" style="margin-left: 10px;">
                                <i class="ace-icon fa fa-refresh"></i>
                                Reset
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="space-4"></div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-sm-3">
                    <div class="widget-box">
                        <div class="widget-header widget-header-small">
                            <h5 class="widget-title lighter">Total Users</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="grey pull-left">Count</span>
                                    <span class="pull-right">{{ $userWiseHistory->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="widget-box">
                        <div class="widget-header widget-header-small header-color-blue">
                            <h5 class="widget-title lighter">Total Amount</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="grey pull-left">Amount</span>
                                    <span class="pull-right">৳{{ number_format($userWiseHistory->sum('total_amount'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="widget-box">
                        <div class="widget-header widget-header-small header-color-green">
                            <h5 class="widget-title lighter">Total Transactions</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="grey pull-left">Count</span>
                                    <span class="pull-right">{{ $userWiseHistory->sum('total_transactions') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="widget-box">
                        <div class="widget-header widget-header-small header-color-orange">
                            <h5 class="widget-title lighter">Unique Numbers</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="grey pull-left">Count</span>
                                    <span class="pull-right">{{ $userWiseHistory->sum('unique_numbers') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-4"></div>

            <!-- Main Table -->
            <table id="user-wise-history-table" class="table table-striped table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>User Info</th>
                        <th>Company</th>
                        <th>Total Transactions</th>
                        <th>Unique Numbers</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php($serial = 1)
                    @foreach($userWiseHistory as $history)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>
                            <strong>{{ $history->trx_user->company_name ?? 'N/A' }}</strong>
                            <br>
                            <small class="text-muted">
                                ID: {{ $history->user_id }} | 
                                Phone: {{ $history->trx_user->cellphone ?? 'N/A' }}
                            </small>
                        </td>
                        <td>{{ $history->trx_user->company_name ?? 'N/A' }}</td>
                        <td class="text-center">
                            <span class="badge badge-primary">{{ $history->total_transactions }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $history->unique_numbers }}</span>
                        </td>
                        <td class="text-success">
                            <strong>৳{{ number_format($history->total_amount, 2) }}</strong>
                        </td>
                        <td>
                            <a href="{{ route('admin.flexiload.user-detailed-history', $history->user_id) }}" 
                               class="btn btn-xs btn-info">
                                <i class="ace-icon fa fa-search-plus bigger-120"></i>
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @if($userWiseHistory->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center text-danger">No data found for the selected filter</td>
                    </tr>
                    @endif
                </tbody>
            </table>
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
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script src="{{ asset('assets') }}/js/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>
    <script type="text/javascript">
       $(document).ready(function() {
    // Initialize DataTable
    $('#user-wise-history-table').DataTable({
        "order": [[ 5, "desc" ]], // Sort by total amount descending
        "pageLength": 25,
        "language": {
            "search": "Search in table:"
        }
    });

    // Initialize Select2 for user dropdown
    $('.select2-user').select2({
        placeholder: "Select User",
        allowClear: true,
        width: '200px'
    });

    // Initialize Datepicker with proper configuration
    $('.datepicker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        clearBtn: true
    });

    // Link datepickers for better UX
    $('input[name="from_date"]').datepicker().on('changeDate', function(selected) {
        if (selected.date) {
            var startDate = new Date(selected.date.valueOf());
            $('input[name="to_date"]').datepicker('setStartDate', startDate);
            
            // If to_date is before from_date, clear it
            var toDateVal = $('input[name="to_date"]').val();
            if (toDateVal) {
                var toDate = $('input[name="to_date"]').datepicker('getDate');
                if (toDate < startDate) {
                    $('input[name="to_date"]').val('').datepicker('update');
                }
            }
        }
    });

    $('input[name="to_date"]').datepicker().on('changeDate', function(selected) {
        if (selected.date) {
            var endDate = new Date(selected.date.valueOf());
            $('input[name="from_date"]').datepicker('setEndDate', endDate);
            
            // If from_date is after to_date, clear it
            var fromDateVal = $('input[name="from_date"]').val();
            if (fromDateVal) {
                var fromDate = $('input[name="from_date"]').datepicker('getDate');
                if (fromDate > endDate) {
                    $('input[name="from_date"]').val('').datepicker('update');
                }
            }
        }
    });

    // Form validation to ensure from_date is not greater than to_date
    $('form').on('submit', function(e) {
        var fromDate = $('input[name="from_date"]').val();
        var toDate = $('input[name="to_date"]').val();
        
        if (fromDate && toDate) {
            var from = $('input[name="from_date"]').datepicker('getDate');
            var to = $('input[name="to_date"]').datepicker('getDate');
            
            if (from > to) {
                alert('From date cannot be greater than To date');
                e.preventDefault();
                return false;
            }
        }
    });
});
    </script>
@endsection
