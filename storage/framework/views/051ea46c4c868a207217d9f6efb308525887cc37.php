

<?php $__env->startSection('reports_menu_class','open'); ?>
<?php $__env->startSection('sms_bill_report_menu_class','active'); ?>
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
	   <div class="col-xs-3">
		<input type="text" name="start_date" data-date-format="yyyy-mm-dd" autocomplete="off" value="<?php echo e($start_date); ?>" class="form-control" id="start" placeholder="Enter Start Date" >
	   </div>
		<div class="col-xs-3">
		<input type="text" name="end_date" data-date-format="yyyy-mm-dd" autocomplete="off" value="<?php echo e($end_date); ?>" class="form-control" id="end" placeholder="Enter End Date" >
	   </div>
		  <div class="col-xs-3">
			<button type="submit" class="btn btn-info btn-sm" name="searchbtn">Search</button>
			  <button type="button" onclick="downloadReport()" class="btn btn-success btn-sm" name="searchbtn"><i class="fa fa-file-excel-o"></i> Download CSV</button>
		  </div>
    	  </form>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="alert alert-info" style="margin-top: 15px;">
                Opening Balance: <strong><?php echo e(number_format($balancebd, 2)); ?></strong>
            </div>

            <table id="view_archived_report" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Campaign Type</th>
                    <th>Campaign Title</th>
                    <th>Submit time</th>
                    <th>SenderID</th>
                    <th>Submitted</th>
                    <th>Total sent</th>
                    <th>Charge</th>
                </tr>
                </thead>

                <tbody>
                <?php ($serial=1); ?>

                <?php ($total=0); ?>
                <?php ($total_sms=0); ?>
                <?php ($total_load=0); ?>
                <?php ($total_sub=0); ?>
                <?php ($total_sub_sms=0); ?>
                <?php ($total_sub_load=0); ?>
                <?php ($debit1=0); ?>
                <?php ($debit2=0); ?>
                <?php ($credit=0); ?>
                
                <?php if(!empty($transactions)): ?>
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                        <tr>
                            <?php if($transaction->asb_pay_mode == 4): ?>
                                <?php ($smsCampaign = $transaction->smsCampaignId); ?>
                                
                                <td><?php echo e($serial++); ?></td>
                                <td>SMS Campaign</td>
                                <td title="<?php echo e(@$smsCampaign->sci_campaign_id); ?>">
                                    <?php echo e(@$smsCampaign->sci_campaign_title ?? @$smsCampaign->sci_campaign_id); ?>

                                </td>
                                <td class="text-center">
                                    <?php if(!empty($transaction->asb_submit_time)): ?>
                                        <?php echo e($transaction->asb_submit_time->format('H:i a, d-M-Y')); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e(@$smsCampaign->sender->sir_sender_id); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e(@$smsCampaign->sci_total_submitted); ?>

                                    <?php ($total_sub_sms = $total_sub_sms + @$smsCampaign->sci_total_submitted); ?>
                                </td>
                                <td class="text-center">
                                    <?php echo e(@$smsCampaign->sci_total_submitted); ?>

                                    <?php ($total_sms = $total_sms + @$smsCampaign->sci_total_submitted); ?>
                                </td>
                                <td class="text-right">
                                    -<?php echo e(number_format($transaction->asb_debit, 2)); ?>

                                    <?php ($debit1 = $debit1 + $transaction->asb_debit); ?>
                                </td>
                            <?php elseif($transaction->asb_pay_mode == 5): ?>
                                <?php ($loadCampaign = $transaction->loadCampaignId); ?>

                                <td><?php echo e($serial++); ?></td>
                                <td>Load Campaign</td>
                                <td title="<?php echo e(@$loadCampaign->campaign_id); ?>">
                                    <?php echo e((@$loadCampaign->campaign_name != '')? @$loadCampaign->campaign_name:@$loadCampaign->campaign_id); ?>

                                </td>
                                <td class="text-center">
                                    <?php if(!empty($transaction->asb_submit_time)): ?>
                                        <?php echo e($transaction->asb_submit_time->format('H:i a, d-M-Y')); ?>

                                    <?php endif; ?>
                                </td>
                                <td>N/A</td>
                                <td class="text-center">
                                    <?php echo e(@$loadCampaign->total_number); ?>

                                    <?php ($total_sub_load = $total_sub_load + @$loadCampaign->total_number); ?>
                                </td>
                                <td class="text-center">
                                    <?php echo e(@$loadCampaign->total_number); ?>

                                    <?php ($total_load = $total_load + @$loadCampaign->total_number); ?>
                                </td>
                                <td class="text-right">
                                    -<?php echo e(number_format($transaction->asb_debit, 2)); ?>

                                    <?php ($debit2 = $debit2 + $transaction->asb_debit); ?>
                                </td>
                            <?php else: ?>
                                <td><?php echo e($serial++); ?></td>
                                <td>Deposit</td>
                                <td>
                                    Deposit
                                </td>
                                <td class="text-center">
                                    <?php if(!empty($transaction->asb_submit_time)): ?>
                                        <?php echo e($transaction->asb_submit_time->format('H:i a, d-M-Y')); ?>

                                    <?php endif; ?>
                                </td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td class="text-right">
                                    <?php echo e(number_format($transaction->asb_credit, 2)); ?>

                                    <?php ($credit = $credit + $transaction->asb_credit); ?>
                                </td>
                            <?php endif; ?>
                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <?php endif; ?>
                <?php ($sub_total = 0); ?>
                <?php ($sub_total2 = 0); ?>
                <?php ($amount = 0); ?>
                <?php ($sub_total = $total_sms + $total_load); ?>
                <?php ($sub_total2 = $total_sub_sms + $total_sub_load); ?>
                <?php ($amount = ($credit + $balancebd) - ($debit1 + $debit2)); ?>

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right"><b>Total:</b></td>
                        <td class="text-center">
                            <b><?php echo e($sub_total2); ?></b>
                        </td>
                        <td class="text-center">
                            <b>
                            <?php echo e($sub_total); ?>

                            </b>
                        </td>
                        <td class="text-right"><b><?php echo e(number_format($amount,2)); ?></b></td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- ------model view start-->
            <div id="my-modal" class="modal fade" tabindex="-1" style="display: none; z-index: 3000;">
                <div class="modal-dialog" style="width: 80%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 class="smaller lighter blue no-margin text-primary"> Today Report Details</h3>
                        </div>
                        <div class="modal-body">
                            <div id="SmsInformation"></div>
                        </div>
                    </div>
                </div><!-- /.modal-dialog -->
            </div>

            <div class="modal fade" id="api_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2000;">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Api Report Detail</h5>
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
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css">
    <style>
        @media(max-width:575px){
            .abcd{
                width: 130px;
            }
        }
        
        </style>    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
    <?php echo $__env->make('user.ajax.view_archived_report', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
        $('#view_archived_report').DataTable({
            responsive: true,
            pageLength: 50,
            lengthMenu: [[25, 50, 100, 500], [25, 50, 100, 500]],
            deferRender: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 2 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 1 },
                    { responsivePriority: 5, targets: 3 },
            ],
            order: []
        });
    } );
    </script>
    <script src="<?php echo e(asset('assets')); ?>/js/bootstrap-datepicker.min.js"></script>

    <script>
        // $('#view_archived_report').DataTable();
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

        function downloadReport() {
            let startDate = $("#start").val();
            let endDate = $("#end").val();
            let route = "<?php echo route('user.reports.bill-report-download'); ?>?start_date="+startDate+"&end_date="+endDate;
            window.open(route, '_blank');
        }
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>