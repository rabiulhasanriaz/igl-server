<?php $__env->startSection('send_sms_to_all_users', 'open'); ?>

<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="<?php echo e(route('reseller.index')); ?>">Dashboard</a>
    </li>
    
</ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
<h1>
    Dashboard
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Send sms to all user and resellers
    </small>
</h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>
<div class="row">
    <div class="col-sm-6">
        <?php echo $__env->make('reseller.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <div id="send-sms-content">
            <form action="<?php echo e(route('reseller.sendSmsToAll')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <label>Create / Paste your Message here</label><br />
                <textarea class="count_me form-control" name="message" id="message" required=""
                          style="min-height: 120px;"></textarea>


                <div class="block">
                    <div class=""><span>CHECK YOUR SMS COUNT</span><span style="color: red;"> ( English : 160 character, bangla sms(max character=315))</span></div>
                    <div class="">
                        <div style=""><span class="charleft contacts-count">&nbsp;</span><span
                                    class="parts-count"></span></div>
                    </div>
                </div>
                
                <input type="submit" type="button" class="btn btn-success" name="" value="Send">
            
        </div>
    </div><!-- /.col -->

    <div class="col-sm-3">
        <br />
        <div style="border: 1px solid gray; background: rgba(255,255,255, 0.1); padding: 10px;">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="reseller_user" id="reseller" value="<?php echo e($numbers_reseller); ?>">
                <label class="form-check-label" for="reseller">
                    Reseller (<?php echo e($total_reseller); ?>)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="reseller_user" id="user" value="<?php echo e($numbers_user); ?>">
                <label class="form-check-label" for="user">
                    User (<?php echo e($total_user); ?>)
                </label>
            </div> 
            <div class="form-check">
                <input class="form-check-input" type="radio" name="reseller_user" id="total" value="<?php echo e($numbers_total); ?>">
                <label class="form-check-label" for="total">
                    Total (<?php echo e($total_reseller + $total_user); ?>)
                </label>
            </div>
            With this operation , you will be able to send a message to all of you Clients.
        </div>
    </div>
</form>

</div><!-- /.row -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/chosen.min.css"/>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_script'); ?>
<?php echo $__env->make('reseller.ajax.employee', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    
    <script src="<?php echo e(asset('assets')); ?>/js/chosen.jquery.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/jquery.textareaCounter.plugin.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/text-area-counter.js"></script>

    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect: true});
        $(document).ready(function () {
            count_textarea('#send-sms-content');
        });
    </script>

    

<?php $__env->stopSection(); ?>
<?php echo $__env->make('reseller.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>