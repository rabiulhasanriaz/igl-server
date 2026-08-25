

<?php $__env->startSection('reports_menu_class','open'); ?>
<?php $__env->startSection('campaign_dlr_menu_class','open'); ?>
<?php $__env->startSection('todays_campaign_menu_class','active'); ?>
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
	Campaign DLR
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Today's Campaign
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
				<th>Submit time</th>
				<th>SenderID</th>
				<th>Submitted</th>
				<th>Total sent</th>
				<th>Charge</th>
				<th>Action</th>
			</tr>
			</thead>

			<tbody>
			<?php ($serial=1); ?>
			<?php $__currentLoopData = $todays_campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $todays_campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($serial++); ?></td>
					<td title="<?php echo e($todays_campaign->sci_campaign_id); ?>"><?php echo e($todays_campaign->sci_campaign_title); ?></td>
					<td></td>

					<td><?php echo e($todays_campaign->sci_campaign_id); ?></td>
					<td><?php echo e($todays_campaign->sci_targeted_time->format('H:i a, d-M-Y')); ?></td>
					<td><?php echo e($todays_campaign->sender->sir_sender_id); ?></td>
					<td><?php echo e($todays_campaign->sci_total_submitted); ?></td>
					<td><?php echo e($todays_campaign->sci_total_submitted); ?></td>
					<td>BDT <?php echo e(number_format($todays_campaign->sci_total_cost, 2)); ?></td>
					<td>
						<label>
							<a href="#my-modal" onclick="show_today_details('<?php echo e($todays_campaign->id); ?>')"
							   role="button" data-toggle="modal"
							   class="btn-none-edit CampaignId_one"> View </a>
						</label>
						|
						<label>
							<a href="<?php echo e(route('user.reports.download_todays_report', $todays_campaign->id)); ?>" target="_blank" class="btn-none-download"> Download </a>
						</label>
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
	<?php echo $__env->make('user.ajax.view_todays_report', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>