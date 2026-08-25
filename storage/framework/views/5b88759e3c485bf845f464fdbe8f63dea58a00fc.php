<?php if(session()->has('message')): ?>
    <div class="alert alert-<?php echo e(session()->get('type')); ?>" id="report-alert">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <?php echo e(session()->get('message')); ?>

    </div>
<?php endif; ?>