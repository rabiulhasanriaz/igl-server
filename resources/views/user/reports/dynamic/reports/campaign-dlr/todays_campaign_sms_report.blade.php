@extends('user.master')

@section('dynamic_reports_menu_class','open')
@section('dynamic_campaign_dlr_menu_class','open')
@section('dynamic_todays_campaign_menu_class','active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('user.index') }}">Dashboard</a>
	</li>
	<li class="active">Reports SMS</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Reports & Statistics
	<i class="ace-icon fa fa-angle-double-right"></i>
	Campaign DLR
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Today's Campaign
	</small>
</h1>
@endsection


@section('main_content')

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

		<table class="table table-striped table-bordered table-hover">
			<thead>
			<tr>
				<th>SL</th>
				<th>Campaign Title</th>
				<th>Submit time</th>
				<th>Submitted</th>
				<th>Total sent</th>
				<th>Charge</th>
				{{-- <th>Action</th> --}}
			</tr>
			</thead>

			<tbody>
			@php($serial=1)
			@foreach($todays_campaigns as $todays_campaign)
				<tr>
					<td>{{ $serial++ }}</td>
					<td title="{{ $todays_campaign->sdci_campaign_id }}">{{ $todays_campaign->sdci_campaign_title }}</td>
	

					
					<td>{{ $todays_campaign->sdci_targeted_time }}</td>
					<td>{{ $todays_campaign->sdci_total_submitted }}</td>
					<td>{{ $todays_campaign->sdci_total_submitted }}</td>
					<td class="text-right">BDT {{ number_format($todays_campaign->sdci_total_cost, 2) }}</td>
					{{-- <td>
						<label>
							<a href="#my-modal" onclick="show_today_details('{{$todays_campaign->id}}')"
							   role="button" data-toggle="modal"
							   class="btn-none-edit CampaignId_one"> View </a>
						</label>
						|
						<label>
							<a href="{{ route('user.reports.download_todays_report', $todays_campaign->id) }}" target="_blank" class="btn-none-download"> Download </a>
						</label>
					</td> --}}
				</tr>
			@endforeach
			</tbody>
		</table>
			
		<!-- ------model view start-->
		<div id="my-modal" class="modal fade" tabindex="-1" style="display: none;">
			<div class="modal-dialog" style="width: 80%;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h3 class="smaller lighter blue no-margin text-primary"> Today Report Details</h3>
					</div>
					<div class="modal-body">
						<div id="SmsInformation"></div>
					</div>
				</div>
			</div><!-- /.modal-dialog -->
		</div>	

	</div><!-- /.col -->
</div><!-- /.row -->

@endsection

@section('custom_script')
	@include('user.ajax.view_todays_report')
@endsection

