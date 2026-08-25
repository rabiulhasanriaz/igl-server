@extends('admin.master')

@section('whitelisted_ip_menu_class','open')
@section('non_whitelisted_menu_class', 'active')

@section('page_location')
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="{{ route('admin.index') }}">Dashboard</a>
    </li>
    <li class="active">Non-Whitelisted IP</li>
</ul>
@endsection

@section('page_header')
<h1>
    Non-Whitelisted IP
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Users with Daily Limit (50 SMS/Day)
    </small>
</h1>
@endsection

@section('main_content')
<div class="space-6"></div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        @include('admin.partials.session_messages')

        <div class="clearfix">
            <div class="pull-left">
                <div class="alert alert-warning" style="display: inline-block;">
                    <i class="ace-icon fa fa-exclamation-triangle"></i>
                    <strong>Note:</strong> These users have <strong>NO whitelisted IP</strong> configured.
                    <br>Each user can send maximum <strong>50 SMS per day</strong> from any IP address.
                </div>
            </div>
            <div class="pull-right">
                <a href="{{ route('admin.whitelistedIp.create') }}" class="btn btn-sm btn-success">
                    <i class="ace-icon fa fa-plus"></i>
                    Add Whitelisted IP
                </a>
            </div>
        </div>

        <hr>

        <table id="non-whitelisted-table" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>SL</th>
                <th>Phone Number</th>
                <th>Company Name</th>
                <th>Email</th>
                <th>Daily Limit</th>
                <th>Today's Usage</th>
                <th>Remaining</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
                @php($serial = 1)
                @forelse($users as $user)
                <tr>
                    <td>{{ $serial++ }}</td>
                    <td>{{ $user->cellphone ?? 'N/A' }}</a></td>
                    <td>{{ $user->company_name ?? 'N/A' }}</a></td>
                    <td>{{ $user->email ?? 'N/A' }}</a></td>
                    <td>
                        <span class="label label-primary">
                            <i class="ace-icon fa fa-envelope"></i> 50 SMS/Day
                        </span>
                    </td>
                    <td>
                        @if(($user->today_count ?? 0) > 0)
                            <span class="label label-warning">
                                <i class="ace-icon fa fa-send"></i> {{ $user->today_count }} SMS
                            </span>
                        @else
                            <span class="label label-success">
                                <i class="ace-icon fa fa-check"></i> 0 SMS
                            </span>
                        @endif
                    </td>
                    <td>
                        @if(($user->remaining ?? 50) > 0)
                            <span class="label label-success">
                                <i class="ace-icon fa fa-hourglass-half"></i> {{ $user->remaining }} Left
                            </span>
                        @else
                            <span class="label label-danger">
                                <i class="ace-icon fa fa-ban"></i> Limit Exceeded
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="label label-danger">
                            <i class="ace-icon fa fa-globe"></i> Open Access
                        </span>
                        <br>
                        <small style="color: #ff6600;">(Daily limit active)</small>
                    </td>
                    <td>
                        <div class="hidden-sm hidden-xs action-buttons">
                            <a class="green" href="{{ route('admin.whitelistedIp.edit', $user->id) }}" title="Add Whitelisted IP">
                                <i class="ace-icon fa fa-shield bigger-130"></i>
                            </a>
                            <a class="blue" href="javascript:void(0)" onclick="viewDailyUsage({{ $user->id }})" title="View Daily Usage">
                                <i class="ace-icon fa fa-bar-chart bigger-130"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="alert alert-info">
                            <i class="ace-icon fa fa-info-circle"></i>
                            No non-whitelisted users found. All users have IP whitelist configured.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Daily Usage Modal -->
<div id="dailyUsageModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dailyUsageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="dailyUsageModalLabel">
                    <i class="ace-icon fa fa-bar-chart"></i> Daily SMS Usage Details
                </h4>
            </div>
            <div class="modal-body" id="usageModalBody">
                <div class="text-center">
                    <i class="ace-icon fa fa-spinner fa-spin fa-2x"></i>
                    <p>Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_style')
