@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_history_menu_class','open')
@section('flexiload_number_wise_history_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.flexiload.number-wise-history') }}">Flexiload</a>
        </li>
        <li class="active">Number-wise Load History</li>
    </ul><!-- /.breadcrumb -->
@endsection

@section('page_header')
    <h1>
        Flexiload
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Number-wise Load History (All Time)
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
                    <h5 class="widget-title lighter">Filter Numbers</h5>
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
                                <label for="target_number">Phone Number:</label>
                                <input type="text" name="target_number" class="form-control input-sm" 
                                       placeholder="88017..., 017..., 17..." value="{{ request('target_number') }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" style="margin-left: 10px;">
                                <i class="ace-icon fa fa-filter"></i>
                                Filter
                            </button>
                            <a href="{{ route('admin.flexiload.number-wise-history') }}" class="btn btn-sm btn-default" style="margin-left: 10px;">
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
                <div class="col-sm-4">
                    <div class="widget-box">
                        <div class="widget-header widget-header-small">
                            <h5 class="widget-title lighter">Total Numbers</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="grey pull-left">Unique Numbers</span>
                                    <span class="pull-right">{{ $numberWiseHistory->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="widget-box">
                        <div class="widget-header widget-header-small header-color-blue">
                            <h5 class="widget-title lighter">Total Amount</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="grey pull-left">All Time Amount</span>
                                    <span class="pull-right">৳{{ number_format($numberWiseHistory->sum('total_amount'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="widget-box">
                        <div class="widget-header widget-header-small header-color-green">
                            <h5 class="widget-title lighter">Total Hits</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="clearfix">
                                    <span class="grey pull-left">All Transactions</span>
                                    <span class="pull-right">{{ $numberWiseHistory->sum('total_transactions') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-4"></div>

            <!-- Phone Number Format Info -->
            @if(request('target_number'))
            <div class="alert alert-info">
                <strong>Searching for:</strong> "{{ request('target_number') }}" 
                <br>
                <small>Showing all-time results for this number</small>
            </div>
            @endif

            <!-- Main Table -->
            <table id="number-wise-history-table" class="table table-striped table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Phone Number</th>
                        <th>Owner Name</th>
                        <th>User Info</th>
                        <th>Total Hits</th>
                        <th>Total Amount</th>
                        <th>Last Transaction</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php($serial = 1)
                    @foreach($numberWiseHistory as $history)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>
                            <strong>{{ $history->targeted_number }}</strong>
                        </td>
                        <td>{{ $history->owner_name ?? 'N/A' }}</td>
                        <td>
                            <strong>{{ $history->trx_user->company_name ?? 'N/A' }}</strong>
                            <br>
                            <small class="text-muted">
                                ID: {{ $history->user_id }} | 
                                Phone: {{ $history->trx_user->cellphone ?? 'N/A' }}
                            </small>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-primary" title="Total hits for this number">{{ $history->total_transactions }}</span>
                        </td>
                        <td class="text-success">
                            <strong>৳{{ number_format($history->total_amount, 2) }}</strong>
                        </td>
                        <td>
                            @if($history->last_transaction_date)
                                {{ \Carbon\Carbon::parse($history->last_transaction_date)->format('d M Y h:i A') }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.flexiload.number-detailed-history', $history->targeted_number) }}" 
                               class="btn btn-xs btn-info" title="View all transactions for this number">
                                <i class="ace-icon fa fa-search-plus bigger-120"></i>
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @if($numberWiseHistory->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center text-danger">
                            @if(request('target_number'))
                                No data found for phone number "{{ request('target_number') }}"
                            @else
                                No data found for the selected filter
                            @endif
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/select2.min.css"/>
    <style>
        .select2-container--default .select2-selection--single {
            height: 32px !important;
            padding: 5px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
        }
        .badge {
            font-size: 12px;
            padding: 4px 8px;
        }
    </style>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize DataTable
            $('#number-wise-history-table').DataTable({
                "order": [[ 4, "desc" ]], // Sort by total hits descending
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
        });
    </script>
@endsection
