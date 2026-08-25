<?php $__env->startSection('add_fund_debit_menu_class','active'); ?>
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

<div class="row bg-container">
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
		
	</div>
	<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
		<?php echo $__env->make('reseller.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		<?php echo $__env->make('reseller.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

		<form action="<?php echo e(route('reseller.balance.debit.store')); ?>" method="post" class="form-horizontal" role="form">
			<?php echo csrf_field(); ?>
			<div class="form-group">
				<label for="form-field-select-3" style="font-size: 20px;">Company Name :</label>
				<br />
				<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Select User" name="user_id" required="" onchange="user_balance(this.value)">
					<option value="">  </option>
					<?php $__currentLoopData = $resellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reseller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<option value="<?php echo e($reseller->id); ?>"> <?php echo e($reseller->company_name); ?>- ( <?php echo e($reseller->cellphone); ?>

							)
						</option>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</select>
			</div>

			<div class="form-group">
				<label for="credit" style="font-size: 20px;">Debit amount :<span class="text-success" id="CustomerBalance"></span></label>
				<input type="text" name="debit_amount" id="credit" onkeyup="show_terget_time(this.value)" value="" class="form-control input-mask-numberTk" placeholder="00.00" maxlength="10" required style="font-size: 20px;">
			</div>

			<div class="form-group">
				<label for="payReference" style="font-size: 20px;">Payment Reference :</label>

				<input style="font-size: 20px;" type="text" name="payment_reference" id="payReference" value="" class="form-control" placeholder="Reference" maxlength="32" required>
			</div>

			<div class="clearfix form-group" id="submit_btn_debit">
				<input type="submit" class="btn btn-info" value="Submit" id="submitBtn" >
				&nbsp; &nbsp; &nbsp;
				<button class="btn btn-danger" type="reset">
					<i class="ace-icon fa fa-undo bigger-110"></i>
					Reset
				</button>
			</div>

		</form>
		
	</div>
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-left: 20px;">
		<div class="row" style="margin-left: 20px;">
			
				<div id="transaction-history">
					<!-- Transaction history will be displayed here -->
				</div>

		</div>
	</div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_style'); ?>
	<link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/chosen.min.css"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
	<script src="<?php echo e(asset('assets')); ?>/js/chosen.jquery.min.js"></script>
	<script type="text/javascript">
	
        $('.chosen-select').chosen({allow_single_deselect: true});

		// ===
		$('#form-field-select-3').on('change', function() {
			var userId = $(this).val();

			if (userId) {
				$.ajax({
					type: 'GET',
					url: "<?php echo e(route('reseller.transaction.history')); ?>",
					data: {
						userId: userId
					},
					success: function(data) {
						console.log('AJAX Response:', data);

						$('#transaction-history').html(data);
					}
				});
			} else {
				$('#transaction-history').empty();
			}
		});
		// ===

        function show_terget_time(value) {

            var max_ammount = $("#balanceOfCustomer").text();
            max_ammount = parseFloat(max_ammount);
            if(value<0){
                $("#submitBtn").hide();
            }
            else if(value>max_ammount){
                $("#submitBtn").hide();
            }
            else{
                $("#submitBtn").show();
            }
        }
	</script>
	<?php echo $__env->make('admin.ajax.check_customer_available_balance', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('reseller.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>