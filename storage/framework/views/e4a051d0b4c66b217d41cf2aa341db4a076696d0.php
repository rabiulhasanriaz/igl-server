
<?php $__env->startSection('topup_menu_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Top Up Balance</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
    <div class="space-6"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
            <!-- Custom Error and Success Messages -->
            <div class="row">
                <div class="col-md-12">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <strong>
                                <i class="ace-icon fa fa-times"></i>
                                Error!
                            </strong>
                            Please fix the following errors:
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <strong>
                                <i class="ace-icon fa fa-check"></i>
                                Success!
                            </strong>
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <strong>
                                <i class="ace-icon fa fa-times"></i>
                                Error!
                            </strong>
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('warning')): ?>
                        <div class="alert alert-warning">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <strong>
                                <i class="ace-icon fa fa-warning"></i>
                                Warning!
                            </strong>
                            <?php echo e(session('warning')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('info')): ?>
                        <div class="alert alert-info">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <strong>
                                <i class="ace-icon fa fa-info"></i>
                                Info!
                            </strong>
                            <?php echo e(session('info')); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">Top Up SMS Balance</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <form action="<?php echo e(route('user.balance.initiate')); ?>" method="POST" id="topup-form">
                                <?php echo csrf_field(); ?>
                                
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="amount">Top-up Amount (BDT) <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <input type="number" 
                                                       name="amount" 
                                                       id="amount"
                                                       class="form-control input-lg" 
                                                       placeholder="Enter amount"
                                                       min="2000"
                                                       step="1"
                                                       value="<?php echo e(old('amount')); ?>"
                                                       style="font-size: 18px; height: 50px; padding: 12px;"
                                                       required>
                                                <span class="input-group-addon" style="font-size: 18px; height: 50px; padding: 12px 15px;">
                                                    ৳
                                                </span>
                                            </div>
                                        </div>
                                    <div class="col-md-6">
    <label for="payable-amount">Total Payable Amount</label>
    <div class="input-group input-group-lg">
        <input type="text" 
               id="payable-amount"
               class="form-control input-lg" 
               style="font-size: 18px; height: 50px; padding: 12px; background-color: #f0f8f0; font-weight: bold; color: #28a745; border: 2px solid #28a745;"
               readonly
               value="0.00">
        <span class="input-group-addon"
              style="font-size: 18px; height: 50px; padding: 12px 15px; background-color: #28a745; color: white; border-color: #28a745;">
            ৳
        </span>
    </div>
</div>

                                    </div>
                                    <small class="text-muted" id="charge-info" style="display: block; margin-top: 10px;">Minimum amount: ৳2000 (Service charge will be calculated based on payment method)</small>
                                    <small class="text-muted" id="payable-breakdown" style="display: block;">Enter amount above to see payable amount</small>
                                    
                                    <?php if($errors->has('amount')): ?>
                                        <span class="text-danger"><?php echo e($errors->first('amount')); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label>Payment Method <span class="text-danger">*</span></label>
                                    <div class="payment-methods">
                                    <div class="radio">
                                            <label>
                                                <input type="radio" name="gateway" value="bkash"  checked class="ace gateway-radio">
                                                
                                                <span class="lbl"> 
                                                    <strong>     <img src="https://static.vecteezy.com/system/resources/previews/068/764/270/non_2x/bkash-logo-mobile-banking-app-icon-transparent-background-free-png.png"
             alt="bKash"
             style="width: 35px; height: 35px;">
             bKash</strong> - Mobile Wallet Payment
                                                  
                                                </span>
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="gateway" value="sslcommerz"  class="ace gateway-radio">
                                                <span class="lbl"> 
                                                    <strong>Bank</strong> - Credit/Debit Card, Mobile Banking, Internet Banking
                                                    <?php if(env('SSLCZ_TESTMODE', false)): ?>
                                                        <span class="label label-warning">Test Mode</span>
                                                    <?php endif; ?>
                                                </span>
                                            </label>
                                        </div>
                                        
                                    </div>
                                    <?php if($errors->has('gateway')): ?>
                                        <span class="text-danger"><?php echo e($errors->first('gateway')); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="clearfix form-actions">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn btn-success btn-lg" id="proceed-btn" style="padding: 12px 30px; font-size: 16px;">
                                            <i class="ace-icon fa fa-credit-card bigger-110"></i>
                                            Proceed to Payment
                                        </button>
                                        
                                        <a href="<?php echo e(route('user.index')); ?>" class="btn btn-default btn-lg" style="padding: 12px 30px; font-size: 16px;">
                                            <i class="ace-icon fa fa-arrow-left bigger-110"></i>
                                            Back to Dashboard
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h5 class="panel-title"><i class="ace-icon fa fa-credit-card"></i> Bank</h5>
                            </div>
                            <div class="panel-body">
                                <p><i class="ace-icon fa fa-check text-success"></i> All major credit/debit cards</p>
                                <p><i class="ace-icon fa fa-check text-success"></i> Mobile banking (Rocket, Nagad, Upay)</p>
                                <p><i class="ace-icon fa fa-check text-success"></i> Internet banking</p>
                                <p><i class="ace-icon fa fa-percent text-warning"></i> Service charge: <strong>2.5%</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h5 class="panel-title"><i class="ace-icon fa fa-mobile"></i> bKash</h5>
                            </div>
                            <div class="panel-body">
                                <p><i class="ace-icon fa fa-check text-success"></i> Pay from your bKash account</p>
                                <p><i class="ace-icon fa fa-check text-success"></i> Instant payment processing</p>
                                <p><i class="ace-icon fa fa-check text-success"></i> bKash secured transaction</p>
                                <p><i class="ace-icon fa fa-percent text-warning"></i> Service charge: <strong>2%</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">
                            <i class="ace-icon fa fa-history"></i>
                            Recent Top-ups
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <?php
                                $recentTopups = isset($recentTopups) ? $recentTopups : collect([]);
                            ?>
                            
                            <?php if($recentTopups->count() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Gateway</th>
                                                <th>Credited</th>
                                                <th>Total Paid</th>
                                                <th>Status</th>
                                                <th>Reference</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $recentTopups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($topup->asb_submit_time->format('M d, Y h:i A')); ?></td>
                                                    <td>
                                                        <?php if($topup->asb_pay_mode == 2): ?>
                                                            <span class="label label-info">SSLCommerz</span>
                                                        <?php elseif($topup->asb_pay_mode == 3): ?>
                                                            <span class="label label-success">bKash</span>
                                                        <?php else: ?>
                                                            <span class="label label-default">Other</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-success">+৳<?php echo e(number_format($topup->asb_credit, 2)); ?></td>
                                                    <td class="text-info">
                                                        <?php if(isset($topup->asb_total_paid) && $topup->asb_total_paid > 0): ?>
                                                            ৳<?php echo e(number_format($topup->asb_total_paid, 2)); ?>

                                                        <?php else: ?>
                                                            ৳<?php echo e(number_format($topup->asb_credit, 2)); ?>

                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($topup->asb_payment_status == 1 || $topup->asb_payment_status == 'completed'): ?>
                                                            <span class="label label-success">Completed</span>
                                                        <?php elseif($topup->asb_payment_status == 'pending'): ?>
                                                            <span class="label label-warning">Pending</span>
                                                        <?php elseif($topup->asb_payment_status == 'failed'): ?>
                                                            <span class="label label-danger">Failed</span>
                                                        <?php elseif($topup->asb_payment_status == 'cancelled'): ?>
                                                            <span class="label label-default">Cancelled</span>
                                                        <?php else: ?>
                                                            <span class="label label-info"><?php echo e(ucfirst($topup->asb_payment_status)); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><small><?php echo e($topup->asb_pay_ref); ?></small></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-muted">No recent top-ups found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.col -->
    </div>
    <style>
.payment-methods .radio {
    margin-bottom: 10px;
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: #fafafa;
}
.payment-methods .radio:hover {
    border-color: #5cb85c;
    background-color: #f0f9f0;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.payment-methods .radio label {
    font-weight: bold;
    cursor: pointer;
    margin: 0;
    display: flex;
    align-items: center;
}
.payment-methods .radio input[type="radio"] {
    margin-right: 10px;
}
.payment-methods .radio .lbl {
    font-size: 14px;
    line-height: 1.4;
}

/* Bigger input field */
.input-group-lg .form-control {
    font-size: 18px;
    height: 50px;
    padding: 12px;
}

/* Payable amount styling */
#payable-amount {
    background-color: #f0f8f0 !important;
    font-weight: bold;
    color: #28a745 !important;
    border: 2px solid #28a745 !important;
}

