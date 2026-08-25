<?php $__env->startSection('campaign_reshedule_class','active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Dynamic Permission Set</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <?php if(session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo e(session()->get('success')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
    <?php endif; ?>
    <?php if(session()->has('suspend')): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <?php echo e(session()->get('suspend')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
<?php endif; ?>
    <h1>
        API Permission
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Set
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('admin.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            
            <form action="<?php echo e(route('admin.flexiload.reload-load-all')); ?>" method="get">
                <?php echo csrf_field(); ?>
                
            <table class="table table-striped table-bordered table-hover" id="reseller_list">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>User name</th>
                    <th>Company Name</th>
                    <th>Cellphone</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                ($sl=0)
                ?>
                <?php $__currentLoopData = $api_user; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(++$sl); ?></td>
                    <td><?php echo e($user->userDetail->name); ?></td>
                    <td><?php echo e($user->company_name); ?></td>
                    <td><?php echo e($user->cellphone); ?></td>
                    <td><?php echo e($user->email); ?></td>
                    <td>
                        <?php if($user->userDetail->campaign_reschedule == 1): ?>
                        <style>
                            input[type=checkbox].ace.ace-switch.ace-switch-4:checked+.lbl::before, input[type=checkbox].ace.ace-switch.ace-switch-5:checked+.lbl::before {
                                background-color: #25af56;
                            }
                        </style>
                        
                        <?php else: ?>
                        <style>
                            input[type=checkbox].ace.ace-switch.ace-switch-4+.lbl::before, input[type=checkbox].ace.ace-switch.ace-switch-5+.lbl::before {
                                background-color: #e41d1d;
                                border: 1px solid #ce2b42;
                                
                            }
                        </style>
                        <?php endif; ?>
                        
                        
                        <input name="switch-field-1" id="contactID" <?php echo e(($user->userDetail->campaign_reschedule == 1)?'checked' : ''); ?> onchange="updateStatus('<?php echo e($user->id); ?>')" value="<?php echo e($user->id); ?>" class="ace ace-switch ace-switch-5" type="checkbox" />
                        <span class="lbl"></span>
                        
                        
                        
                        
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

        </div><!-- /.col -->
    </div><!-- /.row -->


<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#reseller_list').DataTable();
    </script>

<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});




 function updateStatus(statusValue){

   $.ajax({
    url:"<?php echo e(route('admin.campaign-reshedule-update')); ?>",
    method:"POST",
    data: {statusValue:statusValue},
    
    success:function(data)
    {
        
    }
   });
  
 }



    
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>