

<?php $__env->startSection('sender_id_menu_class','open'); ?>
<?php $__env->startSection('add_sender_id_menu_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
	</li>
	<li class="active">Sender ID</li>
</ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
<h1>
	Sender ID
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		 Add
	</small>
</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

<div class="space-6"></div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">

		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
			<!-- PAGE CONTENT BEGINS -->
			<?php echo $__env->make('reseller.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			<?php echo $__env->make('reseller.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			<form action="<?php echo e(route('admin.senderID.create')); ?>" method="post" class="form-horizontal" role="form">
				<?php echo csrf_field(); ?>
				<div class="form-group">
					<label for="non_masking"> Select non-masking  </label>
					<br />
					<select class="chosen-select form-control" id="non_masking" data-placeholder="Non-masking">
						<option value=""> </option>
                        <?php $__currentLoopData = $nonMaskingSenderIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nonMaskingSenderId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($nonMaskingSenderId->number); ?>"><?php echo e($nonMaskingSenderId->number); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

					</select>
				</div> 

				<div class="form-group">
					<label for="viewNonMasking">Add Sender ID</label>
					<input type="text" name="sender_id" value="<?php echo e(old('sender_id')); ?>" class="form-control" placeholder="Sender ID" maxlength="15" id="viewNonMasking" required>
                    <span class="duplicate-senderId text-danger"></span>
                    <span class="valid-senderId text-success"></span>
                    <?php if($errors->has('sender_id')): ?>
                        <span class="text-danger retErrSenderId"><?php echo e($errors->first('sender_id')); ?></span>
                    <?php endif; ?>
				</div>
				<div class="form-group">
					<label for=""> Virtual number Robi  </label>
					<br />
					<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Robi" name="robi_virtual_number" >
						<option value="">  </option>
						<?php $__currentLoopData = $robiVirtualNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $robiVirtualNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<option value="<?php echo e($robiVirtualNumber->id); ?>"><?php echo e($robiVirtualNumber->sivn_number); ?></option>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>
				</div>
				
				
				<div class="form-group">
					<label for=""> Virtual number Teletalk  </label>
					<br />

					
					<select class="chosen-select col-sm-4" id="form-field-select-3" data-placeholder="Teletalk" name="teletalk_virtual_number">
						<option value="">  </option>
                        <?php $__currentLoopData = $teletalkVirtualNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teletalkVirtualNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($teletalkVirtualNumber->id); ?>"><?php echo e($teletalkVirtualNumber->sivn_number); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>

					<input type="" class="col-sm-4  pull-right" name="teletalk_user_name" placeholder="User Name">

					<input type="" class="col-sm-4  pull-right" name="teletalk_user_password" placeholder="Password">
					

				</div>
				
				<div class="form-group">
					<label for=""> Virtual number Grameen  </label>
					<br />
					<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Grameen" name="gp_virtual_number" >
						<option value="">  </option>
							<?php $__currentLoopData = $gpVirtualNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gpVirtualNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($gpVirtualNumber->id); ?>"><?php echo e($gpVirtualNumber->sivn_number); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>
				</div>
				
				<div class="form-group">
					<label for=""> Virtual number Airtel   </label>
					<br />
					<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Airtel " name="airtel_virtual_number" >
						<option value="">  </option>
                        <?php $__currentLoopData = $airtelVirtualNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airtelVirtualNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($airtelVirtualNumber->id); ?>"><?php echo e($airtelVirtualNumber->sivn_number); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>
				</div>
				
				<div class="form-group">
					<label for=""> Virtual number BanglaLink  </label>
					<br />
					<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="BanglaLink" name="bl_virtual_number" >
						<option value="">  </option>
                        <?php $__currentLoopData = $blVirtualNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blVirtualNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($blVirtualNumber->id); ?>"><?php echo e($blVirtualNumber->sivn_number); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>
				</div>


				<div class="clearfix form-group" id = "create_btn">
				
					<input type="submit" class="btn btn-info" value="Submit">
					&nbsp; &nbsp; &nbsp;
					<button class="btn btn-danger" type="reset">
						<i class="ace-icon fa fa-undo bigger-110"></i>
						Reset
					</button>
																
				</div>

			</form>
		</div>
		
		
	</div><!-- /.col -->
</div><!-- /.row -->


<?php $__env->stopSection(); ?>




<?php $__env->startSection('custom_style'); ?>
	<link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/chosen.min.css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
	<script src="<?php echo e(asset('assets')); ?>/js/chosen.jquery.min.js"></script>
	<script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect:true});
        $('#non_masking').change(function () {
            var nonMaskingValue = $('#non_masking').val();
            $('#viewNonMasking').val(nonMaskingValue);
            checkSenderIdExistence(nonMaskingValue);
        });
        $('#viewNonMasking').bind("keyup", function() {
            var nonMaskingVal = $('#viewNonMasking').val();
            checkSenderIdExistence(nonMaskingVal);
        });
	</script>
	<?php echo $__env->make('admin.ajax.check_sender_id_existence', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>