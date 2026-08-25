

<?php $__env->startSection('sender_id_menu_class','open'); ?>
<?php $__env->startSection('user_sender_id_menu_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.senderID.index')); ?>">Sender ID</a>
        </li>
        <li class="active">User Sender ID</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_header'); ?>
    <h1>
        User Sender ID
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Add
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">

                <?php if($errors->any()): ?>
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="text-danger"><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
                <?php if(session()->has('message')): ?>
                    <span class="text-<?php echo e(session()->get('type')); ?>"><?php echo e(session()->get('message')); ?></span>
                <?php endif; ?>

                <form action="<?php echo e(route('admin.senderID.userSenderID.store')); ?>" method="post" class="form-horizontal"
                      role="form">
                    <?php echo csrf_field(); ?>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-lg-offset-2 col-md-offset-2">
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-group">
                            <label for="sender_type">Select Sender ID Type</label>
                            <select id="sender_type" class="form-control">
                                <option value="">-- Select Type --</option>
                                <option value="masking">Masking</option>
                                <option value="nonmasking">Non-Masking</option>
                                <option value="iptsp">IPTSP</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3">SenderID</label>
                            <br/>
                            <select class="chosen-select form-control" id="form-field-select-3"
                                    data-placeholder="SenderId choose.." name="senderId" required>
                                <option value=""></option>
                                <?php $__currentLoopData = $senders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $type = 'unknown';
                                        $operatorName = 'N/A';
                                        $senderId = $sender->sir_sender_id;
                                        $assignedCompanies = [];
                                        $isAssigned = false;
                                        
                                        // Check all companies this sender ID is assigned to
                                        foreach($userSenders as $userSender) {
                                            if ($userSender->sender_id == $sender->id) {
                                                $isAssigned = true;
                                                $companyName = isset($userSender->user->company_name) ? $userSender->user->company_name : 'Unknown Company';
                                                $assignedCompanies[] = $companyName;
                                            }
                                        }
                                        
                                        // Determine type
                                        if (preg_match('/^[A-Za-z]/', $senderId)) {
                                            $type = 'masking';
                                        } elseif (preg_match('/^(8801|01)/', $senderId)) {
                                            $type = 'nonmasking';
                                        } elseif (preg_match('/^(8809|09)/', $senderId)) {
                                            $type = 'iptsp';
                                            
                                            // Match sender ID with operator numbers
                                            foreach($operators as $operator) {
                                                // Check if sender ID starts with the operator number
                                                if (strpos($senderId, $operator->ope_number) === 0) {
                                                    $operatorName = $operator->ope_operator_name;
                                                    break;
                                                }
                                            }
                                        }
                                        
                                        // Prepare display for assigned companies
                                        $assignedDisplay = '';
                                        if ($isAssigned) {
                                            $totalCompanies = count($assignedCompanies);
                                            if ($totalCompanies <= 5) {
                                                $assignedDisplay = implode(', ', $assignedCompanies);
                                            } else {
                                                $firstFive = array_slice($assignedCompanies, 0, 5);
                                                $assignedDisplay = implode(', ', $firstFive) . ', ...';
                                            }
                                        }
                                    ?>
                                    <option value="<?php echo e($sender->id); ?>" 
                                            data-type="<?php echo e($type); ?>" 
                                            data-operator="<?php echo e($operatorName); ?>"
                                            data-assigned="<?php echo e($isAssigned ? 'yes' : 'no'); ?>"
                                            class="<?php echo e($isAssigned ? 'assigned-option' : 'available-option'); ?>">
                                        <?php echo e($senderId); ?> 
                                        <?php if($type == 'iptsp' && $operatorName != 'N/A'): ?>
                                            (<?php echo e($operatorName); ?>)
                                        <?php endif; ?>
                                        <?php if($isAssigned): ?>
                                            - [<?php echo e($assignedDisplay); ?>]
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            
                            <!-- Legend -->
                            <div style="margin-top: 10px; padding: 8px 12px; background: #f5f5f5; border-radius: 4px; border: 1px solid #e0e0e0;">
                                <span class="legend-box legend-available"></span>
                                <span class="legend-text">Available</span>
                                
                                <span class="legend-box legend-assigned"></span>
                                <span class="legend-text">Already Assigned to Company</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3"> Company name </label>
                            <br/>
                            <select class="select2 form-control" id="form-field-select-3"
                                    data-placeholder="Company name.." name="User_id" required="">
                                <option value=""></option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>"> <?php echo e($user->company_name); ?> -
                                        ( <?php echo e($user->cellphone); ?> )
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="clearfix form-group">
                            <input type="submit" class="btn btn-info" value="Submit">
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-danger" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div><!-- /.col -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f8f8f8;">
                <hr>
                <h3>Default SenderID Information</h3>
                <table id="user-senderID-table" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Company name</th>
                        <th>User Id</th>
                        <th>SenderID</th>
                        <th>Operator</th>
                        <th>Create date</th>
                        <th>Status</th>
                        <th>System</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $serial = 1; ?>
                    <?php $__currentLoopData = $userSenders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userSender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $senderType = 'unknown';
                            $operatorName = 'N/A';
                            $senderId = isset($userSender->sender->sir_sender_id) ? $userSender->sender->sir_sender_id : '';
                            $matchedOperators = [];
                            
                            if ($senderId) {
                                if (preg_match('/^[A-Za-z]/', $senderId)) {
                                    $senderType = 'masking';
                                } elseif (preg_match('/^(8801|01)/', $senderId)) {
                                    $senderType = 'nonmasking';
                                } elseif (preg_match('/^(8809|09)/', $senderId)) {
                                    $senderType = 'iptsp';
                                }
                                
                                // Match sender ID with operator numbers
                                foreach($operators as $operator) {
                                    // Check if sender ID starts with the operator number
                                    if (strpos($senderId, $operator->ope_number) === 0) {
                                        $matchedOperators[] = $operator->ope_operator_name;
                                    }
                                }
                                
                                // If multiple operators match, join them with comma
                                if (!empty($matchedOperators)) {
                                    $operatorName = implode(', ', $matchedOperators);
                                }
                            }
                        ?>
                        <tr>
                            <td><?php echo e($serial++); ?></td>
                            <td><?php echo e(isset($userSender->user->company_name) ? $userSender->user->company_name : ''); ?></td>
                            <td><?php echo e(isset($userSender->user->userDetail->name) ? $userSender->user->userDetail->name : ''); ?></td>
                            <td><?php echo e($senderId); ?></td>
                            <td>
                                <?php if($operatorName != 'N/A'): ?>
                                    <span class="label label-info"><?php echo e($operatorName); ?></span>
                                <?php else: ?>
                                    <span class="label label-default"><?php echo e(ucfirst($senderType)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e(isset($userSender->created_at) ? $userSender->created_at->format('j-M-Y') : ''); ?></td>

                            <?php if(isset($userSender->status)): ?>
                                <?php if($userSender->status == 1): ?>
                                    <td>Active</td>
                                <?php elseif($userSender->status == 2): ?>
                                    <td>Inactive</td>
                                <?php else: ?>
                                    <td>Pending</td>
                                <?php endif; ?>
                            <?php else: ?>
                                <td>Pending</td>
                            <?php endif; ?>

                            <td>
                                <a href="<?php echo e(route('admin.senderID.userSenderID.edit', [$userSender->id])); ?>"
                                   class="btn-none-edit">Edit</a> |
                                <a href="<?php echo e(route('admin.senderID.userSenderID.delete', [$userSender->id])); ?>" class="btn-none-delete"
                                   onclick="return confirm('Are you sure you want to delete ?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->

<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/chosen.min.css"/>
    <link href="<?php echo e(asset('assets/datatable/jquery.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('assets/datatable/rowReorder.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('assets/datatable/responsive.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        @media(max-width:575px){
            .abcd{
                width: 130px;
            }
        }
        
        /* Style for available options - Light Green */
        .available-option {
            background-color: #e8f5e9 !important;
            color: #1b5e20 !important;
            padding: 8px 12px !important;
        }
        
        /* Style for assigned options - Light Orange */
        .assigned-option {
            background-color: #fff3e0 !important;
            color: #bf360c !important;
            padding: 8px 12px !important;
            border-left: 3px solid #ff9800 !important;
        }
        
        /* Hover effect for available options - Dark Green with White Text */
        .available-option:hover {
            background-color: #2e7d32 !important;
            color: #ffffff !important;
            cursor: pointer !important;
        }
        
        /* Hover effect for assigned options - Dark Orange with White Text */
        .assigned-option:hover {
            background-color: #bf360c !important;
            color: #ffffff !important;
            cursor: pointer !important;
        }
        
        /* Chosen select - Available options */
        .chosen-select .chosen-results li.available-option {
            background-color: #e8f5e9 !important;
            color: #1b5e20 !important;
        }
        
        /* Chosen select - Assigned options */
        .chosen-select .chosen-results li.assigned-option {
            background-color: #fff3e0 !important;
            color: #bf360c !important;
            border-left: 3px solid #ff9800 !important;
        }
        
        /* Chosen select - Hover for Available - Dark Green with White */
        .chosen-select .chosen-results li.available-option:hover,
        .chosen-select .chosen-results li.available-option.highlighted {
            background-color: #2e7d32 !important;
            color: #ffffff !important;
            background-image: none !important;
        }
        
        /* Chosen select - Hover for Assigned - Dark Orange with White */
        .chosen-select .chosen-results li.assigned-option:hover,
        .chosen-select .chosen-results li.assigned-option.highlighted {
            background-color: #bf360c !important;
            color: #ffffff !important;
            background-image: none !important;
        }
        
        /* Chosen select results padding */
        .chosen-select .chosen-results li {
            padding: 10px 15px !important;
            font-size: 13px !important;
        }
        
        /* Legend styling */
        .legend-box {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 3px;
            margin-right: 5px;
            vertical-align: middle;
        }
        
        .legend-available {
            background: #e8f5e9;
            border: 1px solid #4caf50;
        }
        
        .legend-assigned {
            background: #fff3e0;
            border: 1px solid #ff9800;
        }
        
        .legend-text {
            vertical-align: middle;
            margin-right: 15px;
            font-size: 12px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
    <script>
        document.getElementById('sender_type').addEventListener('change', function () {
            const selectedType = this.value;
            const senderSelect = document.getElementById('form-field-select-3');
            const options = senderSelect.querySelectorAll('option');

            options.forEach(option => {
                const type = option.getAttribute('data-type');

                // Always show the placeholder option
                if (!option.value) {
                    option.style.display = '';
                    return;
                }

                // Show ALL options regardless of assignment
                if (!selectedType || type === selectedType) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });

            // Reset the selected option
            senderSelect.value = '';
            // If using Chosen plugin, trigger update
            if ($(senderSelect).hasClass('chosen-select')) {
                $(senderSelect).trigger("chosen:updated");
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/chosen.jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
       $(document).ready(function() {
            $('.select2').select2();
        });
   </script>
    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect: true});
        $(document).ready(function() {
            var table = $('#user-senderID-table').DataTable( {
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>