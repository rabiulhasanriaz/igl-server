<?php $__env->startSection('reports_menu_class','open'); ?>
<?php $__env->startSection('schedule_sms_menu_class','open'); ?>
<?php $__env->startSection('general_pending_sms_send_menu_class','active'); ?>
<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
	</li>
	<li class="active">Reports SMS</li>
</ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
<h1>
	Reports & Statistics
	<i class="ace-icon fa fa-angle-double-right"></i>
	Shedule SMS
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Pending SMS
	</small>
</h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>
	<?php echo $__env->make('user.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

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
				<form action="<?php echo e(route('user.sms.change_shedule_sms_time')); ?>" method="POST">
					<div class="modal-body">
						<p>Select Your desired date and time</p>
						<hr />
							<?php echo csrf_field(); ?>
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
			<?php ($serial=1); ?>
			<?php $__currentLoopData = $schedule_pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule_sms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($serial++); ?></td>
					<td title="<?php echo e($schedule_sms->sci_campaign_id); ?>"><?php echo e($schedule_sms->sci_campaign_title); ?></td>
					<td><?php echo e($schedule_sms->sci_targeted_time->format('H:i a, d-M-Y')); ?></td>
					<td><?php echo e($schedule_sms->sender->sir_sender_id); ?></td>
					<td><?php echo e($schedule_sms->sci_total_submitted); ?></td>
					<td><?php echo e($schedule_sms->sci_total_submitted); ?></td>
					<td>BDT <?php echo e(number_format($schedule_sms->sci_total_cost, 2)); ?></td>
					<td>
						<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#timeChangeModal" onclick="set_campaign_id('<?php echo e($schedule_sms->sci_campaign_id); ?>','<?php echo e($schedule_sms->sci_targeted_time); ?>')">
							Change Time
						</button>
					</td>
					<td>
						<?php if(OtherHelpers::schedule_sms_status($schedule_sms->id)=="Processing"): ?>
							<label class="text-primary">Processing</label>
						<?php elseif(OtherHelpers::schedule_sms_status($schedule_sms->id)=="AllSent"): ?>
							<label>
								<?php if($schedule_sms->sci_targeted_time > \Carbon\Carbon::now()->subHours(24)): ?>
									<a href="#my-modal" onclick="show_today_details('<?php echo e($schedule_sms->id); ?>')"
									   role="button" data-toggle="modal"
									   class="btn-none-edit CampaignId_one"> View </a>
									|
									<label>
										<a href="<?php echo e(route('user.reports.download_todays_report', $schedule_sms->id)); ?>"
										   target="_blank" class="btn-none-download"> Download </a>
									</label>
								<?php else: ?>
									<a href="#my-modal" onclick="show_archived_details('<?php echo e($schedule_sms->id); ?>')"
									   role="button" data-toggle="modal"
									   class="btn-none-edit CampaignId_one"> View </a>
									|
									<label>
										<a href="<?php echo e(route('user.reports.download_archived_report', $schedule_sms->id)); ?>"
										   target="_blank" class="btn-none-download"> Download </a>
									</label>
								<?php endif; ?>
							</label>

						<?php else: ?>
							<label class="text-danger">Something Wrong</label>
						<?php endif; ?>
					</td>

				</tr>

			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_style'); ?>
	<link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/bootstrap-datetimepicker.min.css"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
	<?php echo $__env->make('user.ajax.view_archived_report', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php echo $__env->make('user.ajax.view_todays_report', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

	<script src="<?php echo e(asset('assets')); ?>/js/moment.min.js"></script>
	<script src="<?php echo e(asset('assets')); ?>/js/bootstrap-datetimepicker.min.js"></script>
	<script src="<?php echo e(asset('assets')); ?>/js/bootstrap-datepicker.min.js"></script>
	<script>
		function set_campaign_id(campaign_id, target_time)
		{
			$("#target_time_field").val(target_time);
			console.log(target_time);
			$('#campaign_id_field').val(campaign_id);
		}
		$('.date-timepicker').datetimepicker();
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>