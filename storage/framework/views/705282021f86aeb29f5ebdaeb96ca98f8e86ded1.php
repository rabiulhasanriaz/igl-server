

<?php $__env->startSection('reports_menu_class','open'); ?>
<?php $__env->startSection('campaign_dlr_menu_class','open'); ?>
<?php $__env->startSection('archived_campaign_menu_class','active'); ?>
<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
	</li>
	<li class="active">Reports SMS</li>
</ul>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
<h1>
	Reports & Statistics
	<i class="ace-icon fa fa-angle-double-right"></i>
	Campaign DLR
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Archived Campaign
	</small>
</h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

		<table class="table table-striped table-bordered table-hover" id="archived-campaign-table">
			<thead>
			<tr>
				<th>SL</th>
				<th>Campaign Title</th>
				<th class="hidden-600">Submit time</th>
				<th>SenderID</th>
				<th>Submitted</th>
				<th>Total sent</th>
				<th>Charge</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
			</thead>

			<tbody>
			<?php
				$serial = 1;
			?>
			<?php $__currentLoopData = $archived_campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $archived_campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($serial++); ?></td>
					<td title="<?php echo e($archived_campaign->sci_campaign_id); ?>"><?php echo e($archived_campaign->sci_campaign_title); ?></td>
					<td><?php echo e($archived_campaign->sci_targeted_time->format('H:i a, d-M-Y')); ?></td>
					<td><?php echo e($archived_campaign->sender->sir_sender_id ?? 'N/A'); ?></td>
					<td><?php echo e($archived_campaign->sci_total_submitted); ?></td>
					<td><?php echo e($archived_campaign->sci_total_submitted); ?></td>
					<td>BDT <?php echo e(number_format($archived_campaign->sci_total_cost, 2)); ?></td>
					<td>
						<?php
							$status = $archived_campaign->sci_campaign_status ?? 0;
						?>
						<?php if($status == 1): ?>
							<span class="label label-success">Completed</span>
						<?php elseif($status == 2): ?>
							<span class="label label-danger">Rejected</span>
						<?php elseif($status == 0): ?>
							<span class="label label-warning">Pending</span>
						<?php else: ?>
							<span class="label label-default">Unknown</span>
						<?php endif; ?>
					</td>
					<td>
						<div class="btn-group">
							<a href="#my-modal" onclick="showArchivedDetails('<?php echo e($archived_campaign->id); ?>')"
							   role="button" data-toggle="modal"
							   class="btn btn-xs btn-info">
								<i class="ace-icon fa fa-eye bigger-120"></i> View
							</a>
							<a href="<?php echo e(route('user.reports.download_archived_report', $archived_campaign->id)); ?>" 
							   target="_blank" 
							   class="btn btn-xs btn-success">
								<i class="ace-icon fa fa-download bigger-120"></i> Download
							</a>
						</div>
					</td>
				</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			</tbody>
		</table>
			
		<!-- Modal for viewing archived report details -->
		<div id="my-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style="width: 90%; max-width: 1200px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
						<h3 class="smaller lighter blue no-margin text-primary">
							<i class="ace-icon fa fa-file-text-o bigger-130"></i> Archive Report Details
						</h3>
						<span id="campaign-title" class="pull-right text-muted" style="margin-top: -22px;"></span>
					</div>
					<div class="modal-body" style="padding: 20px; max-height: 500px; overflow-y: auto;">
						<div id="SmsInformation">
							<div class="text-center" style="padding: 40px 0;">
								<i class="ace-icon fa fa-spinner fa-spin fa-3x text-primary"></i>
								<p class="text-muted" style="margin-top: 10px;">Loading data...</p>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-sm btn-default" data-dismiss="modal">
							<i class="ace-icon fa fa-times"></i> Close
						</button>
					</div>
				</div>
			</div>
		</div>	

	</div>
</div>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/datatable/jquery.dataTables.min.css')); ?>" type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('assets/datatable/rowReorder.dataTables.min.css')); ?>" type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('assets/datatable/responsive.dataTables.min.css')); ?>" type="text/css">
<style>
    @media(max-width:575px){
        .abcd{
            width: 130px;
        }
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    .modal-dialog {
        margin-top: 50px;
    }
    #SmsInformation {
        min-height: 100px;
    }
    .label {
        padding: 5px 10px;
        font-size: 12px;
    }
    .table > tbody > tr > td {
        vertical-align: middle;
    }
</style>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_script'); ?>
<script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.min.js"></script>
<script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    // Initialize DataTable for the main table
    if ($.fn.dataTable) {
        $('#archived-campaign-table').DataTable({
            responsive: true,
            "pageLength": 25,
            "order": [[2, "desc"]],
            "language": {
                "emptyTable": "No archived campaigns found",
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
                },
                {
                    "targets": [7],
                    "responsivePriority": 8
                },
                {
                    "targets": [8],
                    "orderable": false,
                    "responsivePriority": 1
                }
            ]
        });
    }
});

// Function to show archived details
function showArchivedDetails(campaign_id) {
    // Show loading spinner in modal
    $('#SmsInformation').html('<div class="text-center" style="padding: 40px 0;"><i class="ace-icon fa fa-spinner fa-spin fa-3x text-primary"></i><p class="text-muted" style="margin-top: 10px;">Loading archived report details...</p></div>');
    
    // Get the CSRF token
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    $.ajax({
        url: "<?php echo e(route('ajax.show_archived_report')); ?>",
        type: 'GET',
        data: {
            campaign_id: campaign_id
        },
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        xhrFields: {
            withCredentials: true
        },
        success: function(response) {
            // Insert the response HTML
            $('#SmsInformation').html(response);
            
            // Re-initialize DataTable after content is loaded
            setTimeout(function() {
                if ($.fn.dataTable) {
                    // Destroy existing DataTable if any
                    if ($.fn.dataTable.isDataTable('#archived-details-table')) {
                        $('#archived-details-table').DataTable().destroy();
                    }
                    
                    // Check if table exists and has rows
                    if ($('#archived-details-table').length > 0 && $('#archived-details-table tbody tr').length > 0) {
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
                    }
                }
            }, 200);
        },
        error: function(xhr, status, error) {
            var errorMsg = xhr.statusText || 'Please try again';
            if (xhr.status === 419) {
                errorMsg = 'Session expired. Please refresh the page.';
            } else if (xhr.status === 401) {
                errorMsg = 'Please refresh the page and try again.';
            } else if (xhr.status === 404) {
                errorMsg = 'Page not found. Please check the URL.';
            }
            $('#SmsInformation').html('<div class="alert alert-danger"><i class="ace-icon fa fa-exclamation-triangle"></i> <strong>Error loading data:</strong> ' + errorMsg + '<br><small>Status: ' + status + ' - ' + error + '</small><br><small>Please try refreshing the page.</small></div>');
        }
    });
}

// Handle modal close - clear content to prevent showing old data
$('#my-modal').on('hidden.bs.modal', function () {
    $('#SmsInformation').html('<div class="text-center" style="padding: 40px 0;"><i class="ace-icon fa fa-file-text-o fa-3x text-muted"></i><p class="text-muted" style="margin-top: 10px;">Select a campaign to view details</p></div>');
    $('#campaign-title').text('');
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>