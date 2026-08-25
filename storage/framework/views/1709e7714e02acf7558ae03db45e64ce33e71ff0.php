<?php $__env->startSection('virtual_number_menu_class','open'); ?>
<?php $__env->startSection('add_virtual_number_menu_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.senderID.index')); ?>">Sender ID</a>
        </li>
        <li class="active">Virtual Number</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        Virtual Number
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Add
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>

    <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">

                <form action="<?php echo e(route('admin.virtualNumber.store')); ?>" method="post" class="form-horizontal"
                      role="form">

                    <?php echo csrf_field(); ?>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-group">
                            <label for="form-field-select-3"> Operator name </label>
                            <br/>
                            <select class="chosen-select form-control" id="form-field-select-3"
                                    data-placeholder="Operator name.." name="operator_id" required="">
                                <option value=""></option>
                                <?php $__currentLoopData = $operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($operator->id); ?>"> <?php echo e($operator->ope_operator_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($errors->has('operator_id')): ?>
                                <span class="text-danger"><?php echo e($errors->first('operator_id')); ?></span>
                            <?php endif; ?>
                        </div>


                        <div class="form-group">
                            <label for="form-field-select-3">Virtual number</label>

                            <input type="text" name="virtual_number" value="<?php echo e(old('virtual_number')); ?>"
                                   class="form-control"
                                   placeholder="Virtual number" maxlength="100" required="">
                            <?php if($errors->has('virtual_number')): ?>
                                <span class="text-danger"><?php echo e($errors->first('virtual_number')); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3">Virtual number name</label>

                            <input type="text" name="virtual_number_name" value="<?php echo e(old('virtual_number_name')); ?>"
                                   class="form-control"
                                   placeholder="Virtual number name" maxlength="100" required="">
                            <?php if($errors->has('virtual_number_name')): ?>
                                <span class="text-danger"><?php echo e($errors->first('virtual_number_name')); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3">API User name</label>

                            <input type="text" name="api_username" value="<?php echo e(old('api_username')); ?>"
                                   class="form-control" placeholder="Api user name"
                                   maxlength="100" required="">
                            <?php if($errors->has('api_username')): ?>
                                <span class="text-danger"><?php echo e($errors->first('api_username')); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3">API Password</label>

                            <input type="text" name="api_password" value="<?php echo e(old('api_password')); ?>"
                                   class="form-control" placeholder="Api password"
                                   maxlength="100" required="">
                            <?php if($errors->has('api_password')): ?>
                                <span class="text-danger"><?php echo e($errors->first('api_password')); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3">Auto Load Amount</label>
                            <input type="text" name="auto_load_amount" value="0"
                                   class="form-control" placeholder="Enter Autometic load amount" required>
                            <?php if($errors->has('auto_load_amount')): ?>
                                <span class="text-danger"><?php echo e($errors->first('auto_load_amount')); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="clearfix form-group">

                            <input type="submit" class="btn btn-info" value="Submit">
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-danger" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                        </div>
                    </div>

                </form>
            </div><!-- end bg-container-->
        </div>
    </div><!-- /.row -->


<?php $__env->stopSection(); ?>




<?php $__env->startSection('custom_style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/chosen.min.css"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect: true});
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>