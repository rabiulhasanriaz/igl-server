

<?php $__env->startSection('user_statement_menu_class','active'); ?>
<?php $__env->startSection('acc_details_menu_class','open'); ?>

<?php $__env->startSection('page_location'); ?>
	<ul class="breadcrumb">
		<li>
			<i class="ace-icon fa fa-home home-icon"></i>
			<a href="<?php echo e(route('reseller.index')); ?>">Dashboard</a>
		</li>
		<li class="active">Price Sms</li>
	</ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
	<h1>
		Price & Coverage
		<small>
			<i class="ace-icon fa fa-angle-double-right"></i>
			Price List
		</small>
	</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

<div class="space-6"></div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		
		<table id="transiation-history-table" class="table table-bordered" style="background: #fff;">
			<thead>
				<tr>
					<th>SL</th>
					<th>Company name</th>
					<th>Name</th>
					<th>Mobile No.</th>
					<th>Credit </th>
					<th>Debit </th>
					<th>Available balance</th>
					
				</tr>
			</thead>

			<tbody>
			<?php ($serial=1); ?>
			<?php ($total_credit=0); ?>
			<?php ($total_debit=0); ?>
			<?php ($total_available_balance=0); ?>
			<?php $__currentLoopData = $resellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reseller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php ($total_credit += BalanceHelper::user_total_credit($reseller->id)); ?>
				<?php ($total_debit += BalanceHelper::user_total_debit($reseller->id)); ?>
				<?php ($total_available_balance += BalanceHelper::user_available_balance($reseller->id)); ?>
				<tr>
					<td><?php echo e($serial++); ?></td>
					<td><?php echo e($reseller->company_name); ?></td>
					<td><?php echo e($reseller->userDetail['name']); ?></td>
					<td><?php echo e($reseller->cellphone); ?></td>
					<td class="text-right"><?php echo e(number_format(BalanceHelper::user_total_credit($reseller->id), 2)); ?></td>
					<td class="text-right"><?php echo e(number_format(BalanceHelper::user_total_debit($reseller->id), 2)); ?></td>
					<td class="text-right"><?php echo e(number_format(BalanceHelper::user_available_balance($reseller->id), 2)); ?></td>
					
				</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
				<tfoot>
					<tr bgcolor="#dcc">
						<th class="text-right" colspan="4">Total Amount:</th>
						
						<th class="text-right"><?php echo e(number_format($total_credit,2)); ?></th>
						<th class="text-right"><?php echo e(number_format($total_debit,2)); ?></th>
						<th class="text-right"><?php echo e(number_format($total_available_balance,2)); ?></th>
						
					</tr>
				</tfoot>

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
    
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#transiation-history-table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 2 },
                   
            ]
        } );
    } );
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('reseller.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>