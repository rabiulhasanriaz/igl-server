

<?php $__env->startSection('virtual_number_menu_class','open'); ?>
<?php $__env->startSection('low_balance_virtual_number_menu_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.senderID.index')); ?>">Sender ID</a>
        </li>
        <li class="active">Low Balance Virtual Numbers</li>
    </ul>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_header'); ?>
    <h1>
        Low Balance Virtual Numbers
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            List
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
    <div class="space-6"></div>

    <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding bg-container">
                <hr>
                
                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h4>Critical Low Balance</h4>
                            </div>
                            <div class="panel-body text-center">
                                <h2><?php echo e($summary['total_low_balance'] ?? 0); ?></h2>
                                <p>Numbers with balance &lt; 500 TK</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <h4>Warning Balance</h4>
                            </div>
                            <div class="panel-body text-center">
                                <h2><?php echo e($summary['total_warning'] ?? 0); ?></h2>
                                <p>Numbers with balance 500-999 TK</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4>Lowest Balance</h4>
                            </div>
                            <div class="panel-body text-center">
                                <h2><?php echo e(number_format($summary['lowest_balance'] ?? 0, 2)); ?> TK</h2>
                                <p>Lowest balance amount</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h4>Total Numbers</h4>
                            </div>
                            <div class="panel-body text-center">
                                <h2><?php echo e($summary['total_numbers'] ?? 0); ?></h2>
                                <p>Numbers with low balance</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix" style="margin-bottom: 15px;">
                    <button class="btn btn-primary pull-right" onclick="refreshLowBalance()">
                        <i class="ace-icon fa fa-refresh"></i> Refresh List
                    </button>
                </div>

                <h3>Low Balance Virtual Numbers (Balance &lt; 1000 TK)</h3>
                <div class="table-responsive">
                    <table class="table table-bordered" id="low-balance-table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Virtual Number Name</th>
                                <th>Virtual Number</th>
                                <th>Operator</th>
                                <th>Username</th>
                                <th>Current Balance</th>
                                <th>Load Amount</th>
                                <th>Status</th>
                                <th>Pending Messages</th>
                                <th>Action</th>
                                <th>Change to IPTSP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php ($serial = 1); ?>
                            <?php $__currentLoopData = $lowBalanceNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($serial++); ?></td>
                                    <td><?php echo e($vn['virtual_name']); ?></td>
                                    <td><?php echo e($vn['virtual_number']); ?></td>
                                    <td><?php echo e($vn['operator']); ?></td>
                                    <td><?php echo e($vn['username']); ?></td>
                                    <td>
                                        <strong style="color: <?php echo e($vn['balance'] < 500 ? '#e74c3c' : '#f39c12'); ?>; font-size: 16px;">
                                            <?php echo e(number_format($vn['balance'], 2)); ?> TK
                                        </strong>
                                    </td>
                                    <td><?php echo e($vn['load_amount']); ?></td>
                                    <td>
                                        <span class="label label-<?php echo e($vn['status_class']); ?>">
                                            <?php echo e($vn['status']); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="label label-info pending-count-<?php echo e($vn['id']); ?>">Loading...</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a class="green" href="javascript:void(0)" onclick="checkBalance(<?php echo e($vn['id']); ?>, '<?php echo e(addslashes($vn['virtual_name'])); ?>', '<?php echo e(addslashes($vn['virtual_number'])); ?>')" title="Check Balance">
                                                <i class="ace-icon glyphicon glyphicon-usd"></i>
                                            </a>
                                            <a class="green" href="<?php echo e(route('admin.virtualNumber.edit', $vn['id'])); ?>" title="Edit">
                                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" onclick="showChangeSenderModal(<?php echo e($vn['id']); ?>, '<?php echo e(addslashes($vn['virtual_number'])); ?>', '<?php echo e(addslashes($vn['operator'])); ?>')">
                                            <i class="ace-icon fa fa-exchange"></i> Change to IPTSP
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if(count($lowBalanceNumbers) == 0): ?>
                                <tr>
                                    <td colspan="11" class="text-center">No virtual numbers with low balance found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Modal -->
    <div class="modal fade" id="balanceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Balance Information</h4>
                </div>
                <div class="modal-body">
                    <div id="balance-loading" style="text-align:center; display:none; padding:20px;">
                        <i class="ace-icon fa fa-spinner fa-spin fa-2x"></i>
                        <p>Loading balance...</p>
                    </div>
                    <div id="balance-result"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change to IPTSP Modal -->
    <div class="modal fade" id="changeSenderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: #f39c12; color: white;">
                    <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
                    <h4 class="modal-title">
                        <i class="ace-icon fa fa-exchange"></i> Change to IPTSP Number
                    </h4>
                </div>
                <div class="modal-body">
                    <div id="change-sender-loading" style="text-align:center; display:none; padding:20px;">
                        <i class="ace-icon fa fa-spinner fa-spin fa-2x"></i>
                        <p>Loading IPTSP numbers...</p>
                    </div>
                    <div id="change-sender-content">
                        <div class="alert alert-info">
                            <strong>Current Virtual Number:</strong> <span id="change-vn-number"></span><br>
                            <strong>Operator:</strong> <span id="change-vn-operator"></span>
                        </div>
                        
                        <div class="form-group">
                            <label>Select IPTSP Number:</label>
                            <select id="new_sender_id" class="form-control select2-iptsp" style="width: 100%;">
                                <option value="">Loading IPTSP numbers...</option>
                            </select>
                            <small class="text-muted">Search and select IPTSP number (88096xxxxxx)</small>
                        </div>
                        
                        <div id="pending-count" class="alert alert-warning" style="display:none;">
                            <i class="ace-icon fa fa-clock-o"></i>
                            <strong>Pending Messages:</strong> <span id="pending-count-number">0</span> messages will be changed to IPTSP number
                        </div>
                        
                        <div class="alert alert-danger">
                            <i class="ace-icon fa fa-warning"></i>
                            <strong>Warning:</strong> This will change the sender ID for ALL pending messages from this virtual number to the selected IPTSP number. This action cannot be undone!
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="changeSenderId()">
                        <i class="ace-icon fa fa-exchange"></i> Change to IPTSP
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_style'); ?>
    <link href="<?php echo e(asset('assets/datatable/jquery.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />
    <style>
        .panel {
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .panel-danger .panel-heading {
            background-color: #e74c3c;
            color: white;
        }
        .panel-warning .panel-heading {
            background-color: #f39c12;
            color: white;
        }
        .panel-info .panel-heading {
            background-color: #3498db;
            color: white;
        }
        .panel-primary .panel-heading {
            background-color: #2c3e50;
            color: white;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .label-danger {
            background-color: #e74c3c;
        }
        .label-warning {
            background-color: #f39c12;
        }
        .label-info {
            background-color: #3498db;
        }
        .btn-sm {
            padding: 3px 8px;
            margin: 2px;
        }
        .action-buttons a {
            margin: 0 3px;
        }
        .select2-container--bootstrap .select2-selection {
            border-radius: 4px;
            min-height: 34px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    
    <script type="text/javascript">
        let currentVirtualNumberId = null;
        let currentVirtualNumber = null;
        let currentOperator = null;
        let iptspSelect2 = null;

        $(document).ready(function() {
            $('#low-balance-table').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[5, 'asc']]
            });
            
            // Initialize Select2
            iptspSelect2 = $('#new_sender_id').select2({
                theme: 'bootstrap',
                placeholder: 'Search IPTSP number...',
                allowClear: true,
                width: '100%'
            });
            
            // Load pending counts for each virtual number
            <?php $__currentLoopData = $lowBalanceNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                loadPendingCount(<?php echo e($vn['id']); ?>);
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        });
        
        function loadPendingCount(virtualNumberId) {
            $.ajax({
                url: '<?php echo e(route("admin.virtualNumber.getPendingCount")); ?>',
                type: 'GET',
                data: { virtual_number_id: virtualNumberId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('.pending-count-' + virtualNumberId).text(response.pending_count + ' pending');
                        if (response.pending_count > 0) {
                            $('.pending-count-' + virtualNumberId).addClass('label-warning').removeClass('label-info');
                        } else {
                            $('.pending-count-' + virtualNumberId).addClass('label-success').removeClass('label-info');
                        }
                    } else {
                        $('.pending-count-' + virtualNumberId).text('0 pending');
                    }
                },
                error: function() {
                    $('.pending-count-' + virtualNumberId).text('0 pending');
                }
            });
        }

        function checkBalance(id, name, number) {
            $('#balance-loading').show();
            $('#balance-result').html('');
            $('#balanceModal').modal('show');
            
            $.ajax({
                url: '<?php echo e(route("admin.virtualNumber.balance_query", "")); ?>/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#balance-loading').hide();
                    
                    if (response.success) {
                        let balance = 'N/A';
                        
                        if (response.data && response.data.data && response.data.data.availableBalance) {
                            balance = response.data.data.availableBalance;
                        } else if (response.data && response.data.availableBalance) {
                            balance = response.data.availableBalance;
                        } else if (response.data && response.data.data && response.data.data.balance) {
                            balance = response.data.data.balance;
                        } else if (response.data && response.data.balance) {
                            balance = response.data.balance;
                        } else if (response.data && response.data.response && response.data.response.availableBalance) {
                            balance = response.data.response.availableBalance;
                        }
                        
                        let balanceClass = parseFloat(balance) < 500 ? 'danger' : (parseFloat(balance) < 1000 ? 'warning' : 'success');
                        let balanceColor = balanceClass == 'danger' ? '#e74c3c' : (balanceClass == 'warning' ? '#f39c12' : '#27ae60');
                        
                        let html = '<div class="alert alert-info">';
                        html += '<h4>Balance Information</h4>';
                        html += '<hr>';
                        html += '<p><strong>Virtual Number Name:</strong> ' + name + '</p>';
                        html += '<p><strong>Virtual Number:</strong> ' + number + '</p>';
                        html += '<p><strong>Current Balance:</strong> <span style="font-size:24px;font-weight:bold;color:' + balanceColor + ';">' + parseFloat(balance).toFixed(2) + '</span> TK</p>';
                        if (response.cached) {
                            html += '<p><small><i class="ace-icon fa fa-database"></i> Cached result (5 minutes)</small></p>';
                        }
                        html += '</div>';
                        $('#balance-result').html(html);
                    } else {
                        $('#balance-result').html('<div class="alert alert-danger">' + (response.message || 'Failed to get balance') + '</div>');
                    }
                },
                error: function(xhr) {
                    $('#balance-loading').hide();
                    $('#balance-result').html('<div class="alert alert-danger">Error checking balance</div>');
                }
            });
        }

        function showChangeSenderModal(id, virtualNumber, operator) {
            currentVirtualNumberId = id;
            currentVirtualNumber = virtualNumber;
            currentOperator = operator;
            
            $('#change-vn-number').text(virtualNumber);
            $('#change-vn-operator').text(operator);
            $('#change-sender-loading').show();
            $('#change-sender-content').hide();
            $('#changeSenderModal').modal('show');
            
            // Clear and reset Select2
            iptspSelect2.empty().append('<option value="">Loading IPTSP numbers...</option>').trigger('change');
            
            // Load IPTSP sender IDs
            $.ajax({
                url: '<?php echo e(route("admin.virtualNumber.getSenderIds")); ?>',
                type: 'GET',
                data: { operator: operator, virtual_number_id: id },
                dataType: 'json',
                success: function(response) {
                    $('#change-sender-loading').hide();
                    $('#change-sender-content').show();
                    
                    if (response.success && response.senders.length > 0) {
                        // Clear Select2 and add options
                        let options = [{ id: '', text: 'Search IPTSP number...' }];
                        $.each(response.senders, function(key, sender) {
                            options.push({
                                id: sender.id,
                                text: sender.sir_sender_id + ' (IPTSP)'
                            });
                        });
                        
                        iptspSelect2.empty();
                        $.each(options, function(key, option) {
                            let optionElement = new Option(option.text, option.id, false, false);
                            iptspSelect2.append(optionElement);
                        });
                        iptspSelect2.trigger('change');
                        
                        $('#pending-count-number').text(response.pending_count);
                        $('#pending-count').show();
                    } else {
                        iptspSelect2.empty().append('<option value="">No IPTSP numbers available</option>').trigger('change');
                        $('#pending-count').hide();
                        alert('No IPTSP numbers found. Please add IPTSP sender IDs first.');
                    }
                },
                error: function() {
                    $('#change-sender-loading').hide();
                    $('#change-sender-content').show();
                    iptspSelect2.empty().append('<option value="">Error loading IPTSP numbers</option>').trigger('change');
                    alert('Error loading IPTSP numbers');
                }
            });
        }

        function changeSenderId() {
            let newSenderId = iptspSelect2.val();
            
            if (!newSenderId) {
                alert('Please select an IPTSP number');
                return;
            }
            
            let virtualNumber = $('#change-vn-number').text();
            let pendingCount = $('#pending-count-number').text();
            
            if (pendingCount == 0) {
                alert('No pending messages to change for this virtual number.');
                $('#changeSenderModal').modal('hide');
                return;
            }
            
            if (confirm('Are you sure you want to change ALL ' + pendingCount + ' pending messages from virtual number ' + virtualNumber + ' to the selected IPTSP number?\n\nThis will also change SMS type to Non-Masking (1).\n\nThis action cannot be undone!')) {
                $('#change-sender-loading').show();
                $('#change-sender-content').hide();
                
                $.ajax({
                    url: '<?php echo e(route("admin.virtualNumber.changeSenderForPending")); ?>',
                    type: 'POST',
                    data: {
                        virtual_number_id: currentVirtualNumberId,
                        new_sender_id: newSenderId,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#change-sender-loading').hide();
                        if (response.success) {
                            alert('Success! Changed ' + response.updated_count + ' pending messages to IPTSP number: ' + response.new_sender_number + '\nSMS type changed to Non-Masking (1)');
                            $('#changeSenderModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                            $('#change-sender-content').show();
                        }
                    },
                    error: function(xhr) {
                        $('#change-sender-loading').hide();
                        $('#change-sender-content').show();
                        let errorMsg = xhr.responseJSON?.message || 'Error changing sender ID. Please try again.';
                        alert(errorMsg);
                    }
                });
            }
        }

        function refreshLowBalance() {
            $('#balance-loading').show();
            
            $.ajax({
                url: '<?php echo e(route("admin.virtualNumber.refresh_low_balance")); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#balance-loading').hide();
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to refresh list');
                    }
                },
                error: function() {
                    $('#balance-loading').hide();
                    alert('Error refreshing list');
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>