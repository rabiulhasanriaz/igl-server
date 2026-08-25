

<?php $__env->startSection('flexiload_menu_class','open'); ?>
<?php $__env->startSection('flexiload_load_message_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Load Messages</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        Load Sim
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Messages
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('admin.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="tabs-nav-wrap">
                <ul class="tabs-nav">
                    <li class="tab-nav-link" data-target="#tab-one">Load Messages</li>
                    <li class="tab-nav-link" data-target="#tab-two">Pending Messages</li>
                    <li class="tab-nav-link" data-target="#tab-three">Operator Wise</li>
                    <li class="tab-nav-link" data-target="#tab-four">Airtel</li>
                    <li class="tab-nav-link" data-target="#tab-five">GP</li>
                    <li class="tab-nav-link" data-target="#tab-six">Banglalink</li>		
                    <li class="tab-nav-link" data-target="#tab-seven">Robi</li>		
                    <li class="tab-nav-link" data-target="#tab-eight">Teletalk</li>			
                 </ul>
               <div style="clear:both;"></div>
             </div><!-- ends tabs-nav-wrap -->	  
  
  
	     <div class="tabs-main-content">
		 
              <div id="tab-one" class="tab-content"><!-- Begins tab-one -->
                 <div class="tab-inner">
			        <table class="table table-striped table-bordered table-hover" id="reseller_list">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>User</th>
                            <th>Operator</th>
                            
                            <th>Message Body</th>
                            <th>Trx</th>
                            <th>Created Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php ($serial=1); ?>
                        <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php ($trx_id=0); ?>
                            <?php ($trx_id = \App\Model\LoadSimMessages::getTransactionIdFromMessage($msg->message)); ?>
                            <tr>
                                <td><?php echo e($serial++); ?></td>
                                <td title="<?php echo e(($msg->user_id != NULL)? $msg->user_name['company_name'] : ''); ?>">
                                    <?php if($msg->user_id != NULL): ?>
                                    <?php echo e($msg->user_id); ?>

                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td style="width: 120px;" class="text-center">
                                    <?php if($msg->operator_company == 'airtel'): ?>
                                        Airtel
                                        (<?php echo e($msg->sim_no); ?>)
                                    <?php elseif($msg->operator_company == 'gp'): ?>
                                        Grameen Phone
                                        (<?php echo e($msg->sim_no); ?>)
                                    <?php elseif($msg->operator_company == 'blink'): ?>
                                        Banglalink
                                        (<?php echo e($msg->sim_no); ?>)
                                    <?php elseif($msg->operator_company == 'robi'): ?>
                                        Robi
                                        (<?php echo e($msg->sim_no); ?>)
                                    <?php elseif($msg->operator_company == 'teletalk'): ?>
                                        Teletalk
                                        (<?php echo e($msg->sim_no); ?>)
                                    <?php endif; ?>
                                </td>
                                
                                <td style="width: 300px;"><?php echo e($msg->message); ?></td>
                                <td>
                                    <?php if($msg->sender == 'FlexiLoad' || $msg->sender == 8383 || $msg->sender == 'iTopUP' || $msg->sender == 'telecharge' || $msg->sender == 'Skitto' || $msg->sender == 'iRecharge'): ?>
                                    <?php echo e($trx_id); ?>

                                    <?php else: ?> 
                                    N/A
                                    <?php endif; ?>   
                                </td>
                                <td style="width: 100px;" class="text-center"><?php echo e($msg->created_at); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
			     </div>
              </div>
      
              <div id="tab-two" class="tab-content"><!-- Begins tab-two -->
                 <div class="tab-inner">
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-striped table-bordered table-hover" id="pending">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>User</th>
                                    <th>Operator</th>
                                    <th>Target Number</th>
                                    <th>Number Type</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php ($serial=1); ?>
                                <?php $__currentLoopData = $pendings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pending): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($serial++); ?></td>
                                        <td title="<?php echo e($pending->trx_user['company_name']); ?>"><?php echo e($pending->user_id); ?></td>
                                        <td style="width: 120px;" class="text-center">
                                            <?php if($pending->operator_id == 'airtel'): ?>
                                                Airtel
                                            <?php elseif($pending->operator_id == 'gp'): ?>
                                                Grameen Phone
                                            <?php elseif($pending->operator_id == 'blink'): ?>
                                                Banglalink
                                            <?php elseif($pending->operator_id == 'robi'): ?>
                                                Robi
                                            <?php elseif($pending->operator_id == 'teletalk'): ?>
                                                Teletalk    
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($pending->targeted_number); ?></td>
                                        <td>
                                            <?php if($pending->number_type == 1): ?>
                                                Prepaid
                                            <?php else: ?>
                                                Postpaid
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right"><?php echo e($pending->campaign_price); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <ul class="list-group" style="margin-top: 60px;">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span style="font-size: 20px;" class="text-warning">Pending Flexiload Balance</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span >Airtel</span>
                                  <span class="badge badge-primary badge-pill"><?php echo e($airtel_pending_bal); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span >Banglalink</span>
                                  <span class="badge badge-primary badge-pill"><?php echo e($bl_pending_bal); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                  <span >GrameenPhone</span>
                                  <span class="badge badge-primary badge-pill"><?php echo e($gp_pending_bal); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span >Robi</span>
                                    <span class="badge badge-primary badge-pill"><?php echo e($robi_pending_bal); ?></span>
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Teletalk</span>
                                    <span class="badge badge-primary badge-pill"><?php echo e($tt_pending_bal); ?></span>
                                  </li>
                                 <?php ($total = $airtel_pending_bal + $bl_pending_bal + $gp_pending_bal + $robi_pending_bal + $tt_pending_bal); ?>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="text-success">Total</span>
                                    <span class="badge badge-success badge-pill">
                                        <span style="font-size: 20px;"><?php echo e($total); ?></span>
                                    </span>
                                  </li>
                              </ul>
                        </div>
                    </div>
			     </div>
              </div>
	
              <div id="tab-three" class="tab-content"><!-- Begins tab-three -->
                 <div class="tab-inner">
                    
			        <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span >Airtel</span>
                                  <span class="badge badge-primary badge-pill"><?php echo e($airtel_operator); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span >Banglalink</span>
                                  <span class="badge badge-primary badge-pill"><?php echo e($bl_operator); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                  <span >GrameenPhone</span>
                                  <span class="badge badge-primary badge-pill"><?php echo e($gp_operator); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span >Robi</span>
                                    <span class="badge badge-primary badge-pill"><?php echo e($robi_operator); ?></span>
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Teletalk</span>
                                    <span class="badge badge-primary badge-pill"><?php echo e($tt_operator); ?></span>
                                  </li>
                                 <?php ($total = $airtel_operator + $bl_operator + $gp_operator + $robi_operator + $tt_operator); ?>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="text-success">Total</span>
                                    <span class="badge badge-success badge-pill">
                                        <span style="font-size: 20px;"><?php echo e($total); ?></span>
                                    </span>
                                  </li>
                              </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span >Airtel</span>
                                  <span class="badge badge-primary badge-pill"><?php echo e($airtel_pending); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span >Banglalink</span>
                                  <span class="badge badge-primary badge-pill"><?php echo e($bl_pending); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                  <span >GrameenPhone</span>
                                  <span class="badge badge-primary badge-pill"><?php echo e($gp_pending); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span >Robi</span>
                                    <span class="badge badge-primary badge-pill"><?php echo e($robi_pending); ?></span>
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Teletalk</span>
                                    <span class="badge badge-primary badge-pill"><?php echo e($tt_pending); ?></span>
                                  </li>
                                 <?php ($total = $airtel_pending + $bl_pending + $gp_pending + $robi_pending + $tt_pending); ?>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="text-warning">Total</span>
                                    <span class="badge badge-warning badge-pill">
                                        <span style="font-size: 20px;"><?php echo e($total); ?></span>
                                    </span>
                                  </li>
                              </ul>
                        </div>
                    </div>
                 </div>
                 
              </div>
              
              <div id="tab-four" class="tab-content"><!-- Begins tab-one -->
                <div class="tab-inner">
                   <table class="table table-striped table-bordered table-hover" id="four">
                       <thead>
                       <tr>
                           <th>SL</th>
                           <th>User</th>
                           <th>Operator</th>
                           <th>Message Body</th>
                           <th>Trx</th>
                           <th>Created Date</th>
                       </tr>
                       </thead>
                       <tbody>
                       <?php ($serial=1); ?>
                       <?php $__currentLoopData = $airtel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <?php ($trx_id=0); ?>
                       <?php ($trx_id = \App\Model\LoadSimMessages::getTransactionIdFromMessage($msg->message)); ?>
                           <tr>
                               <td><?php echo e($serial++); ?></td>
                               <td title="<?php echo e(($msg->user_id != NULL)? $msg->user_name['company_name'] : ''); ?>">
                                <?php if($msg->user_id != NULL): ?>
                                <?php echo e($msg->user_id); ?>

                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                               </td>
                               <td style="width: 120px;" class="text-center">
                                       Airtel
                                       (<?php echo e($msg->sim_no); ?>)
                               </td>
                               
                               <td style="width: 300px;"><?php echo e($msg->message); ?></td>
                               <td>
                                   <?php if($msg->sender == 'FlexiLoad' || $msg->sender == 8383 || $msg->sender == 'iTopUP' || $msg->sender == 'telecharge' || $msg->sender == 'iRecharge'): ?>
                                   <?php echo e($trx_id); ?>

                                   <?php else: ?> 
                                   N/A
                                   <?php endif; ?>   
                               </td>
                               <td style="width: 100px;" class="text-center"><?php echo e($msg->created_at); ?></td>
                           </tr>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       </tbody>
                   </table>
                </div>
             </div>

             <div id="tab-five" class="tab-content"><!-- Begins tab-one -->
                <div class="tab-inner">
                   <table class="table table-striped table-bordered table-hover" id="five">
                       <thead>
                       <tr>
                           <th>SL</th>
                           <th>User</th>
                           <th>Operator</th>
                           <th>Message Body</th>
                           <th>Trx</th>
                           <th>Created Date</th>
                       </tr>
                       </thead>
                       <tbody>
                       <?php ($serial=1); ?>
                       <?php $__currentLoopData = $grameen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <?php ($trx_id=0); ?>
                       <?php ($trx_id = \App\Model\LoadSimMessages::getTransactionIdFromMessage($msg->message)); ?>
                           <tr>
                               <td><?php echo e($serial++); ?></td>
                               <td title="<?php echo e(($msg->user_id != NULL)? $msg->user_name['company_name'] : ''); ?>">
                                <?php if($msg->user_id != NULL): ?>
                                <?php echo e($msg->user_id); ?>

                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                               </td>
                               <td style="width: 120px;" class="text-center">
                                       Grameen Phone
                                    (<?php echo e($msg->sim_no); ?>)
                               </td>
                               <td style="width: 300px;"><?php echo e($msg->message); ?></td>
                               <td>
                                   <?php if($msg->sender == 'FlexiLoad' || $msg->sender == 8383 || $msg->sender == 'iTopUP' || $msg->sender == 'telecharge' || $msg->sender == 'iRecharge'): ?>
                                   <?php echo e($trx_id); ?>

                                   <?php else: ?> 
                                   N/A
                                   <?php endif; ?>   
                               </td>
                               <td style="width: 100px;" class="text-center"><?php echo e($msg->created_at); ?></td>
                           </tr>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       </tbody>
                   </table>
                </div>
             </div>

             <div id="tab-six" class="tab-content"><!-- Begins tab-one -->
                <div class="tab-inner">
                   <table class="table table-striped table-bordered table-hover" id="six">
                       <thead>
                       <tr>
                           <th>SL</th>
                           <th>User</th>
                           <th>Operator</th>
                           <th>Message Body</th>
                           <th>Trx</th>
                           <th>Created Date</th>
                       </tr>
                       </thead>
                       <tbody>
                       <?php ($serial=1); ?>
                       <?php $__currentLoopData = $blink; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <?php ($trx_id=0); ?>
                       <?php ($trx_id = \App\Model\LoadSimMessages::getTransactionIdFromMessage($msg->message)); ?>
                           <tr>
                               <td><?php echo e($serial++); ?></td>
                               <td title="<?php echo e(($msg->user_id != NULL)? $msg->user_name['company_name'] : ''); ?>">
                                <?php if($msg->user_id != NULL): ?>
                                <?php echo e($msg->user_id); ?>

                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                               </td>
                               <td style="width: 120px;" class="text-center">
                                       Banglalink
                                    (<?php echo e($msg->sim_no); ?>)
                               </td>
                               <td style="width: 300px;"><?php echo e($msg->message); ?></td>
                               <td>
                                   <?php if($msg->sender == 'FlexiLoad' || $msg->sender == 8383 || $msg->sender == 'iTopUP' || $msg->sender == 'telecharge' || $msg->sender == 'iRecharge'): ?>
                                   <?php echo e($trx_id); ?>

                                   <?php else: ?> 
                                   N/A
                                   <?php endif; ?>   
                               </td>
                               <td style="width: 100px;" class="text-center"><?php echo e($msg->created_at); ?></td>
                           </tr>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       </tbody>
                   </table>
                </div>
             </div>

             <div id="tab-seven" class="tab-content"><!-- Begins tab-one -->
                <div class="tab-inner">
                   <table class="table table-striped table-bordered table-hover" id="seven">
                       <thead>
                       <tr>
                           <th>SL</th>
                           <th>User</th>
                           <th>Operator</th>
                           <th>Message Body</th>
                           <th>Trx</th>
                           <th>Created Date</th>
                       </tr>
                       </thead>
                       <tbody>
                       <?php ($serial=1); ?>
                       <?php $__currentLoopData = $robi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <?php ($trx_id=0); ?>
                       <?php ($trx_id = \App\Model\LoadSimMessages::getTransactionIdFromMessage($msg->message)); ?>
                           <tr>
                               <td><?php echo e($serial++); ?></td>
                               <td title="<?php echo e(($msg->user_id != NULL)? $msg->user_name['company_name'] : ''); ?>">
                                <?php if($msg->user_id != NULL): ?>
                                <?php echo e($msg->user_id); ?>

                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                               </td>
                               <td style="width: 120px;" class="text-center">
                                   
                                       Robi
                                   (<?php echo e($msg->sim_no); ?>)
                               </td>
                               <td style="width: 300px;"><?php echo e($msg->message); ?></td>
                               <td>
                                   <?php if($msg->sender == 'FlexiLoad' || $msg->sender == 8383 || $msg->sender == 'iTopUP' || $msg->sender == 'telecharge' || $msg->sender == 'iRecharge'): ?>
                                   <?php echo e($trx_id); ?>

                                   <?php else: ?> 
                                   N/A
                                   <?php endif; ?>   
                               </td>
                               <td style="width: 100px;" class="text-center"><?php echo e($msg->created_at); ?></td>
                           </tr>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       </tbody>
                   </table>
                </div>
             </div>

             <div id="tab-eight" class="tab-content"><!-- Begins tab-one -->
                <div class="tab-inner">
                   <table class="table table-striped table-bordered table-hover" id="eight">
                       <thead>
                       <tr>
                           <th>SL</th>
                           <th>User</th>
                           <th>Operator</th>
                           <th>Message Body</th>
                           <th>Trx</th>
                           <th>Created Date</th>
                       </tr>
                       </thead>
                       <tbody>
                       <?php ($serial=1); ?>
                       <?php $__currentLoopData = $teletalk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <?php ($trx_id=0); ?>
                       <?php ($trx_id = \App\Model\LoadSimMessages::getTransactionIdFromMessage($msg->message)); ?>
                           <tr>
                               <td><?php echo e($serial++); ?></td>
                               <td title="<?php echo e(($msg->user_id != NULL)? $msg->user_name['company_name'] : ''); ?>">
                                <?php if($msg->user_id != NULL): ?>
                                <?php echo e($msg->user_id); ?>

                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                               </td>
                               <td style="width: 120px;" class="text-center">
                                       Teletalk 
                                       (<?php echo e($msg->sim_no); ?>)   
                               </td>
                               <td style="width: 300px;"><?php echo e($msg->message); ?></td>
                               <td>
                                   <?php if($msg->sender == 'FlexiLoad' || $msg->sender == 8383 || $msg->sender == 'iTopUP' || $msg->sender == 'telecharge' || $msg->sender == 'iRecharge'): ?>
                                   <?php echo e($trx_id); ?>

                                   <?php else: ?> 
                                   N/A
                                   <?php endif; ?>   
                               </td>
                               <td style="width: 100px;" class="text-center"><?php echo e($msg->created_at); ?></td>
                           </tr>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       </tbody>
                   </table>
                </div>
             </div>

             

              
			
	        <div style="clear:both;"></div>
	      </div><!-- # tabs-main-content -->	

            

        </div><!-- /.col -->
    </div><!-- /.row -->


<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom_style'); ?>



<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
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




<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>


    
    <script type="text/javascript">
        $('#reseller_list').DataTable();
        $('#pending').DataTable();
        $('#four').DataTable();
        $('#five').DataTable();
        $('#six').DataTable();
        $('#seven').DataTable();
        $('#eight').DataTable();
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