<?php $__env->startSection('add_fund_credit_menu_class','active'); ?>
<?php $__env->startSection('acc_details_menu_class','open'); ?>

<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('reseller.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Price Sms</li>
    </ul>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_header'); ?>
    <h1>
        Price & Coverage
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Price List (<span style="color: forestgreen;">Your Payment Balance is: <?php echo e(number_format( $paymentable_balance, 2)); ?></span>)
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="row bg-container">
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            
        </div>
        <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">

            <?php echo $__env->make('reseller.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('reseller.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <form action="<?php echo e(route('reseller.balance.credit.store')); ?>" method="post" class="form-horizontal"
                      role="form">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="form-field-select-3" style="font-size: 20px;">Company name </label>
                        <br/>
                        
                        <select id="employeeName" class="select2 form-control" name="user_id" required="" onchange="user_balance(this.value)">
                            <option value="" hidden>Select an User</option>
                            <?php $__currentLoopData = $resellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reseller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($reseller->id); ?>" data-user-id="<?php echo e($reseller->id); ?>">
                                    <?php echo e($reseller->company_name); ?>- (<?php echo e($reseller->cellphone); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>          

                    <div class="form-group">
                        <label for="credit" style="font-size: 20px;">Credit amount <span class="text-success"
                                                                id="CustomerBalance"></span></label>
                        <input type="text" name="credit_ammount" id="credit" value=""
                               class="form-control input-mask-numberTk" style="font-size: 20px;" onkeyup="submit_button_control(this)"
                               placeholder="00.00" maxlength="10" required>
                    </div>

                    <div class="form-group">
                        <label for="payReference" style="font-size: 20px;">Payment reference :</label>
                        <input type="text" name="payment_reference" id="payReference" value="" class="form-control"
                               placeholder="Reference" maxlength="32" style="font-size: 20px;" required>
                    </div>

                    <div class="form-group">
                        <label for="payMethod" style="font-size: 20px;">Payment method :</label>
                        <select style="font-size: 20px;" class="form-control" name="payment_method" required=""
                                onchange="show_terget_time(this.value)">
                            <option value="">Select method</option>
                            <option value="1">Cash</option>
                            <option value="2">Bank deposit</option>
                            <option value="3">Check</option>
                        </select>
                    </div>

                    <div class="form-group" id="target_time" style="display: none;">
                        <label for="target" style="font-size: 20px;">Target time </label>
                        <div class='input-group date' id='datetimepicker2'>
                            <input type="text" name="target_time" id="datetimepicker1" type="text" class="form-control"
                                   placeholder="d-m-yyyyy">
                            <span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
                        </div>
                    </div>

                    <div class="clearfix form-group" id="submit_btn_debit">
                        <input type="submit" id="credit_submit_btn" class="btn btn-info" value="Submit">
                        &nbsp; &nbsp; &nbsp;
                        <button class="btn btn-danger" type="reset">
                            <i class="ace-icon fa fa-undo bigger-110"></i>
                            Reset
                        </button>
                    </div>
                </form>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-left: 20px;">
            <div class="row" style="margin-left: 20px;">
                
                    <div id="transaction-history">
                        <!-- Transaction history will be displayed here -->
                    </div>

            </div>
        </div>
    </div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_style'); ?>
    <link href="<?php echo e(asset('assets')); ?>/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/chosen.min.css"/>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js//moment.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js//bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">

    // =========
        $(document).ready(function() {
            $('.select2').select2();

            $('#employeeName').on('change', function() {
                var selectedUserId = $(this).find('option:selected').data('user-id');
                fetchTransactionHistory(selectedUserId);
            });

            function fetchTransactionHistory(userId) {
                $.ajax({
                    url: '<?php echo e(route('reseller.transaction.history')); ?>',
                    type: 'GET',
                    data: {
                        userId: userId 
                    },
                    success: function(data) {
                        $('#transaction-history').html(data);
                    }
                });
            }
        });
    // ==========

        $(function () {
            $('#datetimepicker1').datetimepicker();
        });

        $(document).ready(function() {
            $('.select2').select2();
        });
        $('.chosen-select').chosen({allow_single_deselect: true});

        function show_terget_time(value) {
            if (value == '1') {
                $('#target_time').hide();
            }
            else if (value == '2') {
                $('#target_time').show();
            }
            else if (value == '3') {
                $('#target_time').show();
            }

        }

        function submit_button_control(credit) {

            var resellerBalance = parseFloat("<?php echo e($paymentable_balance); ?>");

            var now_balance = credit.value;
            var bal = now_balance.replace(/,/g,'');
            // alert(resellerBalance);
            if (resellerBalance >= bal && bal > -1) {
                $("#credit_submit_btn").css("display", "inline-block");
            } else {
                $("#credit_submit_btn").css("display", "none");
            }
        }
    </script>
    <script type="text/javascript">
        $("#credit").on('keyup',function(){
            var n = parseInt($(this).val().replace(/\D/g,''),10);
            $(this).val(n.toLocaleString());
        })
    </script>

    <?php echo $__env->make('admin.ajax.check_customer_available_balance', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('reseller.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>