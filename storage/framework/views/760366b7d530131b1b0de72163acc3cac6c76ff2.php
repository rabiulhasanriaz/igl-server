<table id="todays-sms-report-details" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>Sl</th>
        <th>Mobile</th>
        <th>Targeted time</th>
        <th>Sender ID</th>
        <th>Operator</th>
        <th class="hidden-480">Charge/SMS</th>
        <th class="hidden-480">SMS Text</th>
        <th class="hidden-480">Status Report</th>
    </tr>
    </thead>

    <tbody>
    <?php ($serial=1); ?>
    <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e(@$serial++); ?></td>
            <td><?php echo e(@$report->sct_cell_no); ?></td>
            <td><?php echo e(@$report->sct_target_time->format('Y-M-d H:sa')); ?></td>
            <td><?php echo e(@$report->sender->sir_sender_id); ?></td>
            <td><?php echo e(@$report->operator->ope_operator_name); ?></td>
            <td class="hidden-480"><?php echo e(@$report->sct_sms_cost); ?></td>
            <td class="hidden-480"><pre style="width: 340px; line-height: 15px;"><?php echo @$report->sct_message; ?></pre></td>
            <?php if(@$report->sct_delivery_report=="DELIVERED"): ?>
                <td class="hidden-480 text-success"><?php echo e(@$report->sct_delivery_report); ?></td>
            <?php else: ?>
                <td class="text-danger hidden-480"><?php echo e(@$report->sct_delivery_report); ?></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>



    <script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#todays-sms-report-details').DataTable();
    </script>



