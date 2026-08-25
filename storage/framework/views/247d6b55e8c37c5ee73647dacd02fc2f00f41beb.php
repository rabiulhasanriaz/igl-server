

<?php $__env->startSection('user_list_menu_class','active'); ?>
<?php $__env->startSection('user_menu_class','open'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('reseller.index')); ?>">Dashboard</a>
        </li>
        <li>
            <a href="<?php echo e(route('reseller.user.index')); ?>">User List</a>
        </li>
        <li class="active">Price Management</li>
    </ul>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_header'); ?>
    <h1>
        Price Management
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Update Rates for <?php echo e($user->userDetail->company_name); ?>

        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
    <div class="row">
        <div class="col-xs-12">
            <?php echo $__env->make('reseller.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('reseller.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <div class="widget-box transparent">
                <div class="widget-header">
                    <h4 class="widget-title lighter">
                        <i class="ace-icon fa fa-user"></i>
                        <?php echo e($user->userDetail->company_name); ?> - Rate Management
                    </h4>
                    <div class="widget-toolbar no-border">
                        <ul class="nav nav-tabs" id="user-tabs">
                            <li class="active">
                                <a data-toggle="tab" href="#rates-tab">
                                    <i class="ace-icon fa fa-money bigger-120"></i>
                                    Operator Rates
                                </a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#profile-tab">
                                    <i class="ace-icon fa fa-user bigger-120"></i>
                                    Profile Details
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main padding-12 no-padding-left no-padding-right">
                        <div class="tab-content padding-4">
                            <!-- Rates Tab -->
                            <div id="rates-tab" class="tab-pane active">
                                <div class="clearfix">
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-sm btn-success" onclick="submitBulkUpdate()">
                                            <i class="ace-icon fa fa-refresh"></i>
                                            Bulk Update All Prices
                                        </button>
                                        <span class="help-button" data-rel="popover" data-trigger="hover" 
                                              data-placement="left" data-content="Update all prices at once">
                                            <i class="ace-icon fa fa-question-circle blue"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="space-4"></div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead class="thin-border-bottom">
                                            <tr class="info">
                                                <th rowspan="2" class="middle">Country</th>
                                                <th rowspan="2" class="middle">Operator</th>
                                                <th rowspan="2" class="middle">Prefix</th>
                                                <th rowspan="2" class="middle">Buying Price/SMS</th>
                                                <th rowspan="2" class="middle text-center" style="width: 18%;">Masking</th>
                                                <th colspan="2" class="text-center">Non Masking</th>
                                                <th rowspan="2" class="middle">Action</th>
                                            </tr>
                                            <tr class="info">
                                                <th class="text-center" style="width: 18%;">MNO</th>
                                                <th class="text-center" style="width: 18%;">IPTSP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $smsRates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $smsRate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($smsRate->country->country_name); ?></td>
                                                <td><?php echo e($smsRate->operator->ope_operator_name); ?></td>
                                                <td><?php echo e($smsRate->operator->ope_number); ?></td>
                                                <td>
                                                    <span class="label label-info arrowed-in-right">
                                                        BDT: <?php echo e($smsRate->asr_masking); ?> / <?php echo e($smsRate->asr_nonmasking); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <form action="<?php echo e(route('reseller.user.priceUpdate', $smsRate->id)); ?>"
                                                          method="post" id="form_<?php echo e($smsRate->id); ?>">
                                                        <?php echo csrf_field(); ?>
                                                    </form>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon">
                                                            <i class="ace-icon fa fa-tag"></i>
                                                        </span>
                                                        <input type="text" name="masking_price"
                                                               form="form_<?php echo e($smsRate->id); ?>"
                                                               class="form-control" value="<?php echo e($smsRate->asr_masking); ?>" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon">
                                                            <i class="ace-icon fa fa-tag"></i>
                                                        </span>
                                                        <input type="text" name="non_masking_price"
                                                               form="form_<?php echo e($smsRate->id); ?>"
                                                               class="form-control" value="<?php echo e($smsRate->asr_nonmasking); ?>" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon">
                                                            <i class="ace-icon fa fa-tag"></i>
                                                        </span>
                                                        <input type="text" name="non_masking_iptsp_price"
                                                               form="form_<?php echo e($smsRate->id); ?>"
                                                               class="form-control" value="<?php echo e($smsRate->asr_nonmasking_iptsp); ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <button type="button" onclick="submitRateForm('form_<?php echo e($smsRate->id); ?>')" 
                                                            class="btn btn-xs btn-primary">
                                                        <i class="ace-icon fa fa-save"></i>
                                                        Save
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Profile Tab -->
                            <div id="profile-tab" class="tab-pane">
                                <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Company Name </div>
                                        <div class="profile-info-value">
                                            <span><?php echo e($user->company_name); ?></span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Contact Person </div>
                                        <div class="profile-info-value">
                                            <span><?php echo e($user->userDetail->name); ?></span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Email </div>
                                        <div class="profile-info-value">
                                            <span><?php echo e($user->email); ?></span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Cell Phone </div>
                                        <div class="profile-info-value">
                                            <span><?php echo e($user->cellphone); ?></span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Designation </div>
                                        <div class="profile-info-value">
                                            <span><?php echo e($user->userDetail->designation); ?></span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Address </div>
                                        <div class="profile-info-value">
                                            <span><?php echo e($user->userDetail->address); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
    <script type="text/javascript">
        jQuery(function($) {
            // Initialize scrollable
            $('.scrollable').ace_scroll({
                size: 800,
                mouseWheelLock: true
            });

            // Popover for help button
            $('[data-rel=popover]').popover({container:'body'});

            // DataTable initialization
            $('#reseller-list-table').DataTable({
                "responsive": true,
                "autoWidth": false
            });
        });

        function submitRateForm(formName) {
            $("#" + formName).submit();
        }

        function submitBulkUpdate() {
            // Store original button HTML
            var bulkUpdateBtn = $('.btn-success');
            var originalBtnHtml = bulkUpdateBtn.html();
            
            if(confirm('Are you sure you want to update all prices at once? This action cannot be undone.')) {
                // Show loading indicator
                var loading = '<i class="ace-icon fa fa-spinner fa-spin"></i> Processing...';
                bulkUpdateBtn.html(loading).attr('disabled', true);

                // Collect all form data
                let bulkData = {
                    _token: "<?php echo e(csrf_token()); ?>",
                    user_id: "<?php echo e($user->id); ?>",
                    rates: []
                };

                // Loop through each form and collect data
                $('form[id^="form_"]').each(function() {
                    let formId = $(this).attr('id').replace('form_', '');
                    let linkedInputs = $('[form="' + $(this).attr('id') + '"]');
                    bulkData.rates.push({
                        id: formId,
                        masking_price: linkedInputs.filter('[name="masking_price"]').val(),
                        non_masking_price: linkedInputs.filter('[name="non_masking_price"]').val(),
                        non_masking_iptsp_price: linkedInputs.filter('[name="non_masking_iptsp_price"]').val()
                    });
                });

                // Submit via AJAX
                $.ajax({
                    url: "<?php echo e(route('reseller.user.bulk-update')); ?>",
                    type: "POST",
                    data: bulkData,
                    success: function(response) {
                        // Always restore button state
                        bulkUpdateBtn.html(originalBtnHtml).attr('disabled', false);
                        
                        if(response.success) {
                            // Show success message
                            $.gritter.add({
                                title: 'Success!',
                                text: 'All prices updated successfully!',
                                class_name: 'gritter-success',
                                time: 2000,
                                after_close: function() {
                                    // Refresh the page after notification closes
                                    location.reload();
                                }
                            });
                        } else {
                            $.gritter.add({
                                title: 'Error!',
                                text: response.message || 'Failed to update prices',
                                class_name: 'gritter-error',
                                time: 2000
                            });
                        }
                    },
                    error: function(xhr) {
                        // Restore button state on error
                        bulkUpdateBtn.html(originalBtnHtml).attr('disabled', false);
                        
                        let errorMsg = 'An error occurred while updating prices. Please try again.';
                        if(xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        $.gritter.add({
                            title: 'Error!',
                            text: errorMsg,
                            class_name: 'gritter-error',
                            time: 2000
                        });
                        console.error(xhr.responseText);
                    }
                });
            }
        }
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('reseller.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>