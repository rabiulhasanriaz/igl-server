

<?php $__env->startSection('reports_menu_class','open'); ?>
<?php $__env->startSection('view_dlr_menu_class','open'); ?>
<?php $__env->startSection('archived_report_menu_class','active'); ?>

<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
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
            Archived SMS
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

<form action="" method="get">
    <input type="hidden" name="_token" value="" id="_token">
    <div class="row">
        <div class="col-xs-3">
            <input type="text" name="start_date" data-date-format="yyyy-mm-dd" autocomplete="off" value="<?php echo e($start_date); ?>" class="form-control" id="start" placeholder="Enter Start Date">
        </div>
        <div class="col-xs-3">
            <input type="text" name="end_date" data-date-format="yyyy-mm-dd" autocomplete="off" value="<?php echo e($end_date); ?>" class="form-control" id="end" placeholder="Enter End Date">
        </div>
        <div class="col-xs-3">
            <button type="submit" class="btn btn-info btn-sm" name="searchbtn">Search</button>
            <button type="button" onclick="downloadSummaryReport()" class="btn btn-success btn-sm">Download CSV</button>
            <button type="button" onclick="downloadDetailedReport()" class="btn btn-danger btn-sm">Download Details</button>
        </div>
    </div>
</form>

<?php if(!empty($reportError)): ?>
    <div class="alert alert-danger" style="margin-top: 15px;"><?php echo e($reportError); ?></div>
<?php elseif(empty($start_date) || empty($end_date)): ?>
    <div class="alert alert-info" style="margin-top: 15px;">Select a start date and end date, then click Search.</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table id="view_archived_report" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Campaign Title</th>
                    <th class="hidden-600">Submit time</th>
                    <th>SenderID</th>
                    <th>SMS Count</th>
                    <th>Total sent</th>
                    <th>Charge</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php ($serial=1); ?>
                
                <?php $__currentLoopData = $archived_campaigns_by_api; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $api_campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($serial++); ?></td>
                    <td title="<?php echo e($api_campaign->sci_campaign_id); ?>">API - <?php echo e($api_campaign->sci_campaign_title ?? $api_campaign->sci_campaign_id); ?></td>
                    <td><?php echo e($api_campaign->sci_targeted_time->format('H:i a, d-M-Y')); ?></td>
                    <td><?php echo e(optional($api_campaign->sender)->sir_sender_id); ?></td>
                    <td class="text-center"><?php echo e($api_campaign->report_sms_count); ?></td>
                    <td class="text-center"><?php echo e($api_campaign->report_recipient_count); ?></td>
                    <td class="text-right">BDT <?php echo e(number_format($api_campaign->sci_total_cost, 2)); ?></td>
                    <td>
                        <label>
                            <a href="" data-toggle="modal" data-target="#api_modal" onclick="api_reports('<?php echo e($start_date); ?>', '<?php echo e($end_date); ?>')">
                                View
                            </a>
                        </label>
                        |
                        <label>
                            <a href="#" onclick="downloadApiReport()" class="btn-none-download">Download</a>
                        </label>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <?php $__currentLoopData = $archived_campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $archived_campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($serial++); ?></td>
                    <td title="<?php echo e($archived_campaign->sci_campaign_id); ?>"><?php echo e($archived_campaign->sci_campaign_title); ?></td>
                    <td>
                        <?php if(!empty($archived_campaign->sci_targeted_time)): ?>
                            <?php echo e($archived_campaign->sci_targeted_time->format('H:i a, d-M-Y')); ?>

                        <?php endif; ?>
                    </td>
                    <td><?php echo e(optional($archived_campaign->sender)->sir_sender_id); ?></td>
                    <td class="text-center"><?php echo e($archived_campaign->report_recipient_count); ?></td>
                    <td class="text-center"><?php echo e($archived_campaign->sci_total_submitted); ?></td>
                    <td class="text-right">BDT <?php echo e(number_format($archived_campaign->sci_total_cost, 2)); ?>

                        <?php if($cost->has($archived_campaign->id)): ?>
                            (<?php echo e($cost->get($archived_campaign->id)); ?>)
                        <?php endif; ?>
                    </td>
                    <td>
                        <label>
                            <a href="#my-modal" onclick="show_archived_details('<?php echo e($archived_campaign->id); ?>')" role="button" data-toggle="modal" class="btn-none-edit CampaignId_one">
                                View
                            </a>
                        </label>
                        |
                        <label>
                            <a href="<?php echo e(route('user.reports.download_archived_report', $archived_campaign->id)); ?>" target="_blank" class="btn-none-download">
                                Download
                            </a>
                        </label>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <!-- Modal for Regular Campaign Details -->
        <div id="my-modal" class="modal fade" tabindex="-1" style="display: none; z-index: 3000;">
            <div class="modal-dialog" style="width: 80%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin text-primary">Report Details</h3>
                    </div>
                    <div class="modal-body">
                        <div id="SmsInformation"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for API Campaign Details -->
        <div class="modal fade" id="api_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2000;">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">API Report Detail</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="apiReportDetails">
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

