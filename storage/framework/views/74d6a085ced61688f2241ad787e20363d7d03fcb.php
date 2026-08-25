<div class="table-responsive">
    <?php if(isset($campaign)): ?>
        <div class="well well-sm" style="margin-bottom: 15px; background-color: #f5f5f5;">
            <div class="row">
                <div class="col-md-6">
                    <strong>Campaign:</strong> <?php echo e($campaign->sci_campaign_title ?? 'N/A'); ?>

                </div>
                <div class="col-md-3">
                    <strong>Campaign ID:</strong> <?php echo e($campaign->sci_campaign_id ?? 'N/A'); ?>

                </div>
                <div class="col-md-3">
                    <strong>Total Records:</strong> <?php echo e(isset($reports) ? $reports->count() : 0); ?>

                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="ace-icon fa fa-exclamation-triangle"></i>
            Campaign not found
        </div>
    <?php endif; ?>
    
    <?php if(isset($reports) && $reports->count() > 0): ?>
        <table class="table table-striped table-bordered table-hover" id="archived-details-table">
            <thead>
                <tr>
                    <th width="5%">SL</th>
                    <th width="12%">Sender ID</th>
                    <th width="15%">Mobile Number</th>
                    <th width="30%">Message</th>
                    <th width="10%">SMS Cost (BDT)</th>
                    <th width="18%">Submit Time</th>
                    <th width="10%">Delivery Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $serial = 1;
                ?>
                <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($serial++); ?></td>
                        <td>
                            <?php if(isset($report->sender) && isset($report->sender->sir_sender_id)): ?>
                                <?php echo e($report->sender->sir_sender_id); ?>

                            <?php elseif(isset($report->sender_id)): ?>
                                <?php echo e($report->sender_id); ?>

                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($report->sct_cell_no)): ?>
                                <?php echo e($report->sct_cell_no); ?>

                            <?php elseif(isset($report->sc_cell_no)): ?>
                                <?php echo e($report->sc_cell_no); ?>

                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($report->sct_message)): ?>
                                <?php echo e(substr($report->sct_message, 0, 60)); ?><?php echo e(strlen($report->sct_message) > 60 ? '...' : ''); ?>

                            <?php elseif(isset($report->sc_message)): ?>
                                <?php echo e(substr($report->sc_message, 0, 60)); ?><?php echo e(strlen($report->sc_message) > 60 ? '...' : ''); ?>

                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($report->sct_sms_cost)): ?>
                                <?php echo e(number_format($report->sct_sms_cost, 2)); ?>

                            <?php elseif(isset($report->sc_sms_cost)): ?>
                                <?php echo e(number_format($report->sc_sms_cost, 2)); ?>

                            <?php else: ?>
                                0.00
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($report->created_at)): ?>
                                <?php echo e($report->created_at->format('Y-m-d H:i:s')); ?>

                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $delivery_status = isset($report->sct_delivery_report) ? $report->sct_delivery_report : (isset($report->sc_delivery_report) ? $report->sc_delivery_report : null);
                            ?>
                            <?php if(!empty($delivery_status)): ?>
                                <span class="label label-success">Delivered</span>
                            <?php else: ?>
                                <span class="label label-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        
        <div class="row" style="margin-top: 10px;">
            <div class="col-md-12">
                <div class="pull-right">
                    <strong>Total Records:</strong> <?php echo e($reports->count()); ?>

                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning" style="margin: 20px 0;">
            <i class="ace-icon fa fa-warning"></i>
            <strong>No records found</strong> for this campaign.
            <br><small>Campaign ID: <?php echo e(isset($campaign) ? $campaign->id : 'N/A'); ?></small>
            <br><small>User ID: <?php echo e(Auth::id()); ?></small>
            <br><small>Total Submitted: <?php echo e(isset($campaign) ? $campaign->sci_total_submitted : '0'); ?></small>
        </div>
    <?php endif; ?>
</div>

<script>
// Initialize DataTable when this partial view is loaded
(function() {
    // Function to initialize the DataTable
    function initDataTable() {
        if (typeof $.fn.dataTable !== 'undefined') {
            // Check if table exists and has data
            if ($('#archived-details-table').length > 0 && $('#archived-details-table tbody tr').length > 0) {
                // Destroy any existing DataTable
                if ($.fn.dataTable.isDataTable('#archived-details-table')) {
                    $('#archived-details-table').DataTable().destroy();
                }
                
                // Initialize DataTable
                $('#archived-details-table').DataTable({
                    responsive: true,
                    "pageLength": 25,
                    "order": [[5, "desc"]],
                    "language": {
                        "emptyTable": "No records found",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "infoEmpty": "Showing 0 to 0 of 0 entries",
                        "infoFiltered": "(filtered from _MAX_ total entries)",
                        "lengthMenu": "Show _MENU_ entries",
                        "loadingRecords": "Loading...",
                        "processing": "Processing...",
                        "search": "Search:",
                        "zeroRecords": "No matching records found"
                    },
                    "columnDefs": [
                        {
                            "targets": [0],
                            "orderable": false,
                            "responsivePriority": 1
                        },
                        {
                            "targets": [1],
                            "responsivePriority": 2
                        },
                        {
                            "targets": [2],
                            "responsivePriority": 3
                        },
                        {
                            "targets": [3],
                            "responsivePriority": 4
                        },
                        {
                            "targets": [4],
                            "responsivePriority": 5
                        },
                        {
                            "targets": [5],
                            "responsivePriority": 6
                        },
                        {
                            "targets": [6],
                            "responsivePriority": 7
                        }
                    ]
                });
                console.log('DataTable initialized successfully');
                return true;
            } else {
                console.log('Table not found or no data');
                return false;
            }
        } else {
            console.log('DataTable library not loaded');
            return false;
        }
    }

    // Try to initialize immediately
    var initialized = initDataTable();
    
    // If not initialized, try again after a short delay
    if (!initialized) {
        setTimeout(function() {
            initDataTable();
        }, 300);
    }
    
    // Also try when the document is fully loaded
    if (document.readyState === 'complete') {
        setTimeout(function() {
            initDataTable();
        }, 100);
    } else {
        $(document).ready(function() {
            setTimeout(function() {
                initDataTable();
            }, 100);
        });
    }
})();
</script>