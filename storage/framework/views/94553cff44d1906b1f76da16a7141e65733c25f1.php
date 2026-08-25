

<?php $__env->startSection('reports_menu_class','open'); ?>
<?php $__env->startSection('schedule_sms_menu_class','open'); ?>
<?php $__env->startSection('campaign_sms_menu_class','active'); ?>
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
		Archived SMS
	</small>
</h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>

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
			</tr>
			</thead>

			<tbody>
			<?php ($serial=1); ?>
			<?php $__currentLoopData = $schedule_campaign_sms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule_sms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($serial++); ?></td>
					<td title="<?php echo e($schedule_sms->sci_campaign_id); ?>"><?php echo e($schedule_sms->sci_campaign_title); ?></td>
					<td><?php echo e($schedule_sms->sci_targeted_time->format('H:i a, d-M-Y')); ?></td>
					<td><?php echo e($schedule_sms->sender->sir_sender_id); ?></td>
					<td><?php echo e($schedule_sms->sci_total_submitted); ?></td>
					<td><?php echo e($schedule_sms->sci_total_submitted); ?></td>
					<td>BDT <?php echo e(number_format($schedule_sms->sci_total_cost, 2)); ?></td>
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


<?php $__env->startSection('custom_script'); ?>
	<?php echo $__env->make('user.ajax.view_archived_report', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php echo $__env->make('user.ajax.view_todays_report', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>