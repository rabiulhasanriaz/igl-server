

<?php $__env->startSection('messaging_menu_class','open'); ?>
<?php $__env->startSection('templates_menu_class','active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
        </li>
        <li class="active">SMS Templates</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        SMS Templates
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            List
        </small>
    </h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <?php echo $__env->make('user.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('user.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <a href="#my-modal" role="button" data-toggle="modal" class="btn btn-primary btn-sm pull-right">
                &nbsp; Add New Templates&nbsp;
            </a>
            <div class="tab-pane fade active in" id="recipient">
                <form action="<?php echo e(route('user.template.store')); ?>" method="post" id="addTemplateForm">
                    <?php echo csrf_field(); ?>
                    <div id="my-modal" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> New Template </h3>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="templateTitle">Template Name </label>
                                        <input type="text" name="tmp_name" id="templateTitle" class="form-control"
                                               required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Select SMS Type
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="mt-radio-inline">
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosText" class="ace" value="text" checked="">
                                                <span class="lbl"></span> Text
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosFlash" class="ace" value="flash">
                                                <span class="lbl"></span> Flash
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosUnicodeFlash" class="ace"
                                                       value="flashunicode">
                                                <span class="lbl"></span> Flash Unicode
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosUnicode" class="ace" value="unicode">
                                                <span class="lbl"></span> Unicode
                                            </label>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="templateMessage">Enter SMS Content
                                            <span class="required" style="color: red;"> * </span>
                                        </label>
                                        <textarea class="count_me form-control" name="tmp_message"
                                                  id="templateMessage" required=""
                                                  style="min-height: 120px;"></textarea>
                                        <div class="row">
                                            <div class="col-md-4"><span>CHECK YOUR SMS COUNT</span></div>
                                            <div class="col-md-8">
                                                <div style="float: right">
                                                    <span class="charleft contacts-count">&nbsp;</span><span
                                                            class="parts-count"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" onclick="showPasswordModal('add')" class="btn btn-sm btn-primary pull-right">
                                        <i class="fa-check-square-o fa fa-times"></i>
                                        Save changes
                                    </button> &nbsp;&nbsp;
                                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                        <i class="ace-icon fa fa-times"></i>
                                        Close
                                    </button>
                                </div>
                            </div><!-- /.modal-content -->
                            <div id="aside-inside-modal"
                                 class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                 data-placement="bottom" data-background="true" data-backdrop="false"
                                 tabindex="-1"></div>
                        </div><!-- /.modal-dialog -->
                    </div>
                </form>
            </div>

            <div class="tab-pane fade active in" id="recipientEdit">
                <form action="<?php echo e(route('user.template.update')); ?>" method="post" id="editTemplateForm">
                    <?php echo csrf_field(); ?>
                    <div id="my-modal-edit" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> Edit Template </h3>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="editTemplateTitle">Template Name </label>
                                        <input type="text" name="tmp_name" id="editTemplateTitle" class="form-control"
                                               required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Select SMS Type
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="mt-radio-inline">
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="editRecipientsmsRadiosText" class="ace" value="text">
                                                <span class="lbl"></span> Text
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="editRecipientsmsRadiosFlash" class="ace" value="flash">
                                                <span class="lbl"></span> Flash
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="editRecipientsmsRadiosUnicodeFlash" class="ace"
                                                       value="flashunicode">
                                                <span class="lbl"></span> Flash Unicode
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="editRecipientsmsRadiosUnicode" class="ace" value="unicode">
                                                <span class="lbl"></span> Unicode
                                            </label>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="editTemplateMessage">Enter SMS Content
                                            <span class="required" style="color: red;"> * </span>
                                        </label>
                                        <textarea class="count_me form-control" name="tmp_message"
                                                  id="editTemplateMessage" required=""
                                                  style="min-height: 120px;"></textarea>
                                        <div class="row">
                                            <div class="col-md-4"><span>CHECK YOUR SMS COUNT</span></div>
                                            <div class="col-md-8">
                                                <div style="float: right">
                                                    <span class="charleft contacts-count">&nbsp;</span><span
                                                            class="parts-count"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="template_id" id="editTemplateId" value="">
                                <div class="modal-footer">
                                    <button type="button" onclick="showPasswordModal('edit')" class="btn btn-sm btn-primary pull-right">
                                        <i class="fa-check-square-o fa fa-times"></i>
                                        Update
                                    </button> &nbsp;&nbsp;
                                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                        <i class="ace-icon fa fa-times"></i>
                                        Close
                                    </button>
                                </div>
                            </div><!-- /.modal-content -->
                            <div id="aside-inside-modal"
                                 class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                 data-placement="bottom" data-background="true" data-backdrop="false"
                                 tabindex="-1"></div>
                        </div><!-- /.modal-dialog -->
                    </div>
                </form>
            </div>
        </div><!-- /.col -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">
            <table class="table table-bordered" id="template-table">
                <thead>
                <tr>
                    <th>SL</th>
                    <th class="col-md-2">Template title</th>
                    <th class="col-md-7">Template Contain</th>
                    <th> Total message</th>
                    <th> Action</th>
                </tr>
                </thead>

                <tbody>
                <?php ($serial=1); ?>
                <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="d-flex">
                        <td><?php echo e($serial++); ?></td>
                        <td id="template_name_<?php echo e($template->id); ?>"><?php echo e($template->st_name); ?></td>
                        <td id="template_text_<?php echo e($template->id); ?>"><?php echo e($template->st_content); ?></td>
                        <td><?php echo e($template->st_total_sms); ?></td>
                        <td>
                            <label>
                                <input type="hidden" id="template_id_<?php echo e($template->id); ?>" value="<?php echo e($template->id); ?>">
                                <a href="#my-modal-edit" onclick="getTemplate(<?php echo e($template->id); ?>)" role="button"
                                   data-toggle="modal" class="btn-none-edit pass_id">
                                    Edit </a>
                            </label>
                            | 
                            <a href="<?php echo e(route('user.template.delete', $template->id)); ?>" 
                               onclick="return confirm('Are you sure you want to delete this template?');" 
                               class="btn-none-delete"> 
                                Delete 
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>

             </table>
        </div>
    </div><!-- /.row -->

    <!-- Password Verification Modal (ONLY for Add and Edit) -->
    <div id="passwordModal" style="display: none;">
        <div id="passwordOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999;"></div>
        <div id="passwordModalContent" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; z-index: 100000; width: 400px; max-width: 90%; box-shadow: 0 5px 30px rgba(0,0,0,0.3);">
            <div style="margin-bottom: 20px; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">
                <h4 style="margin: 0; color: #333;">Verify Password</h4>
            </div>
            <div style="margin-bottom: 20px;">
                <p style="margin: 0 0 15px 0; color: #666;">Please enter your password to confirm this action.</p>
                <input type="password" id="verifyPassword" class="form-control" placeholder="Enter your password" autocomplete="off" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <input type="hidden" id="actionType" value="">
                <div id="passwordError" style="color: red; margin-top: 10px; font-size: 12px; display: none;"></div>
            </div>
            <div style="text-align: center; padding-top: 10px; border-top: 1px solid #eee;">
                <button onclick="submitWithPassword()" style="margin: 0 5px; padding: 8px 25px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Confirm</button>
                <button onclick="closePasswordModal()" style="margin: 0 5px; padding: 8px 25px; background-color: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_style'); ?>
    <link href="<?php echo e(asset('assets/datatable/jquery.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('assets/datatable/rowReorder.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('assets/datatable/responsive.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <style>
        @media(max-width:575px){
            .abcd{
                width: 130px;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <script src="<?php echo e(asset('assets')); ?>/js/jquery.textareaCounter.plugin.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/text-area-counter.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#template-table').DataTable( {
                responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
                ]
            } );
            
            count_textarea('#recipient');
            count_textarea('#recipientEdit');
        });

        function getTemplate(val) {
            var tmp_title = $("#template_name_" + val).html();
            var tmp_text = $("#template_text_" + val).html();
            var tmp_id = $("#template_id_" + val).val();
            $("#editTemplateTitle").val(tmp_title);
            $("#editTemplateMessage").val(tmp_text);
            $("#editTemplateId").val(tmp_id);
        }

        function showPasswordModal(action, templateId = null) {
            // Close any open bootstrap modals
            $('.modal').modal('hide');
            
            // Reset and show password modal
            $('#verifyPassword').val('');
            $('#passwordError').hide();
            $('#actionType').val(action);
            $('#passwordModal').show();
            
            // Focus on password input
            setTimeout(function() {
                $('#verifyPassword').focus();
            }, 100);
        }

        function closePasswordModal() {
            $('#passwordModal').hide();
            $('#verifyPassword').val('');
            $('#actionType').val('');
            $('#passwordError').hide();
        }

        function submitWithPassword() {
            var password = $('#verifyPassword').val();
            var action = $('#actionType').val();
            
            if (!password) {
                $('#passwordError').text('Please enter your password').show();
                $('#verifyPassword').focus();
                return false;
            }
            
            // Clear error if exists
            $('#passwordError').hide();
            
            if (action === 'add') {
                // Add password to form and submit
                var addForm = $('#addTemplateForm');
                // Remove existing password field if any
                addForm.find('input[name="password"]').remove();
                // Add password field
                addForm.append('<input type="hidden" name="password" value="' + password + '">');
                // Close password modal
                closePasswordModal();
                // Submit the form
                addForm.submit();
            } 
            else if (action === 'edit') {
                // Add password to edit form and submit
                var editForm = $('#editTemplateForm');
                editForm.find('input[name="password"]').remove();
                editForm.append('<input type="hidden" name="password" value="' + password + '">');
                closePasswordModal();
                editForm.submit();
            }
            
            return true;
        }
        
        // Handle enter key on password field
        $(document).on('keypress', '#verifyPassword', function(e) {
            if (e.which === 13) {
                submitWithPassword();
                return false;
            }
        });
        
        // Close modal when clicking on overlay
        $(document).on('click', '#passwordOverlay', function() {
            closePasswordModal();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>