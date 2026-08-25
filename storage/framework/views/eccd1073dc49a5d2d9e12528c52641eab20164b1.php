<?php $__env->startSection('user_list_menu_class','active'); ?>
<?php $__env->startSection('user_menu_class','open'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('reseller.index')); ?>">Dashboard</a>
        </li>
        <li class="active">User</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        User
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            List
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
    <div class="row">
        <div class="col-sm-12 table">

            <?php echo $__env->make('reseller.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('reseller.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <table id="user-list-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th style="width: 20px;">SL</th>
                    <th class="abcd">Company name</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th class="">Phone</th>
                    <th class="">Customar</th>
                    <th>System</th>
                </tr>
                </thead>
                <tbody>
                <?php ($serial=1); ?>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($serial++); ?></td>
                        <td><?php echo e($user->company_name); ?></td>
                        <td><?php echo e($user->userDetail['name']); ?></td>
                        <td><?php echo e($user->email); ?></td>
                        <td class=""><a href="tel:<?php echo e($user->cellphone); ?>"><?php echo e($user->cellphone); ?></a></td>
                        <td class="">
                            <p style='color:green;'>
                                <?php if($user->role==4): ?>
                                    Reseller
                                <?php else: ?>
                                    User
                                <?php endif; ?>
                            </p>
                        </td>
                        <td>
                            <div class="widget-toolbar no-border">
                                <button class="btn btn-xs bigger btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                    Active
                                    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-primary dropdown-menu-right dropdown-caret dropdown-close">
                                    <li>
                                        <a href="<?php echo e(route('reseller.user.priceView', $user->id)); ?>"
                                           class="tooltip-error"
                                           data-rel="tooltip" title="Price rate">
                                            <span class="label label-sm" style="background: green;padding: 3px;">  Price View </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(route('reseller.user.transactionHistory', $user->id)); ?>"
                                           class="tooltip-error" data-rel="tooltip" title="Account Details">
                                            <span class="label label-sm label-primary">Account</span>
                                        </a>
                                    </li>
                                    <li>
                                        <?php if($user->status==1): ?>
                                            <a href="<?php echo e(route('reseller.user.suspend', $user->id)); ?>" class="tooltip-error"
                                               data-rel="tooltip" title="Conform"
                                               onclick="return confirm('Are you sure ?');">
                                                <span class="label label-sm label-warning">Suspend</span>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('reseller.user.active', $user->id)); ?>" class="tooltip-success"
                                               data-rel="tooltip" title="Conform"
                                               onclick="return confirm('Are you sure ?');">
                                                <span class="label label-sm label-success">Re-Active</span>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a class="green" href="<?php echo e(route('reseller.user.edit', $user->id)); ?>"
                                           class="tooltip-error" data-rel="tooltip" title="Customer Edit">
                                            <span class="label label-sm btn-success"
                                                  style="padding: 3px;">Customer Edit</span>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="<?php echo e(route('reseller.user.goToThisAccount', $user->id)); ?>"
                                           class="tooltip-error" data-rel="tooltip" title="Account Details">
                                            <span class="label label-sm label-primary">Go to this account</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
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
    
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#user-list-table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
                    { responsivePriority: 6, targets: 5 },
                    { responsivePriority: 7, targets: 6 },
            ]
        } );
    } );
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('reseller.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>