<link href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/datatable/rowReorder.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<style>
    @media(max-width:575px){
        .abcd{
            width: 130px;
        }
    }
    .label {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 3px;
    }
    .label-primary {
        background-color: #2196F3;
        color: white;
    }
    .label-warning {
        background-color: #ff9800;
        color: white;
    }
    .label-success {
        background-color: #4CAF50;
        color: white;
    }
    .label-danger {
        background-color: #f44336;
        color: white;
    }
    .modal-lg {
        width: 800px;
    }
    @media (min-width: 992px) {
        .modal-lg {
            width: 900px;
        }
    }
    code {
        background-color: #f4f4f4;
        padding: 2px 5px;
        border-radius: 3px;
        font-size: 11px;
    }
    .action-buttons a {
        margin: 0 5px;
    }
    .dataTables_filter {
        float: right;
        margin-bottom: 10px;
    }
    .dataTables_filter input {
        margin-left: 5px;
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
        width: 250px;
    }
    .dataTables_length {
        float: left;
        margin-bottom: 10px;
    }
    .dataTables_info {
        float: left;
        margin-top: 10px;
    }
    .dataTables_paginate {
        float: right;
        margin-top: 10px;
    }
</style>
@endsection

@section('custom_script')

<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var table = $('#non-whitelisted-table').DataTable({
        responsive: true,
        rowReorder: true,
        order: [[0, 'asc']],
        pageLength: 25,
        searching: true,
        language: {
            emptyTable: "No non-whitelisted users found",
            search: "Search Phone, Company or Email:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No matching records found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        columnDefs: [
            { orderable: true, targets: [0, 1, 2, 3] },
            { orderable: false, targets: [4, 5, 6, 7, 8] },
            { searchable: true, targets: [1, 2, 3] },
            { searchable: false, targets: [0, 4, 5, 6, 7, 8] }
        ]
    });
    
    $('.dataTables_filter input').attr('placeholder', 'Search by phone, company or email...');
});

function viewDailyUsage(userId) {
    $('#dailyUsageModal').modal('show');
    $('#usageModalBody').html('<div class="text-center"><i class="ace-icon fa fa-spinner fa-spin fa-2x"></i><p>Loading...</p></div>');
    
    $.ajax({
        url: '{{ route("admin.whitelistedIp.daily_usage", "") }}/' + userId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                var html = '';
                
                if(response.usage_data && response.usage_data.length > 0) {
                    html += '<div class="table-responsive">';
                    html += '<table class="table table-bordered table-striped">';
                    html += '<thead>';
                    html += '<tr>';
                    html += '<th>Date</th>';
                    html += '<th>IP Address</th>';
                    html += '<th>SMS Count</th>';
                    html += '</tr>';
                    html += '</thead><tbody>';
                    
                    $.each(response.usage_data, function(index, data) {
                        html += '<tr>';
                        html += '<td>' + data.limit_date + '</td>';
                        html += '<td><code>' + data.ip_address + '</code></td>';
                        html += '<td><strong>' + data.sms_count + '</strong></td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody>...</div>';
                } else {
                    html += '<div class="alert alert-info">No usage data found for today</div>';
                }
                
                html += '<div class="alert alert-info" style="margin-top: 15px;">';
                html += '<strong>Summary:</strong> Total ' + (response.total_today || 0) + ' SMS used today out of 50 limit.';
                html += '<br><strong>Remaining:</strong> ' + (response.remaining || 50) + ' SMS can be sent today.';
                html += '</div>';
                
                $('#usageModalBody').html(html);
            } else {
                $('#usageModalBody').html('<div class="alert alert-danger">' + (response.message || 'Something went wrong') + '</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $('#usageModalBody').html('<div class="alert alert-danger">Something went wrong. Please try again.</div>');
        }
    });
}
</script>
@endsection
