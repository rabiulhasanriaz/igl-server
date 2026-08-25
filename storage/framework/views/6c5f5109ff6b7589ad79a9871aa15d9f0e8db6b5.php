<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center;">Sl</th>
			<th style="text-align: center;">User Name</th>
			<th style="text-align: center;">Date & Time</th>
			<th style="text-align: center;">Mobile Number</th>
			<th style="text-align: center;">Package Name</th>
			<th style="text-align: center;">Trx</th>
			<th style="text-align: center;">Remarks</th>
			<th style="text-align: center;">Amount</th>
		</tr>
	</thead>

	<tbody>
		
		<?php $__currentLoopData = $allData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td style="text-align: center;"><?php echo e($loop->iteration); ?></td>
				<td><?php echo e($data->owner_name ?? 'N/A'); ?></td>
				<td style="text-align: center;"><?php echo e($data->created_at); ?></td>
				<td style="text-align: center;"><?php echo e($data->targeted_number); ?></td>
				<td>
					<?php if($data->package_id == 0): ?>
						Single Load
					<?php else: ?>
						<?php echo e($data->package_info['package_name']); ?>

					<?php endif; ?>
				</td>
				<td><?php echo e($data->transaction_id); ?></td>
				<td><?php echo e($data->remarks); ?></td>
				<td style="text-align: right;"><?php echo e($data->campaign_price); ?></td>
			</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</tbody>

	<tfoot>
		
	</tfoot>
</table>