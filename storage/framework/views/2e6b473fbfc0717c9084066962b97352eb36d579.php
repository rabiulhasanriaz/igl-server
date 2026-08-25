

<?php $__env->startSection('sender_id_menu_class','active'); ?>

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

            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th class="abcd">SL</th>
                    <th>SenderID</th>
                    <th> Requested On</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                <?php ($serial=1); ?>
                <?php $__currentLoopData = $senderIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $senderId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($serial++); ?></td>
                        <td><?php echo e($senderId->sender->sir_sender_id); ?></td>
                        <td><?php echo e($senderId->created_at->format('j-M-Y')); ?></td>
                        <td><?php echo e(($senderId->status==1)?'Active':'In-active'); ?></td>
                        <td>
                            <a href="<?php echo e(route('reseller.setDefaultSender', $senderId->id)); ?>"
                               onclick="return confirm('Are you sure you want to make default ?');"
                               class="btn btn-info <?php echo e(($senderId->sender_id==@$defaultSenderId->sender_id)?'btn-success disabled':'btn-primary'); ?> btn-sm">
                                Make default
                            </a>
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
        var table = $('#dynamic-table').DataTable( {
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