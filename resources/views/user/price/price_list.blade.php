@extends('user.master')

@section('price_and_converage_menu_class','open')
@section('price_menu_class','active')
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
					<th class="ijk" rowspan="2">SL</th>
					<th rowspan="2">Country</th>
					<th class="oper" rowspan="2">Operator</th>
					<th class="abcd" rowspan="2">Mas king</th>
					<th colspan="2" style="text-align: center;">Non-Masking</th>
					<th rowspan="2">Status</th>
				</tr>
				<tr>
					<th class="defg">MNO</th>
					<th class="defg">IPTSP</th>
				</tr>
			</thead>

			<tbody>
			@php($serial=1)
			@foreach($smsRates as $smsRate)
				<tr>
					<td>{{ $serial++ }}</td>
					<td>{{ $smsRate->country->country_name }}</td>
					<td>{{ $smsRate->operator->ope_operator_name }}</td>
					<td>{{ $smsRate->asr_masking }}</td>
					<td>{{ $smsRate->asr_nonmasking }}</td>
					<td>{{ $smsRate->asr_nonmasking_iptsp }}</td>
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
        
        /* Styling for the multi-row header */
        table thead tr th {
            vertical-align: middle;
            text-align: center;
        }
        
        table thead tr:first-child th {
            border-bottom: 1px solid #ddd;
        }
        
        /* Different background for MNO and IPTSP columns for distinction */
        th:nth-child(5),
        td:nth-child(5) {
            background-color: #f9f9f9;
        }
        
        th:nth-child(6),
        td:nth-child(6) {
            background-color: #f0f8ff;
        }
        
        /* For mobile responsiveness */
        @media screen and (max-width: 767px) {
            table th[rowspan="2"],
            table td {
                font-size: 0.9em;
                padding: 4px;
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
	$(document).ready(function() {
		var table = $('#price_list').DataTable( {
			responsive: true,
			columnDefs: [
				{ responsivePriority: 1, targets: 0 },
				{ responsivePriority: 2, targets: 2 },
				{ responsivePriority: 3, targets: 3 },
				{ responsivePriority: 4, targets: 4 },
				{ responsivePriority: 5, targets: 5 },
				{ responsivePriority: 6, targets: 1 },
				{ responsivePriority: 7, targets: 6 },
			]
		} );
	});
</script>
@endsection
