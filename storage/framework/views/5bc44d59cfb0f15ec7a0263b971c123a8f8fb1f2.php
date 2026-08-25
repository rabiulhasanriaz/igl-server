<?php $__env->startSection('load_menu_class','open'); ?>
<?php $__env->startSection('submenu_load_history','open'); ?>
<?php $__env->startSection('load_view_menu_class','active'); ?>

<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
	</li>
	<li class="active">Flexiload</li>
</ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
<h1>
	Flexiload
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		History
	</small>
</h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>

<div class="row">
	<?php echo $__env->make('user.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

	<!-- ------model view start-->
	<div id="my-modal" class="modal fade" tabindex="-1" style="display: none;">
	    <div class="modal-dialog" style="width: 80%;">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	                <h3 class="smaller lighter blue no-margin text-primary"> Flexiload Reports </h3>
	            </div>
	            <div class="modal-body">
	                <div id="SmsInformation"></div>
	            </div>
	        </div>
	    </div><!-- /.modal-dialog -->
	</div>
<div class="text-right" style="margin-bottom:10px;">
    <a href="<?php echo e(route('user.flexiload.downloadAllCurrentMonth')); ?>" 
       target="_blank" 
       class="btn btn-success">
        Download All
    </a>
</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<table id="dynamic-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th style="text-align: center;">SL</th>
					<th style="text-align: center;">Time</th>
					<th style="text-align: center;">Campaign ID</th>
					<th style="text-align: center;">Total Number</th>
					<th style="text-align: center;">Total Amount</th>
					<th style="text-align: center;">Action</th>
				</tr>
			</thead>

			<tbody>
				<?php
					{{ $total = 0; }}
				?>

			<?php $__currentLoopData = $loads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $load): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			
				<tr>
					<td style="text-align: center;"><?php echo e($loop->iteration); ?></td>
					<td style="text-align: center;"><?php echo e($load->created_at); ?></td>
					<td><?php echo e(($load->campaign_name != '')? $load->campaign_name:$load->campaign_id); ?></td>
					<td class="text-center"><span class="badge"><?php echo e($load->total_number); ?></span></td>
					<td class="text-right"><?php echo e($load->total_amount); ?></td>
					<td>
					    <label>
					        <a href="#my-modal" onclick="show_current_month_campaign_details('<?php echo e($load->campaign_id); ?>')"
					           role="button" data-toggle="modal"
					           class="btn-none-edit CampaignId_one"> View </a>
					    </label>
						|
					    <label>
					        <a href="<?php echo e(route('user.flexiload.downloadCurrentMonthFlexiReport', ['campaign_id'=>$load->campaign_id])); ?>" target="_blank" role="button" data-toggle="modal" class="btn-none-edit CampaignId_one"> Download </a>
					    </label>
					</td>
				</tr>
				<?php ( $total += $load->total_amount ); ?>
			
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>

			<tfoot>
				<tr>
					<th> </th>
					<th> </th>
					<th colspan="2" class="text-right">Total Flexiload Price of This Month : </th>
					<th class="text-right"><?php echo e($total." Tk"); ?></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div><!-- /.row -->

<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom_style'); ?>
<link href="<?php echo e(asset('assets/datatable/jquery.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
<link href="<?php echo e(asset('assets/datatable/rowReorder.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
<link href="<?php echo e(asset('assets/datatable/responsive.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
<style>
	@media(max-width:575px){
		.abcd{
			width: 130px;
		}
	}
	
	</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom_script'); ?>
	
	<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
        <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        var table = $('#dynamic-table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
            ]
        } );
    } );
    </script>
	<?php echo $__env->make('user.flexiload._ajax_campaign_history', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

	<script type="text/javascript">
		// $('#dynamic-table').DataTable();
	</script>

<?php $__env->stopSection(); ?>




<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>