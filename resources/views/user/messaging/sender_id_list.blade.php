@extends('user.master')

@section('messaging_menu_class','open')
@section('sender_id_menu_class','active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('user.index') }}">Dashboard</a>
	</li>
	<li class="active">Sender Id</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Sender Id
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		List
	</small>
</h1>
@endsection


@section('main_content')

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<table id="view_archived_report" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>SL</th>
					<th>SenderID</th>
					<th> Requested On</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>

			<tbody>
			@php($serial=1)
			@foreach($senderIds as $senderId)
				<tr>
					<td>{{ $serial++ }}</td>
					<td>{{ $senderId->sender->sir_sender_id }}</td>
					<td>{{ $senderId->created_at->format('j-M-Y') }}</td>
					<td>{{ ($senderId->status==1)?'Active':'In-active' }}</td>
					<td>
						<a href="{{ route('user.setDefaultSender', $senderId->id) }}"
						   onclick="return confirm('Are you sure you want to make default ?');"
						   class="btn btn-info {{ ($senderId->sender_id==@$defaultSenderId->sender_id)?'btn-success disabled':'btn-primary' }} btn-sm">
							Make default
						</a>
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
	{{-- <style>
		@media(max-width:575px){
            .abcd{
                width: 10px;
            }
        }
	</style> --}}
@endsection
@section('custom_script')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
	// $('#reseller_list').DataTable();
	$(document).ready(function() {
	var table = $('#view_archived_report').DataTable( {
		responsive: true,
		columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
            ]
	} );
} );
</script>
@endsection
