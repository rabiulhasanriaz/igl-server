

<?php $__env->startSection('support_title', 'My Support Tickets'); ?>
<?php $__env->startSection('support_subtitle', 'View and manage your support tickets'); ?>
<?php $__env->startSection('support_ticket', 'active'); ?>
<?php $__env->startSection('support_breadcrumb'); ?>
    <li class="active">All Tickets</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('support_content'); ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="clearfix">
                <div class="pull-right tableTools-container">
                    <a href="<?php echo e(route('user.support.tickets.create')); ?>" class="btn btn-white btn-primary btn-bold">
                        <i class="ace-icon fa fa-plus bigger-120 blue"></i>
                        Create New Ticket
                    </a>
                </div>
            </div>
            
            <div class="table-header">
                My Support Tickets
            </div>
            
            <div>
                <table id="tickets-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Last Update</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($ticket->ticket_number); ?></td>
                                <td>
                                    <a href="<?php echo e(route('user.support.tickets.show', $ticket->id)); ?>">
                                        <?php echo e(strlen($ticket->subject) > 50 ? substr($ticket->subject, 0, 50) . '...' : $ticket->subject); ?>

                                    </a>
                                </td>
                                <td><?php echo e($ticket->category); ?></td>
                                <td>
                                    <?php switch($ticket->priority):
                                        case ('low'): ?>
                                            <span class="label label-info">Low</span>
                                            <?php break; ?>
                                        <?php case ('medium'): ?>
                                            <span class="label label-warning">Medium</span>
                                            <?php break; ?>
                                        <?php case ('high'): ?>
                                            <span class="label label-danger">High</span>
                                            <?php break; ?>
                                        <?php case ('urgent'): ?>
                                            <span class="label label-danger">Urgent</span>
                                            <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php switch($ticket->status):
                                        case ('open'): ?>
                                            <span class="label label-success">Open</span>
                                            <?php break; ?>
                                        <?php case ('in_progress'): ?>
                                            <span class="label label-primary">In Progress</span>
                                            <?php break; ?>
                                        <?php case ('on_hold'): ?>
                                            <span class="label label-warning">On Hold</span>
                                            <?php break; ?>
                                        <?php case ('resolved'): ?>
                                            <span class="label label-info">Resolved</span>
                                            <?php break; ?>
                                        <?php case ('closed'): ?>
                                            <span class="label label-default">Closed</span>
                                            <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td><?php echo e($ticket->created_at->format('Y-m-d H:i')); ?></td>
                                <td><?php echo e($ticket->updated_at->format('Y-m-d H:i')); ?></td>
                                <td>
                                    <div class="hidden-sm hidden-xs action-buttons">
                                        <a href="<?php echo e(route('user.support.tickets.show', $ticket->id)); ?>" 
                                           class="btn btn-xs btn-info" title="View">
                                            <i class="ace-icon fa fa-eye bigger-120"></i>
                                        </a>
                                        
                                        <?php if($ticket->status === 'closed'): ?>
                                            <form method="POST" action="<?php echo e(route('user.support.tickets.reopen', $ticket->id)); ?>" 
                                                  style="display: inline-block;">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-xs btn-warning" title="Reopen">
                                                    <i class="ace-icon fa fa-refresh bigger-120"></i>
                                                </button>
                                            </form>
                                        <?php elseif($ticket->status !== 'closed'): ?>
                                            <form method="POST" action="<?php echo e(route('user.support.tickets.close', $ticket->id)); ?>" 
                                                  style="display: inline-block;">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-xs btn-danger" title="Close">
                                                    <i class="ace-icon fa fa-times bigger-120"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center">No support tickets found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if($tickets->hasPages()): ?>
                    <div class="pull-right">
                        <?php echo e($tickets->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#tickets-table').dataTable({
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bSort": true,
            "bInfo": false,
            "bAutoWidth": false
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('user.support.layout', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>