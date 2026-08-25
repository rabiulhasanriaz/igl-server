<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		table{
			border-collapse: collapse;
			width: 100%;
		}
		table, th, td{
			border: 1px solid lightgray;
		}
		th, td{
			padding: 3px;
		}

		@page  { margin: 150px 50px; } 

	    #header { 
			position: fixed; 
			left: 0px; 
			top: -120px; 
			right: 0px; 
			height: 150px; 
		    text-align: center; 
		}
		#footer { 
			position: fixed; 
			left: 0px; 
			bottom: -100px; 
			right: 0px; 
			height: 70px; 
		}
	    #footer .page:after { content: counter(page, upper-roman); }
	</style>
</head>
<body>
	<div id="header">
			<div class="logo">
				<img src="<?php echo e(asset('assets/uploads/default.png')); ?>" alt="">
			</div>
			<div id="footer">
				<p style="font-size: 20px;">We Are Authorized Aggregator of: 
					<img src="<?php echo e(asset('assets/images/operator_icon')); ?>/airtel.png" style="width: 20px; height: 20px; margin-right: 5px;">
					<img src="<?php echo e(asset('assets/images/operator_icon')); ?>/banglalink.png" style="width: 20px; height: 20px; margin-right: 5px;">
					<img src="<?php echo e(asset('assets/images/operator_icon')); ?>/gp.png" style="width: 20px; height: 20px; margin-right: 5px;">
					<img src="<?php echo e(asset('assets/images/operator_icon')); ?>/robi.png" style="width: 20px; height: 20px; margin-right: 5px;">
					<img src="<?php echo e(asset('assets/images/operator_icon')); ?>/teletalk.png" style="width: 20px; height: 20px;">
				</p>
			</div>
	</div>
	<h3 style="text-align: center;">SMS Sent Details</h3>
	<table>
		<thead>
			<tr>
				<th style="text-align: center;">Sl</th>
				<th style="text-align: center;">Campaign Name</th>
				<th style="text-align: center;">Date & Time</th>
				<th style="text-align: center;">SMS Quantity</th>
				<th style="text-align: center;">SMS Price</th>
			</tr>
		</thead>

        
		<tbody>
			<?php ($total = 0); ?>
			<?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td style="text-align: center; font-size: 13px;"><?php echo e($date); ?></td>
				</tr>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="text-align: center; font-size: 13px;"><?php echo e($loop->iteration); ?></td>
                    <td style="text-align: center; font-size: 13px;"><?php echo e($item->sci_campaign_title ?? $item->sci_campaign_id); ?></td>
                    <td style="text-align: center; font-size: 13px;"><?php echo e($item->sci_targeted_time); ?></td>
                    <td style="text-align: center; font-size: 13px;"><?php echo e(optional($item->creditHistory)->uch_sms_count ?? $item->sci_total_submitted); ?></td>
                    <td style="text-align: right; font-size: 13px;"><?php echo e(number_format($item->sci_total_cost,2)); ?></td>
                </tr>
                <?php ($total += $item->sci_total_cost); ?>
				
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				
				<tr>
                    <td colspan="3">In Word:- <?php echo e(\OtherHelpers::number_to_text($total)); ?></td>
                    <td style="text-align: right;">Total:</td>
                    <td style="text-align: right;"><?php echo e(number_format($total,2)); ?></td>
                </tr>
		</tbody>
        
				
	

		
	</table>
	

</body>
</html>