/* Panel enhancements */
.panel {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.panel-heading {
    border-radius: 8px 8px 0 0 !important;
    font-weight: bold;
}
.panel-body p {
    margin-bottom: 8px;
    font-size: 13px;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .col-lg-offset-2, .col-md-offset-2, .col-sm-offset-1 {
        margin-left: 0;
    }
    .payment-methods .radio {
        padding: 10px;
    }
    .input-group-lg .form-control {
        font-size: 16px;
        height: 45px;
        padding: 10px;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
<script>
$(document).ready(function () {

    function calculatePayable() {
        let amount = parseFloat($("#amount").val());
        let gateway = $("input[name='gateway']:checked").val();

        if (!amount || amount < 2000) {
            $("#payable-amount").val("0.00");
            $("#charge-preview").hide();
            $("#payable-breakdown").text("Enter amount above to see payable amount");
            return;
        }

        // Service charge rate
        let chargeRate = gateway === "sslcommerz" ? 0.025 : 0.02;

        // Calculate charge and total
        let serviceCharge = amount * chargeRate;
        let totalPayable = amount + serviceCharge;

        // Format numbers
        serviceCharge = serviceCharge.toFixed(2);
        totalPayable = totalPayable.toFixed(2);

        // Update payable box
        $("#payable-amount").val(totalPayable);

        // Update summary panel
        $("#preview-amount").text("৳" + amount.toFixed(2));
        $("#preview-charge").text("৳" + serviceCharge);
        $("#preview-total").text("৳" + totalPayable);
        $("#preview-gateway").text(gateway === "sslcommerz" ? "SSLCommerz" : "bKash");
        $("#final-total-amount").text("৳" + totalPayable);

        // Label
        $("#service-charge-label").text(
            gateway === "sslcommerz"
                ? "Service Charge (2.5%):"
                : "Service Charge (2%):"
        );

        // Breakdown text
        $("#payable-breakdown").text(
            "Amount: ৳" + amount.toFixed(2) +
            " + Service Charge: ৳" + serviceCharge +
            " = Total Payable: ৳" + totalPayable
        );

        // Show summary
        $("#charge-preview").slideDown(200);
    }

    // Trigger on amount change
    $("#amount").on("keyup change", function () {
        calculatePayable();
    });

    // Trigger on payment method change
    $(".gateway-radio").on("change", function () {
        calculatePayable();
    });

    // Initial load (if old value exists)
    calculatePayable();
});
</script>

<?php $__env->stopSection(); ?>




<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>