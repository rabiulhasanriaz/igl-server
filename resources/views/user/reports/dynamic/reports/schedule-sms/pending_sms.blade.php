@extends('user.master')

@section('reports_menu_class','open')
@section('schedule_sms_menu_class','open')
@section('general_pending_sms_send_menu_class','active')
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
	Shedule SMS
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Pending SMS
	</small>
</h1>
@endsection


@section('main_content')
	@include('user.partials.session_messages')

	<!-- Date time change Modal -->
	<div class="modal fade" id="timeChangeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLongTitle">Change the Date / Time</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{{ route('user.sms.change_shedule_sms_time') }}" method="POST">
					<div class="modal-body">
						<p>Select Your desired date and time</p>
						<hr />
							@csrf
							<input type="hidden" value="" name="campaign_id" id="campaign_id_field">
							<input type="text" class="form-control date-timepicker" placeholder="Date time" name="new_date_time" id="target_time_field" autocomplete="off">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

		<table class="table table-striped table-bordered table-hover">
			<thead>
			<tr>
				<th>SL</th>
				<th>Campaign Title</th>
				<th class="hidden-600">Submit time</th>
				<th>SenderID</th>
				<th>Submitted</th>
				<th>Total sent</th>
				<th>Charge</th>
				<th>Action</th>
				<th>Status</th>
			</tr>
			</thead>

			<tbody>
			@php($serial=1)
			@foreach($schedule_pending as $schedule_sms)
				<tr>
					<td>{{ $serial++ }}</td>
					<td title="{{ $schedule_sms->sci_campaign_id }}">{{ $schedule_sms->sci_campaign_title }}</td>
					<td>{{ $schedule_sms->sci_targeted_time->format('H:i a, d-M-Y') }}</td>
					<td>{{ $schedule_sms->sender->sir_sender_id }}</td>
					<td>{{ $schedule_sms->sci_total_submitted }}</td>
					<td>{{ $schedule_sms->sci_total_submitted }}</td>
					<td>BDT {{ number_format($schedule_sms->sci_total_cost, 2) }}</td>
					<td>
						<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#timeChangeModal" onclick="set_campaign_id('{{ $schedule_sms->sci_campaign_id }}','{{ $schedule_sms->sci_targeted_time }}')">
							Change Time
						</button>
					</td>
					<td>
						@if(OtherHelpers::schedule_sms_status($schedule_sms->id)=="Processing")
							<label class="text-primary">Processing</label>
						@elseif(OtherHelpers::schedule_sms_status($schedule_sms->id)=="AllSent")
							<label>
								@if($schedule_sms->sci_targeted_time > \Carbon\Carbon::now()->subHours(24))
									<a href="#my-modal" onclick="show_today_details('{{$schedule_sms->id}}')"
									   role="button" data-toggle="modal"
									   class="btn-none-edit CampaignId_one"> View </a>
									|
									<label>
										<a href="{{ route('user.reports.download_todays_report', $schedule_sms->id) }}"
										   target="_blank" class="btn-none-download"> Download </a>
									</label>
								@else
									<a href="#my-modal" onclick="show_archived_details('{{$schedule_sms->id}}')"
									   role="button" data-toggle="modal"
									   class="btn-none-edit CampaignId_one"> View </a>
									|
									<label>
										<a href="{{ route('user.reports.download_archived_report', $schedule_sms->id) }}"
										   target="_blank" class="btn-none-download"> Download </a>
									</label>
								@endif
							</label>

						@else
							<label class="text-danger">Something Wrong</label>
						@endif
					</td>

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

@section('custom_style')
	<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datetimepicker.min.css"/>
@endsection

@section('custom_script')
	@include('user.ajax.view_archived_report')
	@include('user.ajax.view_todays_report')

	<script src="{{ asset('assets') }}/js/moment.min.js"></script>
	<script src="{{ asset('assets') }}/js/bootstrap-datetimepicker.min.js"></script>
	<script src="{{ asset('assets') }}/js/bootstrap-datepicker.min.js"></script>
	<script>
		function set_campaign_id(campaign_id, target_time)
		{
			$("#target_time_field").val(target_time);
			console.log(target_time);
			$('#campaign_id_field').val(campaign_id);
		}
		$('.date-timepicker').datetimepicker();
	</script>
@endsection
