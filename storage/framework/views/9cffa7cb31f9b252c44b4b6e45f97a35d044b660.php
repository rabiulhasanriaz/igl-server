

<?php $__env->startSection('whitelisted_ip_menu_class','open'); ?>
<?php $__env->startSection('whitelisted_ip_list_menu_class', 'active'); ?>

<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
    </li>
    <li>
        <a href="">Users</a>
    </li>
    <li class="active">Whitelisted IP</li>
</ul>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_header'); ?>
<h1>
    Whitelisted IP
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        List
    </small>
</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
<div class="space-6"></div>

<?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding bg-container">
            <div class="clearfix">
                <div class="pull-right">
                    <a href="<?php echo e(route('admin.whitelistedIp.create')); ?>" class="btn btn-sm btn-success">
                        <i class="ace-icon fa fa-plus"></i>
                        Add New Whitelisted IP
                    </a>
                </div>
            </div>
            <hr>
            <h3>Whitelisted IP List</h3>
            <table class="table table-bordered table-responsive" id="whitelisted-ip-table">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Phone Number</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Whitelisted IP(s)</th>
                        <th>IP Count</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serial = 1; ?>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($serial++); ?></td>
                        <td><?php echo e($user->cellphone); ?></td>
                        <td><?php echo e($user->company_name); ?></td>
                        <td><?php echo e($user->email); ?></td>
                        <td>
                            <?php if($user->userDetail && $user->userDetail->white_listed_ip): ?>
                                <code style="font-size: 12px;"><?php echo e($user->userDetail->white_listed_ip); ?></code>
                            <?php else: ?>
                                <span style="color: #ff6600;">Not Configured</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($user->userDetail && $user->userDetail->white_listed_ip): ?>
                                <?php $ipCount = count(explode(',', $user->userDetail->white_listed_ip)); ?>
                                <span style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 12px; font-weight: bold;">
                                    <?php echo e($ipCount); ?>

                                </span>
                            <?php else: ?>
                                <span style="background-color: #ff9800; color: white; padding: 2px 8px; border-radius: 12px; font-weight: bold;">
                                    0
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($user->userDetail && $user->userDetail->white_listed_ip): ?>
                                <span style="background-color: #4CAF50; color: white; padding: 3px 10px; border-radius: 4px; font-size: 12px;">
                                    <i class="ace-icon fa fa-shield"></i> Restricted
                                </span>
                            <?php else: ?>
                                <span style="background-color: #f44336; color: white; padding: 3px 10px; border-radius: 4px; font-size: 12px;">
                                    <i class="ace-icon fa fa-globe"></i> Open Access
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="hidden-sm hidden-xs action-buttons">
                                <a class="red" onclick="return confirm('Are you sure to clear whitelisted IP for <?php echo e($user->name); ?>? This will allow all IPs to access API.')" href="<?php echo e(route('admin.whitelistedIp.delete', $user->id)); ?>" title="Clear IP">
                                    <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                </a>
                                <a class="green" href="<?php echo e(route('admin.whitelistedIp.edit', $user->id)); ?>" title="Edit IP">
                                    <i class="ace-icon fa fa-pencil bigger-130"></i>
                                </a>
                                <a class="blue" href="javascript:void(0)" onclick="checkIpStatus(<?php echo e($user->id); ?>)" title="Check IP Status">
                                    <i class="ace-icon glyphicon glyphicon-eye-open"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
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
code {
    background-color: #f4f4f4;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 11px;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#whitelisted-ip-table').DataTable({
        responsive: true,
        order: [[0, 'asc']]
    });
});

function checkIpStatus(userId) {
    $.ajax({
        url: '<?php echo e(route("admin.whitelistedIp.check_status", "")); ?>/' + userId,
        type: 'GET',
        success: function(response) {
            if(response.success) {
                var message = '';
                if(response.has_whitelist) {
                    message = '✓ Whitelisted IPs: ' + response.white_listed_ip + '\n';
                    message += '✓ Total IPs: ' + response.ip_count + '\n';
                    message += '✓ Only these IPs can access API';
                } else {
                    message = '⚠ No whitelisted IP configured!\n';
                    message += '⚠ API is accessible from ALL IP addresses\n';
                    message += '⚠ Please add whitelisted IP for security';
                }
                alert(message);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Something went wrong. Please try again.');
        }
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>