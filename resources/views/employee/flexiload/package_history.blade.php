@extends('employee.master')

@section('load_menu_class','open')
@section('submenu_load','open')
@section('employee_load_history','active')

@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('employee.index') }}">Dashboard</a>
	</li>
	<li class="active">Flexiload</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Package
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		History
	</small>
</h1>
@endsection


@section('main_content')

<div class="row">
	@include('user.partials.session_messages')

	<!-- ------model view start-->
	<div id="my-modal" class="modal fade" tabindex="-1" style="display: none;">
	    <div class="modal-dialog" style="width: 80%;">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	                <h3 class="smaller lighter blue no-margin text-primary"> Flexiload Reports </h3>
	            </div>
	            <div class="modal-body">
	                <div id="SmsInformation"></div>
	            </div>
	        </div>
	    </div><!-- /.modal-dialog -->
	</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<table id="dynamic-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th style="text-align: center;">SL</th>
					<th style="text-align: center;">Purpose</th>
					{{-- <th style="text-align: center;">Total Package</th> --}}
					<th style="text-align: center;">Total Amount</th>
					<th>Created At</th>
				</tr>
			</thead>

			<tbody>
				@php
					{{ $total = 0; }}
				@endphp

			@foreach($employee as $emp)
				<tr>
					<td style="text-align: center;">{{ $loop->iteration }}</td>
					<td style="text-align: center;">{{ ($emp->euc_status == 4)? 'Load': 'Others'}}</td>
					{{-- <td style="text-align: center;">{{ $package->total_package }}</td> --}}
					<td style="text-align: right;">{{ $emp->euc_debit }} TK</td>
					<td>{{ $emp->created_at->format('Y-m-d h:i A') }}</td>
				</tr>
				@php
				( $total += $emp->euc_debit )
				@endphp
			@endforeach
			</tbody>

			<tfoot>
				<tr>
					<th> </th>
					
					<th colspan="1" class="text-right">Total Package Price of This Month : </th>
					<th class="text-right">{{ $total." Tk" }}</th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
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
	<script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script> --}}
	<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
        <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        var table = $('#dynamic-table').DataTable( {
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
	@include('user.flexiload._ajax_campaign_history')

	<script type="text/javascript">
		// $('#dynamic-table').DataTable();
	</script>

@endsection



