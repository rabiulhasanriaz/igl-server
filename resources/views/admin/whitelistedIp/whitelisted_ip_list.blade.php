@extends('admin.master')

@section('whitelisted_ip_menu_class','open')
@section('whitelisted_ip_list_menu_class', 'active')

@section('page_location')
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="{{ route('admin.index') }}">Dashboard</a>
    </li>
    <li>
        <a href="">Users</a>
    </li>
    <li class="active">Whitelisted IP</li>
</ul>
@endsection

@section('page_header')
<h1>
    Whitelisted IP
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
            <div class="clearfix">
                <div class="pull-right">
                    <a href="{{ route('admin.whitelistedIp.create') }}" class="btn btn-sm btn-success">
                        <i class="ace-icon fa fa-plus"></i>
                        Add New Whitelisted IP
                    </a>
                </div>
            </div>
            <hr>
            <h3>Whitelisted IP List</h3>
            <table class="table table-bordered table-responsive" id="whitelisted-ip-table">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Phone Number</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Whitelisted IP(s)</th>
                        <th>IP Count</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serial = 1; ?>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>{{ $user->cellphone }}</td>
                        <td>{{ $user->company_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->userDetail && $user->userDetail->white_listed_ip)
                                <code style="font-size: 12px;">{{ $user->userDetail->white_listed_ip }}</code>
                            @else
                                <span style="color: #ff6600;">Not Configured</span>
                            @endif
                        </td>
                        <td>
                            @if($user->userDetail && $user->userDetail->white_listed_ip)
                                <?php $ipCount = count(explode(',', $user->userDetail->white_listed_ip)); ?>
                                <span style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 12px; font-weight: bold;">
                                    {{ $ipCount }}
                                </span>
                            @else
                                <span style="background-color: #ff9800; color: white; padding: 2px 8px; border-radius: 12px; font-weight: bold;">
                                    0
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->userDetail && $user->userDetail->white_listed_ip)
                                <span style="background-color: #4CAF50; color: white; padding: 3px 10px; border-radius: 4px; font-size: 12px;">
                                    <i class="ace-icon fa fa-shield"></i> Restricted
                                </span>
                            @else
                                <span style="background-color: #f44336; color: white; padding: 3px 10px; border-radius: 4px; font-size: 12px;">
                                    <i class="ace-icon fa fa-globe"></i> Open Access
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="hidden-sm hidden-xs action-buttons">
                                <a class="red" onclick="return confirm('Are you sure to clear whitelisted IP for {{ $user->name }}? This will allow all IPs to access API.')" href="{{ route('admin.whitelistedIp.delete', $user->id) }}" title="Clear IP">
                                    <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                </a>
                                <a class="green" href="{{ route('admin.whitelistedIp.edit', $user->id) }}" title="Edit IP">
                                    <i class="ace-icon fa fa-pencil bigger-130"></i>
                                </a>
                                <a class="blue" href="javascript:void(0)" onclick="checkIpStatus({{ $user->id }})" title="Check IP Status">
                                    <i class="ace-icon glyphicon glyphicon-eye-open"></i>
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
code {
    background-color: #f4f4f4;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 11px;
}
</style>
@endsection

@section('custom_script')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#whitelisted-ip-table').DataTable({
        responsive: true,
        order: [[0, 'asc']]
    });
});

function checkIpStatus(userId) {
    $.ajax({
        url: '{{ route("admin.whitelistedIp.check_status", "") }}/' + userId,
        type: 'GET',
        success: function(response) {
            if(response.success) {
                var message = '';
                if(response.has_whitelist) {
                    message = '✓ Whitelisted IPs: ' + response.white_listed_ip + '\n';
                    message += '✓ Total IPs: ' + response.ip_count + '\n';
                    message += '✓ Only these IPs can access API';
                } else {
                    message = '⚠ No whitelisted IP configured!\n';
                    message += '⚠ API is accessible from ALL IP addresses\n';
                    message += '⚠ Please add whitelisted IP for security';
                }
                alert(message);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Something went wrong. Please try again.');
        }
    });
}
</script>
@endsection
