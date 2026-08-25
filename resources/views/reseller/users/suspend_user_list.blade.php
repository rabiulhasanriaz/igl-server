@extends('reseller.master')

@section('suspend_user_menu_class','active')
@section('user_menu_class','open')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('reseller.index') }}">Dashboard</a>
	</li>
	<li class="active">User</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	User
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Suspend List
	</small>
</h1>
@endsection

@section('main_content')

<div class="space-6"></div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		@include('reseller.partials.session_messages')
		@include('reseller.partials.all_error_messages')

		<table id="suspend-user-list-table" class="table table-striped table-bordered table-hover">
			<thead>
			<tr>
				<th class="abcd">SL</th>
				<th>Company name</th>
				<th>Name</th>
				<th>Email</th>
				<th class="">Phone</th>
				<th class="">Customar</th>
				<th>System</th>
			</tr>
			</thead>
			<tbody>
			@php($serial=1)
			@foreach($users as $user)
				<tr>
					<td>{{ $serial++ }}</td>
					<td>{{ $user->company_name }}</td>
					<td>{{ $user->userDetail['name'] }}</td>
					<td>{{ $user->email }}</td>
					<td class="">{{ $user->cellphone }}</td>
					<td class="">
						<p style='color:green;'>
							@if($user->role==4)
								Reseller
							@else
								User
							@endif
						</p>
					</td>
					<td>
						<div class="widget-toolbar no-border" style="float: none;">
							<a class="red" href="{{ route('reseller.user.delete', $user->id) }}" onclick="return confirm('Are you sure to delete');">
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
						</div>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div><!-- /.col -->
</div><!-- /.row -->


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
        
        </style>
@endsection


@section('custom_script')
	{{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
	<script type="text/javascript">
	$('#suspend-user-list-table').DataTable();
	</script> --}}
	<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#suspend-user-list-table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
                    { responsivePriority: 6, targets: 5 },
                    { responsivePriority: 7, targets: 6 },
            ]
        } );
    } );
</script>
@endsection
