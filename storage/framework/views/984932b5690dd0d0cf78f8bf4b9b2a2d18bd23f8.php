<?php if(session()->has('message')): ?>
    <div class="alert alert-<?php echo e(session()->get('type')); ?>" id="report-alert">
        <button type="button" class="close" data-dismiss="alert"><span style="font-size: 20px;">x</span></button>
        <?php echo e(session()->get('message')); ?>

    </div>
<?php endif; ?>