@extends('user.master')

@section('load_menu_class','open')
@section('submenu_load_history','open')
@section('load_view_menu_class','active')

@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('user.index') }}">Dashboard</a>
	</li>
	<li class="active">Flexiload</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Flexiload
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		History
	</small>
</h1>
@endsection


@section('main_content')

<div class="row">
	@include('user.partials.session_messages')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<table id="dynamic-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>SL</th>
					<th>Campaign ID</th>
					<th>Targeted Number</th>
					<th>Package Name</th>
					<th>Total Price</th>
					<th>Time</th>
				</tr>
			</thead>

			<tbody>
			@php($serial=1)
			@foreach($loads as $load)
				<tr>
					<td>{{ $serial++ }}</td>
					<td>{{ $load->campaign_id }}</td>
					<td>{{ $load->targeted_number }}</td>
					<td>
						@if($load->package_id == 0)
							Single Load
						@else
							{{ $load->package_info['package_name'] }}
						@endif

					</td>
					<td>{{ $load->campaign_price }}</td>
					<td>{{ $load->created_at }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div><!-- /.row -->

@endsection

@section('custom_script')
	<script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>

	<script type="text/javascript">
		$('#dynamic-table').DataTable();
	</script>
@endsection



