@extends('reseller.master')

@section('inactive_user','active')
@section('user_menu_class','open')

@section('page_location')
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="{{ route('reseller.index') }}">Dashboard</a>
    </li>
    <li class="active">User Activity</li>
</ul>
@endsection

@section('page_header')
<h1>
    User Activity
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Dynamic Filtering
    </small>
</h1>
@endsection

@section('main_content')
<div class="space-6"></div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        @include('reseller.partials.session_messages')
        @include('reseller.partials.all_error_messages')

        <!-- Filter Controls -->
        <div class="well well-sm">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Days Range:</label>
                        <select id="days-range" class="form-control">
                            <option value="10">Last 10 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="60">Last 60 Days</option>
                            <option value="90">Last 90 Days</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Activity Status:</label>
                        <div>
                            <button id="show-active" class="btn btn-sm btn-info">Active Users</button>
                            <button id="show-inactive" class="btn btn-sm btn-warning">Inactive Users</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button id="reset-filters" class="btn btn-sm btn-default">Reset Filters</button>
                            <button id="export-data" class="btn btn-sm btn-success">Export Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Activity Table -->
        <div class="table-responsive">
            <table id="user-activity-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Cellphone</th>
                        <th>Last SMS Sent</th>
                        <th>Balance</th>
                        <th>Last Login</th>
                        <th>Last Active</th>
                        <th>Activity Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data populated via JS -->
                </tbody>
             </table>
        </div>
    </div>
</div>

<!-- Balance Modal -->
<div id="balanceModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">User Balance Details</h4>
            </div>
            <div class="modal-body">
                <div class="text-center" id="balance-loading" style="display: none;">
                    <i class="ace-icon fa fa-spinner fa-spin fa-2x"></i>
                    <p>Loading balance...</p>
                </div>
                <div id="balance-content">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Company Name:</strong>
                        </div>
                        <div class="col-md-6" id="modal-company-name">-</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Email:</strong>
                        </div>
                        <div class="col-md-6" id="modal-email">-</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Cellphone:</strong>
                        </div>
                        <div class="col-md-6" id="modal-cellphone">-</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Current Balance:</strong>
                        </div>
                        <div class="col-md-6" id="modal-balance">
                            <h3 class="text-primary">-</h3>
                        </div>
                    </div>
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
<link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .activity-active { color: green; font-weight: bold; }
    .activity-inactive { color: #f0ad4e; font-weight: bold; }
    .well { background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
    .btn-balance {
        padding: 2px 8px;
        font-size: 11px;
        line-height: 1.5;
        border-radius: 3px;
    }
    .balance-amount {
        font-weight: bold;
        color: #4CAF50;
    }
</style>
@endsection

@section('custom_script')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    let currentStatusFilter = 'inactive'; // default filter

    var table = $('#user-activity-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: false, // client-side only
        ajax: {
            url: '{{ route("reseller.user.inactiveUser") }}',
            dataSrc: 'data',
            data: function(d) {
                d.days = $('#days-range').val();
                d.status = currentStatusFilter;
            }
        },
        columns: [
            { data: 'id' },
            { data: 'company_name' },
            { data: 'email' },
            { data: 'cellphone' },
            { data: 'last_sms_date' },
            { 
                data: 'id',
                render: function(data, type, row) {
                    return '<button class="btn btn-info btn-xs btn-balance" data-user-id="' + data + '" data-user-name="' + row.company_name + '" data-user-email="' + row.email + '" data-user-phone="' + row.cellphone + '">View Balance</button>';
                }
            },
            { data: 'last_login_time' },
            { data: 'last_active_time' },
            { 
                data: 'activity_status',
                render: function(data) {
                    if(data === 'Active') return '<span class="activity-active">Active</span>';
                    return '<span class="activity-inactive">Inactive</span>';
                }
            }
        ]
    });

    // View balance button click event
    $('#user-activity-table').on('click', '.btn-balance', function() {
        var userId = $(this).data('user-id');
        var companyName = $(this).data('user-name');
        var email = $(this).data('user-email');
        var phone = $(this).data('user-phone');
        
        // Set modal data
        $('#modal-company-name').text(companyName);
        $('#modal-email').text(email);
        $('#modal-cellphone').text(phone);
        $('#modal-balance').html('<h3 class="text-primary"><i class="ace-icon fa fa-spinner fa-spin"></i> Loading...</h3>');
        
        // Show modal
        $('#balanceModal').modal('show');
        
        // Fetch balance via AJAX
        $.ajax({
            url: '{{ route("reseller.user.getBalance") }}',
            type: 'GET',
            data: { user_id: userId },
            success: function(response) {
                if(response.success) {
                    var balance = parseFloat(response.balance).toFixed(3);
                    var balanceClass = balance > 0 ? 'text-success' : 'text-danger';
                    $('#modal-balance').html('<h3 class="' + balanceClass + '"><strong>' + balance );
                } else {
                    $('#modal-balance').html('<h3 class="text-danger">Error: ' + response.message + '</h3>');
                }
            },
            error: function(xhr) {
                $('#modal-balance').html('<h3 class="text-danger">Failed to load balance. Please try again.</h3>');
                console.log('Error:', xhr);
            }
        });
    });

    // Days range filter
    $('#days-range').change(function() { table.ajax.reload(); });

    // Active / Inactive filter buttons
    $('#show-active').click(function() { currentStatusFilter = 'active'; updateButtons(); table.ajax.reload(); });
    $('#show-inactive').click(function() { currentStatusFilter = 'inactive'; updateButtons(); table.ajax.reload(); });

    // Reset filters
    $('#reset-filters').click(function() {
        $('#days-range').val('10'); currentStatusFilter = 'inactive'; updateButtons(); table.ajax.reload();
    });

    // Export
    $('#export-data').click(function() {
        let days = $('#days-range').val();
        let status = currentStatusFilter;
        window.location.href = '{{ route("reseller.user.inactiveUser") }}?export=1&days=' + days + '&status=' + status;
    });

    function updateButtons() {
        if(currentStatusFilter === 'active'){
            $('#show-active').removeClass('btn-info').addClass('btn-success');
            $('#show-inactive').removeClass('btn-warning').addClass('btn-default');
        } else {
            $('#show-active').removeClass('btn-success').addClass('btn-info');
            $('#show-inactive').removeClass('btn-default').addClass('btn-warning');
        }
    }

    updateButtons();
});
</script>
@endsection
