@extends('reseller.master')

@section('user_statement_menu_class','active')
@section('acc_details_menu_class','open')

@section('page_location')
	<ul class="breadcrumb">
		<li>
			<i class="ace-icon fa fa-home home-icon"></i>
			<a href="{{ route('reseller.index') }}">Dashboard</a>
		</li>
		<li class="active">Price Sms</li>
	</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
	<h1>
		Price & Coverage
		<small>
			<i class="ace-icon fa fa-angle-double-right"></i>
			Price List
		</small>
	</h1>
@endsection

@section('main_content')

<div class="space-6"></div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		
		<table id="transiation-history-table" class="table table-bordered" style="background: #fff;">
			<thead>
				<tr>
					<th>SL</th>
					<th>Company name</th>
					<th>Name</th>
					<th>Mobile No.</th>
					<th>Credit </th>
					<th>Debit </th>
					<th>Available balance</th>
					
				</tr>
			</thead>

			<tbody>
			@php($serial=1)
			@php($total_credit=0)
			@php($total_debit=0)
			@php($total_available_balance=0)
			@foreach($resellers as $reseller)
				@php($total_credit += BalanceHelper::user_total_credit($reseller->id))
				@php($total_debit += BalanceHelper::user_total_debit($reseller->id))
				@php($total_available_balance += BalanceHelper::user_available_balance($reseller->id))
				<tr>
					<td>{{ $serial++ }}</td>
					<td>{{ $reseller->company_name }}</td>
					<td>{{ $reseller->userDetail['name'] }}</td>
					<td>{{ $reseller->cellphone }}</td>
					<td class="text-right">{{ number_format(BalanceHelper::user_total_credit($reseller->id), 2) }}</td>
					<td class="text-right">{{ number_format(BalanceHelper::user_total_debit($reseller->id), 2) }}</td>
					<td class="text-right">{{ number_format(BalanceHelper::user_available_balance($reseller->id), 2) }}</td>
					{{--
					<td>
						<a href="{{ route('reseller.user.transactionHistory', $reseller->id) }}" class="btn btn-xs btn-primary">Details</a>
					</td>
					--}}
				</tr>
			@endforeach
			</tbody>
				<tfoot>
					<tr bgcolor="#dcc">
						<th class="text-right" colspan="4">Total Amount:</th>
						
						<th class="text-right">{{ number_format($total_credit,2) }}</th>
						<th class="text-right">{{ number_format($total_debit,2) }}</th>
						<th class="text-right">{{ number_format($total_available_balance,2) }}</th>
						
					</tr>
				</tfoot>

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
    $('#employee-list-table').DataTable();
    </script> --}}
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#transiation-history-table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 2 },
                   
            ]
        } );
    } );
</script>
@endsection

{{-- @section('custom_script')
	<script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
	<script type="text/javascript">
	$('#transiation-history-table').DataTable();
	</script>
@endsection --}}
