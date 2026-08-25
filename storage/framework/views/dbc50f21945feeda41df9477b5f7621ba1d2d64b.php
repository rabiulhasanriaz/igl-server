

<?php $__env->startSection('support_title', 'Create Support Ticket'); ?>
<?php $__env->startSection('support_subtitle', 'Submit a new support request'); ?>
<?php $__env->startSection('support_ticket', 'active'); ?>
<?php $__env->startSection('support_breadcrumb'); ?>
    <li class="active">Create Ticket</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('support_content'); ?>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Create New Support Ticket</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <form method="POST" action="<?php echo e(route('user.support.tickets.store')); ?>" id="create-ticket-form">
                            <?php echo csrf_field(); ?>
                            
                            <div class="form-group">
                                <label for="subject">Subject *</label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="<?php echo e(old('subject')); ?>" required placeholder="Brief description of your issue">
                                <?php if($errors->has('subject')): ?>
                                    <span class="text-danger"><?php echo e($errors->first('subject')); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Category *</label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Technical Issue" <?php echo e(old('category') == 'Technical Issue' ? 'selected' : ''); ?>>
                                                Technical Issue
                                            </option>
                                            <option value="Billing" <?php echo e(old('category') == 'Billing' ? 'selected' : ''); ?>>
                                                Billing
                                            </option>
                                            <option value="Account" <?php echo e(old('category') == 'Account' ? 'selected' : ''); ?>>
                                                Account
                                            </option>
                                            <option value="Service" <?php echo e(old('category') == 'Service' ? 'selected' : ''); ?>>
                                                Service
                                            </option>
                                            <option value="Other" <?php echo e(old('category') == 'Other' ? 'selected' : ''); ?>>
                                                Other
                                            </option>
                                        </select>
                                        <?php if($errors->has('category')): ?>
                                            <span class="text-danger"><?php echo e($errors->first('category')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority">Priority *</label>
                                        <select class="form-control" id="priority" name="priority" required>
                                            <option value="low" <?php echo e(old('priority') == 'low' ? 'selected' : ''); ?>>
                                                Low
                                            </option>
                                            <option value="medium" <?php echo e(old('priority') == 'medium' || !old('priority') ? 'selected' : ''); ?>>
                                                Medium
                                            </option>
                                            <option value="high" <?php echo e(old('priority') == 'high' ? 'selected' : ''); ?>>
                                                High
                                            </option>
                                            <option value="urgent" <?php echo e(old('priority') == 'urgent' ? 'selected' : ''); ?>>
                                                Urgent
                                            </option>
                                        </select>
                                        <?php if($errors->has('priority')): ?>
                                            <span class="text-danger"><?php echo e($errors->first('priority')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="8" 
                                          required placeholder="Please provide detailed information about your issue"><?php echo e(old('description')); ?></textarea>
                                <?php if($errors->has('description')): ?>
                                    <span class="text-danger"><?php echo e($errors->first('description')); ?></span>
                                <?php endif; ?>
                                <small class="help-block">Provide as much detail as possible to help us resolve your issue quickly.</small>
                            </div>
                            
                            <div class="form-actions center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ace-icon fa fa-check"></i>
                                    Submit Ticket
                                </button>
                                &nbsp; &nbsp; &nbsp;
                                <a href="<?php echo e(route('user.support.tickets')); ?>" class="btn btn-default">
                                    <i class="ace-icon fa fa-times"></i>
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Optional: Add jQuery validation
        $('#create-ticket-form').on('submit', function(e) {
            let isValid = true;
            $('.text-danger').remove();
            
            // Check subject
            if ($('#subject').val().trim().length < 5) {
                $('#subject').after('<span class="text-danger">Subject must be at least 5 characters</span>');
                isValid = false;
            }
            
            // Check category
            if ($('#category').val() === '') {
                $('#category').after('<span class="text-danger">Please select a category</span>');
                isValid = false;
            }
            
            // Check priority
            if ($('#priority').val() === '') {
                $('#priority').after('<span class="text-danger">Please select a priority</span>');
                isValid = false;
            }
            
            // Check description
            if ($('#description').val().trim().length < 10) {
                $('#description').after('<span class="text-danger">Description must be at least 10 characters</span>');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('user.support.layout', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>