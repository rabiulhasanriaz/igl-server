@extends('admin.master')

@section('sender_id_menu_class','open')
@section('delivery_sender_id_menu_class', 'active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('admin.index') }}">Dashboard</a>
	</li>
	<li class="active">Delivery Sender ID</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Delivery Sender ID
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		 List
	</small>
</h1>
@endsection

@section('main_content')

<div class="space-6"></div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

		<table id="delivery-sender-id-list-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Sender id</th>
					<th>Teletalk</th>
					<th>Robi </th>
					<th>Grameen</th>
					<th>Airtel </th>
					<th>BanglaLink</th>
					<th>System</th>
				</tr>
			</thead>

			<tbody>
			@php($serial=1)
			@foreach($senderIds as $senderId)
				<tr>
					<td>{{ $serial++ }}</td>
					<td>{{ $senderId->sir_sender_id }}</td>

					@if($senderId->sir_teletalk_confirmation==1)
						<td class="text-success">Active</td>
					@else
						<td class="text-danger">Inactive</td>
					@endif

					@if($senderId->sir_robi_confirmation==1)
						<td class="text-success">Active</td>
					@else
						<td class='text-danger'> Inactive</td>
					@endif

					@if($senderId->sir_gp_confirmation==1)
						<td class="text-success">Active</td>
					@else
						<td class='text-danger'>Inactive</td>
					@endif

					@if($senderId->sir_airtel_confirmation==1)
						<td class="text-success">Active</td>
					@else
						<td class='text-danger'> Inactive</td>
					@endif

					@if($senderId->sir_banglalink_confirmation==1)
						<td class="text-success">Active</td>
					@else
						<td class='text-danger'>Inactive</td>
					@endif

					<td><a href="{{ route('admin.senderID.checkDeliverySenderID', $senderId->id) }}"><button class="btn btn-xs btn-primary">Check</button></a></td>
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
	{{--<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#delivery-sender-id-list-table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 5 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
                    { responsivePriority: 6, targets: 4 },
                    { responsivePriority: 7, targets: 6 },
            ]
        } );
    } );
</script>--}}
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#delivery-sender-id-list-table').DataTable( {
            rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
            
        } );
        
    } );
    </script>

@endsection




{{-- @section('custom_script')
	<script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
	<script type="text/javascript">
	$('#delivery-sender-id-list-table').DataTable();
	</script>
@endsection --}}