<?php $__env->startSection('custom_style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/bootstrap-datepicker3.min.css"/>
<link href="<?php echo e(asset('assets/datatable/jquery.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
<link href="<?php echo e(asset('assets/datatable/rowReorder.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
<link href="<?php echo e(asset('assets/datatable/responsive.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
<style>
    @media(max-width:575px){
        .abcd{
            width: 130px;
        }
    }
    .btn-group-sm .btn {
        margin-right: 5px;
    }
</style>    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
<?php echo $__env->make('user.ajax.view_archived_report', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#view_archived_report').DataTable({
            responsive: true,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 2 },
                { responsivePriority: 3, targets: 4 },
                { responsivePriority: 4, targets: 1 },
                { responsivePriority: 5, targets: 3 },
            ]
        });
    });
</script>

<script src="<?php echo e(asset('assets')); ?>/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function () {
        $('#start').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        $('#end').datepicker({
            autoclose: true,
            todayHighlight: true
        });
    });
</script>

<script type="text/javascript">
    // Function to view API reports
    function api_reports(start_date, end_date) {
        let url = "<?php echo e(route('user.reports.api-report-ajax')); ?>";
        var _token = $("#_token").val();
        $.ajax({
            type: "GET",
            url: url,
            data: { _token: _token, start_date: start_date, end_date: end_date },
            success: function (result) {
                $("#apiReportDetails").html(result);
                $("#api_modal").modal("show");
            }
        });
    }

    // Function to download Summary Report (Campaigns only)
    function downloadSummaryReport() {
        let startDate = $("#start").val();
        let endDate = $("#end").val();
        
        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            alert('Start date must be before end date');
            return;
        }
        
        let route = "<?php echo e(route('user.reports.download-archived-report-csv')); ?>" + 
                    "?start_date=" + startDate + 
                    "&end_date=" + endDate;
        window.open(route, '_blank');
    }

    // Function to download Detailed Report (All SMS records)
    function downloadDetailedReport() {
        let startDate = $("#start").val();
        let endDate = $("#end").val();
        
        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            alert('Start date must be before end date');
            return;
        }
        
        let route = "<?php echo e(route('user.reports.download-archived-report-details-pdf')); ?>" + 
                    "?start_date=" + startDate + 
                    "&end_date=" + endDate;
        window.open(route, '_blank');
    }

    // Function to download API Report
    function downloadApiReport() {
        let startDate = $("#start").val();
        let endDate = $("#end").val();
        
        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            alert('Start date must be before end date');
            return;
        }
        
        let route = "<?php echo e(route('user.reports.download-api-report-pdf')); ?>" + 
                    "?start_date=" + startDate + 
                    "&end_date=" + endDate;
        window.open(route, '_blank');
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>