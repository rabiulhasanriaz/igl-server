@extends('user.master')

@section('dynamic_price_and_converage_menu_class','open')
@section('dynamic_price_menu_class','active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('user.index') }}">Dashboard</a>
	</li>
	<li class="active">Price &amp; Coverage</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Price
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Price List
	</small>
</h1>
@endsection


@section('main_content')

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<table class="table table-striped table-bordered table-hover" id="price_list">
			<thead>
				<tr>
					<th class="ijk">SL</th>
					<th>Country</th>
					<th class="oper">Operator</th>
					<th class="abcd">Dynamic </th>
					<th>Status</th>
				</tr>
			</thead>

			<tbody>
            @php
            ($serial=1)
            @endphp
			@foreach($smsRates as $smsRate)
				<tr>
					<td>{{ $serial++ }}</td>
					<td>{{ $smsRate->country->country_name }}</td>
					<td>{{ $smsRate->operator->ope_operator_name }}</td>
					<td>{{ $smsRate->asr_dynamic }}</td>
					<td>Approved</td>
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
                width: 50px;
            }
			.defg{
                width: 30px;
            }
			.ijk{
                width: 10px;
            }
			.oper{
                width: 20px;
            }
        }
        
        </style>
@endsection
@section('custom_script')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
	// $('#reseller_list').DataTable();
	$(document).ready(function() {
	var table = $('#price_list').DataTable( {
		responsive: true,
		columnDefs: [
				{ responsivePriority: 1, targets: 0 },
				{ responsivePriority: 2, targets: 2 },
				{ responsivePriority: 3, targets: 3 },
				{ responsivePriority: 4, targets: 4 },
				{ responsivePriority: 5, targets: 1 },
				{ responsivePriority: 6, targets: 5 },
		]
	} );
} );
</script>
@endsection



