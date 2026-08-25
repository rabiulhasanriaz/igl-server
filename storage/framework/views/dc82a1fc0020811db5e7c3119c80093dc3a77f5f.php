<table id="api_archived_report" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th>SL</th>
            <th>Campaign Title</th>
            <th>Submit time</th>
            <th>SenderID</th>
            <th>Submitted</th>
            <th>Total sent</th>
            <th>Charge</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php ($serial = 1); ?>

        <?php $__currentLoopData = $api_reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $api_report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($serial++); ?></td>

                <td title="<?php echo e($api_report->sci_campaign_title); ?>">
                    <?php echo e($api_report->sci_campaign_id); ?>

                </td>

                <td>
                    <?php if(!empty($api_report->sci_targeted_time)): ?>
                        <?php echo e($api_report->sci_targeted_time->format('H:i a, d-M-Y')); ?>

                    <?php endif; ?>
                </td>

                <td><?php echo e(optional($api_report->sender)->sir_sender_id); ?></td>

                <td class="text-center">
                    <?php echo e($api_report->sms_count); ?>

                </td>

                <td class="text-center">
                    <?php echo e($api_report->sms_count); ?>

                </td>

                <td class="text-right">
                    BDT <?php echo e(number_format($api_report->sci_total_cost, 2)); ?>

                </td>

                <td>
                    <a href="#my-modal"
                       onclick="show_archived_details('<?php echo e($api_report->id); ?>')"
                       role="button"
                       data-toggle="modal"
                       class="btn-none-edit CampaignId_one">
                        View
                    </a>
                    |
                    <a href="<?php echo e(route('user.reports.download_archived_report', $api_report->id)); ?>"
                       target="_blank"
                       class="btn-none-download">
                        Download
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>