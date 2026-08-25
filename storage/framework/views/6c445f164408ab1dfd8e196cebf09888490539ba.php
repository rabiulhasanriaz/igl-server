

<?php $__env->startSection('flexiload_menu_class','open'); ?>
<?php $__env->startSection('flexiload_balance_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Flexiload Balance Enquiry</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        Flexiload
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Available Balance
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('admin.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <div class="col-sm-6">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Airtel</span>
                  <span class="badge badge-primary badge-pill">
                      <?php if($latestbal->airtel == NULL): ?>
                      <span style="font-size: 20px;">0.00</span>
                      <?php else: ?>
                      <span style="font-size: 20px;"><?php echo e($latestbal->airtel); ?></span>
                      <?php endif; ?>
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Banglalink</span>
                  <span class="badge badge-primary badge-pill">
                      <?php if($latestbal->blink == NULL): ?>
                      <span style="font-size: 20px;">0.00</span>
                      <?php else: ?>
                      <span style="font-size: 20px;"><?php echo e($latestbal->blink); ?></span>
                      <?php endif; ?>
                  </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span style="font-size: 20px;">GrameenPhone</span>
                  <span class="badge badge-primary badge-pill">
                      <?php if($latestbal->gp == NULL): ?>
                      <span style="font-size: 20px;">0.00</span>
                      <?php else: ?>
                      <span style="font-size: 20px;"><?php echo e($latestbal->gp); ?></span>
                      <?php endif; ?>
                  </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Robi</span>
                    <span class="badge badge-primary badge-pill">
                        <?php if($latestbal->robi == NULL): ?>
                        <span style="font-size: 20px;">0.00</span>
                        <?php else: ?>
                        <span style="font-size: 20px;"><?php echo e($latestbal->robi); ?></span>
                        <?php endif; ?>
                    </span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Teletalk</span>
                    <span class="badge badge-primary badge-pill">
                        <?php if($latestbal->teletalk == NULL): ?>
                        <span style="font-size: 20px;">0.00</span>
                        <?php else: ?>
                        <span style="font-size: 20px;"><?php echo e($latestbal->teletalk); ?></span>
                        <?php endif; ?>
                    </span>
                  </li>
                  <?php
                      $total = $latestbal->airtel + $latestbal->gp + $latestbal->blink + $latestbal->robi + $latestbal->teletalk;
                  ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;" class="text-danger">Total</span>
                    <span class="badge badge-danger badge-pill">
                        <span style="font-size: 20px;"><?php echo e($total); ?></span>
                    </span>
                  </li>
              </ul>

              <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px; margin-left: 100px;" class="text-warning">Pending Flexiload Balance</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Prepaid</span>
                  <span class="badge badge-primary badge-pill">
                      <?php if($pending_bal_pre == 0): ?>
                      <span style="font-size: 20px;">0.00</span>
                      <?php else: ?>
                      <span style="font-size: 20px;"><?php echo e($pending_bal_pre); ?></span>
                      <?php endif; ?>
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Postpaid</span>
                  <span class="badge badge-primary badge-pill">
                      <?php if($pending_bal_post == 0): ?>
                      <span style="font-size: 20px;">0.00</span>
                      <?php else: ?>
                      <span style="font-size: 20px;"><?php echo e($pending_bal_post); ?></span>
                      <?php endif; ?>
                  </span>
                </li>
                  <?php
                      $total = $pending_bal_pre + $pending_bal_post;
                  ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;" class="text-danger">Total</span>
                    <span class="badge badge-danger badge-pill">
                        <span style="font-size: 20px;"><?php echo e(number_format($total,2)); ?></span>
                    </span>
                  </li>
              </ul>
        </div>

        <div class="col-sm-6">
            <div class="widget-box transparent">
                <div class="widget-header widget-header-flat">
                    <h4 class="widget-title lighter">
                        <span style="font-size: 20px; margin-left: 100px;" class="text-success">Flexiload Transaction (Last 7 Days)</span>
                    </h4>

                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <table class="table table-striped table-bordered table-hover" id="flexi_list">
                            <thead>
                              <tr>
                                <th rowspan="3">SL</th>
                                <th rowspan="3">Company Name</th>
                                <th colspan="2">Bill Amount</th>
                                <th colspan="2">Submission Summary</th>
                              </tr>
                              <tr>
                                <th colspan="1">Success</th>
                                <th colspan="1">Pending</th>
                                <th colspan="1">Success</th>
                                <th colspan="1">Pending</th>
                              </tr>
                            </thead>
                            <tbody>

                                <?php
                                function formatNumber($number) {
                                    if ($number >= 1000) {
                                        return number_format($number);
                                    }
                                    return $number;
                                }
                                ?>
                                
                                <?php ($sl = 0); ?>
                                <?php ($totalAmountSum = 0); ?>
                                <?php ($totalNumberSum = 0); ?>
                                <?php ($totalPenSum = 0); ?>
                                <?php ($penAmount = 0); ?>
                        
                                <?php $__currentLoopData = $campaignPriceSum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userId => $priceSum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php ($totalAmountSum += $priceSum); ?>
                                    <?php ($totalNumberSum += $numberSum->sum()); ?>
                                    <?php ($totalPenSum += $numPendSum->sum()); ?>
                                    <?php ($penAmount += $numPendAmount->sum()); ?>
                        
                                    <tr>
                                        <td><?php echo e(++$sl); ?></td>
                                        <td style="text-transform: uppercase;"><?php echo e($userNames->get($userId)); ?></td>
                                        <td class="hidden-480 text-right"><?php echo e(formatNumber($priceSum)); ?></td>
                                        <td class="hidden-480 text-right"><?php echo e(formatNumber($numPendAmount->get($userId, 0))); ?></td>
                                        <td class="hidden-480 text-right"><?php echo e(formatNumber($numberSum->get($userId, 0))); ?></td>
                                        <td class="hidden-480 text-right"><?php echo e(formatNumber($numPendSum->get($userId, 0))); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td class="text-right"><strong><?php echo e(formatNumber($totalAmountSum)); ?></strong></td>
                                    <td class="text-right"><?php echo e(formatNumber($penAmount)); ?></td>
                                    <td class="text-right"><?php echo e(formatNumber($totalNumberSum)); ?></td>
                                    <td class="text-right"><?php echo e(formatNumber($totalPenSum)); ?></td>
                                </tr>
                            </tbody>
                          </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#flexi_list').DataTable();
        });
        
    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>