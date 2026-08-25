

<?php $__env->startSection('support_menu_class', 'open'); ?>
<?php $__env->startSection('support_ticket', 'active'); ?>

<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="">Dashboard</a>
        </li>
        <li>
            <a href="<?php echo e(route('user.support.tickets')); ?>">Support Center</a>
        </li>
        <?php echo $__env->yieldContent('support_breadcrumb'); ?>
    </ul>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_header'); ?>
    <h1>
        <?php echo $__env->yieldContent('support_title'); ?>
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            <?php echo $__env->yieldContent('support_subtitle'); ?>
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
    <div class="row">
        <div class="col-xs-12">
            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>
                        <i class="ace-icon fa fa-check"></i>
                        Success!
                    </strong>
                    <?php echo e(session('success')); ?>

                    <br>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>
                        <i class="ace-icon fa fa-times"></i>
                        Error!
                    </strong>
                    <?php echo e(session('error')); ?>

                    <br>
                </div>
            <?php endif; ?>

            <?php if(session('info')): ?>
                <div class="alert alert-info">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>
                        <i class="ace-icon fa fa-info-circle"></i>
                        Info!
                    </strong>
                    <?php echo e(session('info')); ?>

                    <br>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>
                        <i class="ace-icon fa fa-times"></i>
                        Please fix the following errors:
                    </strong>
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('support_content'); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>