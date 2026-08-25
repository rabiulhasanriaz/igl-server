@extends('admin.master')

@section('virtual_number_menu_class','open')
@section('virtual_number_list_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.senderID.index') }}">Sender ID</a>
        </li>
        <li class="active">Virtual Number</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Virtual Number
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            List
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>

    @include('admin.partials.session_messages')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding bg-container">
                <hr>
                <h3>Virtual number view</h3>
                <div class="table-responsive">
                    <table class="table table-bordered" id="virtual-number-table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Operator Name</th>
                                <th>Date</th>
                                <th>Virtual Number Name</th>
                                <th>Load Amount</th>
                                <th>User ID</th>
                                <th>Virtual Number</th>
                                <th>System</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($serial=1)
                            @foreach($virtualNumbers as $virtualNumber)
                                <tr>
                                    <td>{{ $serial++ }}</td>
                                    <td>{{ $virtualNumber->operator->ope_operator_name }}</td>
                                    <td>{{ $virtualNumber->created_at->format('j M, Y') }}</td>
                                    <td>{{ $virtualNumber->sivn_name }}</td>
                                    <td>{{ $virtualNumber->sivn_load_amount }}</td>
                                    <td>{{ $virtualNumber->sivn_api_user_name }}</td>
                                    <td>
                                        {{ $virtualNumber->sivn_number }} ->
                                        <span style="color: red; background-color: #ddd; border: 2px solid green; padding: 0px 8px; font-weight: bold;">
                                            {{ optional($virtualNumber->{strtolower($virtualNumber->operator->ope_operator_name ?? '')})->count() ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="viewSystem({{ $virtualNumber->id }})" class="label label-info">
                                            View System
                                        </a>
                                    </td>
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons">
                                            <a class="green" href="javascript:void(0)" onclick="checkBalance({{ $virtualNumber->id }}, '{{ addslashes($virtualNumber->sivn_name) }}', '{{ addslashes($virtualNumber->sivn_number) }}')" title="Balance">
                                                <i class="ace-icon glyphicon glyphicon-usd"></i>
                                            </a>
                                            <a class="green" href="{{ route('admin.virtualNumber.edit', $virtualNumber->id) }}" title="Edit">
                                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                                            </a>
                                            <a class="red" onclick="return confirm('Are you sure to delete this?')" href="{{ route('admin.virtualNumber.delete', $virtualNumber->id) }}" title="Delete">
                                                <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Modal -->
    <div class="modal fade" id="balanceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Balance Information</h4>
                </div>
                <div class="modal-body">
                    <div id="balance-loading" style="text-align:center; display:none; padding:20px;">
                        <i class="ace-icon fa fa-spinner fa-spin fa-2x"></i>
                        <p>Loading balance...</p>
                    </div>
                    <div id="balance-result"></div>
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
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endsection

@section('custom_script')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize DataTable
            $('#virtual-number-table').DataTable({
                responsive: true,
                "pageLength": 25,
                "order": [[0, "asc"]]
            });
        });

        function viewSystem(id) {
            alert('System details for ID: ' + id);
        }

        function checkBalance(id, name, number) {
            $('#balance-loading').show();
            $('#balance-result').html('');
            $('#balanceModal').modal('show');
            
            $.ajax({
                url: '{{ route("admin.virtualNumber.balance_query", "") }}/' + id,
                type: 'GET',
                data: {
    refresh: 1
},
                dataType: 'json',
                success: function(response) {
                    $('#balance-loading').hide();
                    
                    if (response.success) {
                        let balance = 'N/A';
                        
                        // Extract balance from nested response
                        if (response.data && response.data.data && response.data.data.availableBalance) {
                            balance = response.data.data.availableBalance;
                        } else if (response.data && response.data.availableBalance) {
                            balance = response.data.availableBalance;
                        } else if (response.data && response.data.data && response.data.data.balance) {
                            balance = response.data.data.balance;
                        } else if (response.data && response.data.balance) {
                            balance = response.data.balance;
                        } else if (response.data && response.data.response && response.data.response.availableBalance) {
                            balance = response.data.response.availableBalance;
                        } else if (response.data && response.data.response && response.data.response.balance) {
                            balance = response.data.response.balance;
                        }
                        
                        let html = '<div class="alert alert-success">';
                        html += '<h4>Balance Information</h4>';
                        html += '<hr>';
                        html += '<p><strong>Virtual Number Name:</strong> ' + name + '</p>';
                        html += '<p><strong>Virtual Number:</strong> ' + number + '</p>';
                    html += '<p><strong>Current Balance:</strong> <span style="font-size:24px;font-weight:bold;color:#28a745;">' + (parseFloat(balance) / 100).toFixed(2) + '</span> TK</p>';
                        if (response.cached) {
                            html += '<p><small class="text-muted"><i class="ace-icon fa fa-database"></i> Cached result (5 minutes)</small></p>';
                        }
                        if (response.data && response.data.data && response.data.data.mnoResponseMessage) {
                            html += '<p><strong>Status:</strong> ' + response.data.data.mnoResponseMessage + '</p>';
                        }
                        html += '</div>';
                        $('#balance-result').html(html);
                    } else {
                        let errorMsg = response.message || 'Failed to get balance';
                        $('#balance-result').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                    }
                },
                error: function(xhr) {
                    $('#balance-loading').hide();
                    let errorMsg = 'Error checking balance';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#balance-result').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            });
        }
    </script>
@endsection
