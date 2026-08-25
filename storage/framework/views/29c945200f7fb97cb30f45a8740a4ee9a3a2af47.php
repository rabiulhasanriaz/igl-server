<?php $__env->startSection('pending_sms_menu_class','active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Reports SMS</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        Reports & Statistics
        <i class="ace-icon fa fa-angle-double-right"></i>
        View DLR
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Pending's SMS
        </small>
    </h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>User</th>
                    <th>Campaign Title</th>
                    <th>Submit time</th>
                    <th>SenderID</th>
                    <th>Submitted</th>
                    <th>Charge</th>
                    <th>Content</th>
                    <th style="width: 100px;">Route</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                <?php ($serial=1); ?>
                <?php $__currentLoopData = $pending_campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pending_campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($serial++); ?></td>
                        <td><?php echo e($pending_campaign->user->company_name); ?> / <?php echo e($pending_campaign->user->cellphone); ?></td>
                        <td title="<?php echo e($pending_campaign->sci_campaign_id); ?>"><?php echo e($pending_campaign->sci_campaign_title); ?></td>
                        <td><?php echo e($pending_campaign->sci_targeted_time->format('h:i A, d-M-Y')); ?></td>
                        <td><?php echo e($pending_campaign->sender->sir_sender_id); ?></td>
                        <td class="text-center"><?php echo e($pending_campaign->sci_total_submitted); ?></td>
                        <td class="text-right">BDT <?php echo e(number_format($pending_campaign->sci_total_cost, 2)); ?></td>
                        <td>
                            <?php if(count($pending_campaign->pendingSmsData) > 0): ?>
                                <pre style="width:400px !important;"><?php echo $pending_campaign->pendingSmsData->first()->scp_message; ?></pre>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="<?php echo e(route('admin.accept-campaign-sms', $pending_campaign->id)); ?>">
                                <?php echo csrf_field(); ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" <?php echo e(($pending_campaign->sci_route == 0)? 'checked' : ''); ?> type="radio" name="route" id="inlineRadio1" value="0">
                                <label class="form-check-label" for="inlineRadio1">A</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" <?php echo e(($pending_campaign->sci_route == 1)? 'checked' : ''); ?> type="radio" name="route" id="inlineRadio2" value="1">
                                <label class="form-check-label" for="inlineRadio2">B</label>
                              </div>
                        </td>
                        <td>
                            <button type="submit" onclick="return confirm('Are you sure to accept this campaign?')" class="btn btn-xs btn-success">
                                <i class="fa fa-check"></i>
                            </button>
                        </form>
                            <a href="<?php echo route('admin.reject-campaign-sms', $pending_campaign->id); ?>" onclick="return confirm('Are you sure to reject this campaign?')" class="btn btn-xs btn-danger">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                </tbody>
            </table>

            <!-- ------model view start-->
            <div id="my-modal" class="modal fade" tabindex="-1" style="display: none; z-index: 2001;">
                <div class="modal-dialog" style="width: 80%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 class="smaller lighter blue no-margin text-primary"> Today Report Details</h3>
                        </div>
                        <div class="modal-body">
                            <div id="SmsInformation"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div><!-- /.modal-dialog -->
            </div>

            <div class="modal fade" id="today_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2000;">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Api Report Detail</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body" id="todayReportDetails">

                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>

        </div><!-- /.col -->
    </div><!-- /.row -->

<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_script'); ?>
    <script type="text/javascript">
    </script>
<?php $__env->stopSection(); ?>













<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>