@extends('reseller.master')

@section('low_balance_user_menu', 'active')
@section('user_menu_class', 'open')

@section('page_location')
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="{{ route('reseller.index') }}">Dashboard</a>
    </li>
    <li class="active">Low Balance Users</li>
</ul>
@endsection

@section('page_header')
<h1>
    Low Balance Users
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Users with balance less than <span id="threshold-display">{{ number_format($threshold) }}</span>
    </small>
</h1>
@endsection

@section('main_content')
<div class="space-6"></div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        @include('reseller.partials.session_messages')
        @include('reseller.partials.all_error_messages')

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="well well-sm text-center stats-card" style="background: #ff9800; color: white;">
                    <h4>Total Low Balance Users</h4>
                    <h2 id="total-users-count">0</h2>
                    <p>Balance &lt; <span id="stat-threshold">{{ number_format($threshold) }}</span></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="well well-sm text-center stats-card" style="background: #2196f3; color: white;">
                    <h4>Total Balance Amount</h4>
                    <h2 id="total-balance-amount">0</h2>
                    <p>Combined balance</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="well well-sm text-center stats-card" style="background: #4caf50; color: white;">
                    <h4>Average Balance</h4>
                    <h2 id="avg-balance-amount">0</h2>
                    <p>Per user average</p>
                </div>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="well well-sm">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Threshold Amount:</label>
                        <select id="threshold-range" class="form-control">
                            <option value="500" {{ $threshold == 500 ? 'selected' : '' }}>Less than 500</option>
                            <option value="1000" {{ $threshold == 1000 ? 'selected' : '' }}>Less than 1000</option>
                            <option value="2000" {{ $threshold == 2000 ? 'selected' : '' }}>Less than 2000</option>
                            <option value="5000" {{ $threshold == 5000 ? 'selected' : '' }}>Less than 5000</option>
                            <option value="10000" {{ $threshold == 10000 ? 'selected' : '' }}>Less than 10000</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button id="refresh-stats" class="btn btn-info btn-sm">
                                <i class="ace-icon fa fa-refresh"></i> Refresh Stats
                            </button>
                            <!-- <a id="export-link" href="{{ route('reseller.user.low_balance_export', ['threshold' => $threshold]) }}" class="btn btn-success btn-sm">
                                <i class="ace-icon fa fa-download"></i> Export CSV
                            </a> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Balance Users DataTable -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="low-balance-table" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Current Balance</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data populated via DataTables AJAX -->
                </tbody>
             </table>
        </div>
    </div>
</div>

<!-- Send Reminder Modal -->
<div id="reminderModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Send Balance Reminder</h4>
            </div>
            <form id="reminder-form" action="{{ route('reseller.user.send_reminder') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="reminder-user-id">
                    <input type="hidden" name="send_via" value="sms">
                    
                    <div class="alert alert-info">
                        <strong>User:</strong> <span id="reminder-user-name">-</span><br>
                        <strong>Current Balance:</strong> <span id="reminder-user-balance">-</span>
                    </div>
                    
                    <div class="form-group">
                        <label>Reminder Message</label>
                        <textarea name="message" id="reminder-message" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="send-reminder-btn">Send Reminder</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('custom_style')
<link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet">
<style>
    .well {
        background: #f5f5f5;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    .stats-card {
        transition: transform 0.3s;
        cursor: pointer;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .label {
        font-size: 12px;
        padding: 4px 8px;
    }
    .label-danger {
        background-color: #d9534f;
    }
    .label-warning {
        background-color: #f0ad4e;
    }
    .label-info {
        background-color: #5bc0de;
    }
    .label-success {
        background-color: #5cb85c;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .dataTables_filter input {
        margin-left: 10px;
    }
</style>
@endsection

@section('custom_script')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
    let currentThreshold = $('#threshold-range').val();
    
    // Initialize DataTable
    var table = $('#low-balance-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ route("reseller.user.low_balance_data") }}',
            type: 'GET',
            data: function(d) {
                d.threshold = currentThreshold;
            },
            error: function(xhr, error, code) {
                console.log('DataTable Error:', xhr.responseText);
                toastr.error('Failed to load data. Please refresh the page.');
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'company_name', name: 'company_name' },
            { data: 'contact_person', name: 'contact_person', orderable: false },
            { data: 'email', name: 'email' },
            { data: 'cellphone', name: 'cellphone' },
            { data: 'balance', name: 'balance', orderable: true },
            { data: 'status', name: 'status', orderable: false },
            { data: 'created_by_name', name: 'created_by_name', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[5, 'asc']], // Sort by balance column (index 5)
        pageLength: 25,
        language: {
            processing: '<i class="ace-icon fa fa-spinner fa-spin fa-2x"></i> Loading...',
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ low balance users",
            infoEmpty: "No low balance users found",
            infoFiltered: "(filtered from _MAX_ total users)",
            zeroRecords: "No matching users found"
        },
        drawCallback: function() {
            // Re-bind reminder button events after each draw
            $('.send-reminder').off('click').on('click', function() {
                var userId = $(this).data('id');
                var userName = $(this).data('name');
                var currentBalance = $(this).data('balance');
                
                $('#reminder-user-id').val(userId);
                $('#reminder-user-name').text(userName);
                $('#reminder-user-balance').text(parseFloat(currentBalance).toFixed(3));
                
                var message = "Dear " + userName + ",\n\nYour current SMS balance is " + parseFloat(currentBalance).toFixed(3) + " credits.\nYour balance is running low. Please recharge your account to continue sending SMS without interruption.\n\nThank you for using our service.";
                $('#reminder-message').val(message);
                
                $('#reminderModal').modal('show');
            });
        }
    });
    
    // Load statistics
    function loadStatistics() {
        $.ajax({
            url: '{{ route("reseller.user.low_balance_stats") }}',
            type: 'GET',
            data: { threshold: currentThreshold },
            success: function(response) {
                if(response.success) {
                    $('#total-users-count').text(response.total_users);
                    $('#total-balance-amount').text(response.total_balance);
                    $('#avg-balance-amount').text(response.average_balance);
                    $('#stat-threshold').text(response.threshold);
                    $('#threshold-display').text(parseInt(response.threshold).toLocaleString());
                }
            },
            error: function() {
                console.log('Failed to load statistics');
            }
        });
    }
    
    // Threshold filter change
    $('#threshold-range').change(function() {
        currentThreshold = $(this).val();
        $('#export-link').attr('href', '{{ route("reseller.user.low_balance_export") }}?threshold=' + currentThreshold);
        table.ajax.reload();
        loadStatistics();
    });
    
    // Refresh stats button
    $('#refresh-stats').click(function() {
        loadStatistics();
        table.ajax.reload();
        toastr.success('Statistics refreshed');
    });
    
    // Initial statistics load
    loadStatistics();
    
    // Reminder form submit
    $('#reminder-form').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        $('#send-reminder-btn').prop('disabled', true).text('Sending...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    $('#reminderModal').modal('hide');
                    toastr.success('Reminder sent successfully!');
                } else {
                    toastr.error('Failed to send reminder: ' + response.message);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Failed to send reminder. Please try again.';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            },
            complete: function() {
                $('#send-reminder-btn').prop('disabled', false).text('Send Reminder');
            }
        });
    });
});

// Toastr notification setup
@if(session('success'))
    toastr.success('{{ session('success') }}');
@endif
@if(session('error'))
    toastr.error('{{ session('error') }}');
@endif
</script>
@endsection