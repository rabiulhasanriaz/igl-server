<?php $__env->startSection('sms_flexi_report_class','open'); ?>
<?php $__env->startSection('operator_class','active'); ?>

<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li class="active"><?php echo e($days); ?> Days Reports</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>
    <form class="form-inline" action="" method="get">
        <input type="hidden" name="_token" value="" id="_token">
        <div class="form-group">
          <label for="email">Starting Date</label>
          <input type="text" value="<?php echo e($start); ?>" data-date-format="yyyy-mm-dd" name="start_date" class="form-control" id="start">
        </div>
        <div class="form-group">
          <label for="pwd">Ending Date</label>
          <input type="text" value="<?php echo e($end); ?>" data-date-format="yyyy-mm-dd" name="end_date" class="form-control" id="end">
        </div>
        <button type="submit" class="btn btn-info btn-sm" name="searchbtn">Search</button>
    </form>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('admin.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="tabs-nav-wrap">
                <ul class="tabs-nav">
                    <li class="tab-nav-link" data-target="#tab-one">Masking/Non-Masking</li>
                    <li class="tab-nav-link" data-target="#tab-two">Flexiload</li>		
                 </ul>
               <div style="clear:both;"></div>
             </div><!-- ends tabs-nav-wrap -->	  
  
  
	     <div class="tabs-main-content">
		 
              <div id="tab-one" class="tab-content"><!-- Begins tab-one -->
                 <div class="tab-inner">
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="list-group">
                                <li class="list-group-item active text-center">Last <?php echo e($days); ?>Days Masking SMS Reports</li>
                            </ul>
                            <table id="masking" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Operator</th>
                                        <th>Total Masking SMS</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php ($sl=0); ?>
                                    <?php ($total_op_sms=0); ?>
                                    <?php ($total_op_cost=0); ?>
                                    <?php $__currentLoopData = $sms_report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php ($total_op_sms += $sms->total); ?>
                                    <?php ($total_op_cost += $sms->total_cost); ?>
                                    <tr>
                                        <td><?php echo e(++$sl); ?></td>
                                        <td title=""><?php echo e($sms->operator->ope_operator_name); ?></td>
                                        <td class="text-center"><?php echo e($sms->total); ?></td>
                                        <td class="text-right"><?php echo e(number_format($sms->total_cost,2)); ?></td>
                                    </tr>  
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-center"><?php echo e($total_op_sms); ?></th>
                                        <th class="text-right"><?php echo e(number_format($total_op_cost,2)); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <ul class="list-group">
                                <li class="list-group-item active text-center">Last <?php echo e($days); ?>Days Non-Maskings SMS Reports</li>
                            </ul>
                            <table id="non-masking" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Operator</th>
                                        <th>Total Non-Masking SMS</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php ($sl=0); ?>
                                    <?php ($total_op_sms=0); ?>
                                    <?php ($total_op_cost=0); ?>
                                    <?php $__currentLoopData = $nonMaskingReport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php ($total_op_sms += $sms->total); ?>
                                    <?php ($total_op_cost += $sms->total_cost); ?>
                                    <tr>
                                        <td><?php echo e(++$sl); ?></td>
                                        <td title="">
                                            <?php if($sms->sci_sender_operator == 1): ?>
                                                Robi/Airtel Non-Masking
                                            <?php elseif($sms->sci_sender_operator == 2): ?>
                                                GP Non-Masking
                                            <?php elseif($sms->sci_sender_operator == 3): ?>
                                                Banglalink Non-Masking
                                            <?php elseif($sms->sci_sender_operator == 4): ?>
                                                Teletalk Non-Masking
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo e($sms->total); ?></td>
                                        <td class="text-right"><?php echo e(number_format($sms->total_cost,2)); ?></td>
                                    </tr>  
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-center"><?php echo e($total_op_sms); ?></th>
                                        <th class="text-right"><?php echo e(number_format($total_op_cost,2)); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
			     </div>
              </div>
      
              <div id="tab-two" class="tab-content"><!-- Begins tab-two -->
                 <div class="tab-inner">
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="list-group">
                                <li class="list-group-item active text-center">Last <?php echo e($days); ?>Days Flexi Reports</li>
                            </ul>
                            <table id="flexi" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>CellPhone</th>
                                        <th>Total Number</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php ($sl=0); ?>
                                   <?php ($total_op_flexi=0); ?>
                                   <?php ($total_op_cost=0); ?>
                                   <?php $__currentLoopData = $flexi_report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flexi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                   <?php ($total_op_flexi += $flexi->total); ?>
                                   <?php ($total_op_cost += $flexi->total_cost); ?>
                                   <tr>
                                        <td><?php echo e(++$sl); ?></td>
                                        <td title="">
                                            <?php if($flexi->operator_id == 'airtel'): ?>
                                                Airtel
                                            <?php elseif($flexi->operator_id == 'blink'): ?>
                                                Banglalink
                                            <?php elseif($flexi->operator_id == 'gp'): ?>
                                                GrameenPhone
                                            <?php elseif($flexi->operator_id == 'gpst'): ?>
                                                Skitto
                                            <?php elseif($flexi->operator_id == 'robi'): ?>
                                                Robi
                                            <?php elseif($flexi->operator_id == 'teletalk'): ?>
                                                Teletalk 
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo e($flexi->total); ?></td>
                                        <td class="text-right"><?php echo e(number_format($flexi->total_cost,2)); ?></td>
                                    </tr>
                                   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-center"><?php echo e($total_op_flexi); ?></th>
                                        <th class="text-right"><?php echo e(number_format($total_op_cost,2)); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
			     </div>
              </div>
	        <div style="clear:both;"></div>
	      </div><!-- # tabs-main-content -->	

            

        </div><!-- /.col -->
    </div><!-- /.row -->


<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom_style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/bootstrap-datepicker3.min.css"/>
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css" rel="stylesheet" />
    <style>
        .tabs-nav-wrap {padding:10px 0; text-align: center;}
        .tabs-nav-wrap ul {display: inline-block; list-style: none;}

        .tabs-nav-wrap ul li {
        list-style: none;
        display: inline-block;

        margin:0 3px;
        }
        .tabs-nav li {
        list-style: none;
        display: block;
        padding:10px 20px;
        background: #736565;
        font-size: 16px;
        font-weight: 300;
        cursor: pointer;
        color: #fff;
        text-align: left;
        text-decoration: none;
        border:1px solid #615656;
        }

        .tabs-nav .tab-nav-link.current,
        .tabs-nav .tab-nav-link:hover {
        border:1px solid #5eaace;
        background: #74c1e4;
        }

        .tab-content {
            padding:20px 0; 
            /* text-align: center;  */
            display:none;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
<script src="<?php echo e(asset('assets')); ?>/js/bootstrap-datepicker.min.js"></script>
    
    <script type="text/javascript">
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

     $(document).ready(function() {
            var table = $('#masking').DataTable( {
                
                responsive: true
            } );
        } );
        $(document).ready(function() {
            var table = $('#non-masking').DataTable( {
                
                responsive: true
            } );
        } );
        $(document).ready(function() {
            var table = $('#flexi').DataTable( {
                
                responsive: true
            } );
        } );
        function submitLimitForm(formName){
            if(confirm('Are you Sure')) {
                $("#" + formName).submit();
            }
        }

        function copy(that){
        var inp =document.createElement('input');
        document.body.appendChild(inp);
        inp.value =that;
        inp.select();
        document.execCommand('copy',false);
        inp.remove();
        }

        $(function() {
        $('.tab-content:first-child').show();
        $('.tab-nav-link').bind('click', function(e) {
            $this = $(this);
            $tabs = $this.parent().parent().next();
            $target = $($this.data("target")); // get the target from data attribute
            $this.siblings().removeClass('current');
            $target.siblings().css("display", "none")
            $this.addClass('current');
            $target.fadeIn("fast");
        
        });
        $('.tab-nav-link:first-child').trigger('click');
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>