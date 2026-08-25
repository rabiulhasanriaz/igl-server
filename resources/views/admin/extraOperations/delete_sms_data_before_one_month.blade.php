@extends('admin.master')

@section('extra_operation_menu_class','open')
@section('delete_data_menu_class','active')

@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('admin.index') }}">Dashboard</a>
	</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Dashboard
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Delete Data
	</small>
</h1>
@endsection

@section('main_content')
	<div class="alert alert-danger alert-sm">
		<strong>Importants ! </strong>
		This operation will delete all sms information before 
			<span class="badge" style="font-weight: bold">
				<!-- {{ date('Y').' - '.(intval((date('m')))-1).' - '.date('d') }} -->
				{{ Carbon\Carbon::now()->subMonth(1)->format('Y - M - d - h : i : s a') }}
			</span> 

	</div>

	@if( session()->has('delete_info') )
		<div class="alert alert-info">
			<span>{{ session('delete_info') }}</span>
		</div>
	@endif

	@php
		$total_sms = App\Model\SmsCampaign::count();
		
		
		$has_to_delete_sms = App\Model\SmsCampaign::where('updated_at','<', Carbon\Carbon::now()->subMonth(1) )->count();
		

		$available_sms = $total_sms - $has_to_delete_sms;
		
	@endphp

	<div class="row">
		<div class="col-md-4 col-md-offset-4" style="border: 1px solid black; border-radius: 3px; padding: 20px;">
			<ul>
				<li>{{ $total_sms }} sms information is existing now </li>
				
				<li>{{ $has_to_delete_sms }} sms information will be deleted. </li>

				<li>After deleting {{ $available_sms }} sms information will be exist.</li>

				{{--<li>{{ $total_dynamic_sms }} Dynamic sms information is existing now </li>
				
				<li>{{ $has_to_delete_dynamic_sms }} Dynamic sms information will be deleted. </li>

				<li>After deleting {{ $available_dynamic_sms }} Dynamic sms information will be exist.</li>--}}
			</ul>
			
			<form method="POST" action="{{ route('admin.deleteDataBeforeOneMonth') }}">
			@csrf
				<input type="submit" type="button" class="btn btn-danger btn-sm pull-right" value="Delete" name="delete_data" onclick="return confirm('Are you sure want to delete ?');">
			</form>	
		</div>

		
	</div>


@endsection