<?php $__env->startSection('sender_id_menu_class','open'); ?>
<?php $__env->startSection('delivery_sender_id_menu_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
	</li>
	<li class="active">Delivery Sender ID</li>
</ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
<h1>
	Delivery Sender ID
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		 List
	</small>
</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

<div class="space-6"></div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

		<table id="delivery-sender-id-list-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Sender id</th>
					<th>Teletalk</th>
					<th>Robi </th>
					<th>Grameen</th>
					<th>Airtel </th>
					<th>BanglaLink</th>
					<th>System</th>
				</tr>
			</thead>

			<tbody>
			<?php ($serial=1); ?>
			<?php $__currentLoopData = $senderIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $senderId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($serial++); ?></td>
					<td><?php echo e($senderId->sir_sender_id); ?></td>

					<?php if($senderId->sir_teletalk_confirmation==1): ?>
						<td class="text-success">Active</td>
					<?php else: ?>
						<td class="text-danger">Inactive</td>
					<?php endif; ?>

					<?php if($senderId->sir_robi_confirmation==1): ?>
						<td class="text-success">Active</td>
					<?php else: ?>
						<td class='text-danger'> Inactive</td>
					<?php endif; ?>

					<?php if($senderId->sir_gp_confirmation==1): ?>
						<td class="text-success">Active</td>
					<?php else: ?>
						<td class='text-danger'>Inactive</td>
					<?php endif; ?>

					<?php if($senderId->sir_airtel_confirmation==1): ?>
						<td class="text-success">Active</td>
					<?php else: ?>
						<td class='text-danger'> Inactive</td>
					<?php endif; ?>

					<?php if($senderId->sir_banglalink_confirmation==1): ?>
						<td class="text-success">Active</td>
					<?php else: ?>
						<td class='text-danger'>Inactive</td>
					<?php endif; ?>

					<td><a href="<?php echo e(route('admin.senderID.checkDeliverySenderID', $senderId->id)); ?>"><button class="btn btn-xs btn-primary">Check</button></a></td>
				</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


			</tbody>
		</table>
		
		
	</div><!-- /.col -->
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
	
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#delivery-sender-id-list-table').DataTable( {
            rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
            
        } );
        
    } );
    </script>

<?php $__env->stopSection(); ?>






<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>