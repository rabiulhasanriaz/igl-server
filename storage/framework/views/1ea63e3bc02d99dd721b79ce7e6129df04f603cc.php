<?php $__env->startSection('load_menu_class','open'); ?>
<?php $__env->startSection('submenu_load','open'); ?>
<?php $__env->startSection('flexiload_make_menu_class', 'active'); ?>

<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
        </li>
        <li>
            <a href="<?php echo e(route('user.create')); ?>">Flexiload</a>
        </li>
        <li class="active">Load</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        Flexiload
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Load
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('admin.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <div class="center-block" style="width: 40%;">
                <form action="<?php echo e(route('user.flexiload.flexiloadForm')); ?>" method="post" onsubmit="return checkForm(this);">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="number_type">Number Type<span style="color: red">*</span></label>
                        <div class="form-group">
                            <label class="radio-inline"><input type="radio" value="1" name="number_type" required>Prepaid</label>
                            <label class="radio-inline"><input type="radio" value="2" name="number_type" required>PostPaid</label>
                            <label class="radio-inline"><input type="radio" value="3" name="number_type" required>Skitto</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="" for="targeted_number">Mobile Number<span style="color: red">*</span></label>
                        <input type="number" style="font-size: 20px;" id="targeted_number" name="targeted_number" class="form-control input-sm" required placeholder="01xxxxxxxxx">
                    </div>

                    <div class="form-group">
                            <label for="edit_operator">Operator </label>
                            <select name="operator" id="edit_operator" class="form-control">
                                <option value="">Select One</option>
                                <option value="gp">Grameen</option>
                                <option value="blink">Banglalink</option>
                                <option value="airtel">Airtel</option>
                                <option value="robi">Robi</option>
                                <option value="teletalk">Teletalk</option>
                                
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount (Tk) <span style="color: red">*</span></label>
                        <input type="number" style="font-size: 20px;" id="amount" name="amount" class="form-control" min="10" max="50000" required placeholder="00">
                    </div>

                    <div class="form-group">
                        <label class="" for="remarks">Remarks</label>
                        <input type="text" id="remarks" name="remarks" class="form-control input-sm" required placeholder="Short description">
                    </div>

                    <div class="form-group">
                        <label class="" for="flexipin">FlexiPin</label>
                        <input type="number" style="-webkit-text-security: disc;" id="flexipin" name="flexipin" class="form-control input-sm" required placeholder="flexipin">
                    </div>

                    <div class="form-group">
                        <label></label>
                        <input type="submit" class="btn btn-primary btn-sm" name="myButton" value="Submit">
                    </div>


                </form>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_style'); ?>
	<style>
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
		}

		/* Firefox */
		input[type=number] {
		-moz-appearance: textfield;
		}
	</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom_script'); ?>
<script type="text/javascript">
    $('form').submit(function() {
      $(this).find("input[type='submit']").prop('disabled',true);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>