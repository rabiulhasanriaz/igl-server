

<?php $__env->startSection('whitelisted_ip_menu_class','open'); ?>
<?php $__env->startSection('add_whitelisted_ip_menu_class', 'active'); ?>

<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.whitelistedIp.index')); ?>">Whitelisted IP</a>
        </li>
        <li class="active">Add Whitelisted IP</li>
    </ul>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_header'); ?>
    <h1>
        Whitelisted IP
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
        <div class="col-md-6 col-md-offset-3">
            <div class="widget-box">
                <div class="widget-header widget-header-blue widget-header-flat">
                    <h4 class="widget-title lighter">
                        <i class="ace-icon fa fa-shield"></i>
                        Add Whitelisted IP
                    </h4>
                </div>

                <div class="widget-body">
                    <div class="widget-main">
                        <form action="<?php echo e(route('admin.whitelistedIp.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            
                            <div class="form-group">
                                <label for="user_id">Select User <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-control select2" required>
                                    <option value="">-- Select User --</option>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>">
                                        <?php echo e($user->cellphone); ?> - <?php echo e($user->company_name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="white_listed_ip">Whitelisted IP(s)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="white_listed_ip" 
                                       name="white_listed_ip" 
                                       placeholder="e.g., 192.168.1.1 or 103.86.193.27,59.152.5.62">
                                <small class="text-muted">
                                    <i class="ace-icon fa fa-info-circle"></i>
                                    Single IP, multiple IPs (comma separated), CIDR (192.168.1.0/24), or wildcard (192.168.1.*)
                                </small>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ace-icon fa fa-save"></i> Save
                                </button>
                                <a href="<?php echo e(route('admin.whitelistedIp.index')); ?>" class="btn btn-default">
                                    <i class="ace-icon fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_style'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 34px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px;
    }
    .col-md-offset-3 {
        margin-left: 25%;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select a user",
            allowClear: true
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>