<?php $__env->startSection('phone_book_menu_class','open'); ?>
<?php $__env->startSection('contact_and_group_menu_class','active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Phonebook</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        <a href="<?php echo e(route('user.phonebook.index')); ?>">Phonebook</a>
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Group Name
        </small>
    </h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 padding">

                
                <?php echo $__env->make('admin.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <div class="ajax_error" style="display: none">
                    <div class="alert alert-danger" id="report-alert">
                        <button type="button" class="close" data-dismiss="alert"><span style="font-size: 20px;">x</span>
                        </button>
                        <span class="error_messages"></span>
                    </div>
                </div>
                <div class="ajax_success" style="display: none">
                    <div class="alert alert-success" id="report-alert">
                        <button type="button" class="close" data-dismiss="alert"><span style="font-size: 20px;">x</span>
                        </button>
                        <span class="success_messages"></span>
                    </div>
                </div>

                <a href="#import_contact_modal" role="button" data-toggle="modal"
                   class="btn btn-primary btn-sm pull-right">&nbsp; Import Contact &nbsp;</a>
                <a href="#add_single_contact_modal" role="button" data-toggle="modal"
                   class="btn btn-danger btn-sm pull-right" id="updateButtonHide">&nbsp; Add Single Contact &nbsp;</a>

                
                <form action="<?php echo e(route('user.phonebook.importContact')); ?>" id="import_contact_form" method="post" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <!-- /.modal-dialog  start-->
                    <div id="import_contact_modal" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> Import Contact </h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="group_id">Groups</label>
                                        <select name="group_id" id="group_id" class="chosen-select form-control group_id" required="">
                                            <option value="">Nothing Selected</option>
                                            <?php $__currentLoopData = $contact_groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($contact_group->id); ?>"> <?php echo e($contact_group->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Import Contact <span class="text-primary">(only .xls, .xlsx, .csv, .txt file accept)</span></label>
                                        <input type="file" name="contact_file" accept=".xls, .xlsx, .csv, .txt" required="">
                                    </div>
                                    <br>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-sm btn-primary pull-right"
                                            name="group_fileUpload_submit">
                                        <i class="fa-check-square-o fa fa-times"></i>Submit
                                    </button> &nbsp;&nbsp;
                                    <button class="btn btn-sm btn-danger pull-right modal_close" data-dismiss="modal">
                                        <i class="ace-icon fa fa-times"></i>Close
                                    </button>
                                </div>
                            </div><!-- /.modal-content -->
                            <div id="aside-inside-modal"
                                 class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                 data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">
                            </div>
                        </div><!-- /.modal-dialog -->
                    </div>
                </form>
            


                <!-- -----------Single  contact model- start--------- -->
                <form action="<?php echo e(route('user.phonebook.storeContact')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div id="add_single_contact_modal" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> Add Contact </h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="contact_number">Contact No </label>
                                        <input type="text" name="contact_number" id="contact_number"
                                               class="form-control" required="">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Name </label>
                                        <input type="text" name="contact_name" id="valuePass" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Designation </label>
                                        <input type="text" name="designation" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Groups</label>
                                        <select name="category_id" class="chosen-select form-control group_id"  required="">
                                            <option value="">Nothing Selected</option>
                                            <?php $__currentLoopData = $contact_groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($contact_group->id); ?>"><?php echo e($contact_group->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label><br>
                                        <label>
                                            <input type="radio" class="ace" name="contactStatus" value="1"
                                                   required="" checked="">
                                            <span class="lbl">  Enabled </span>
                                        </label>
                                        <label>
                                            <input type="radio" class="ace" name="contactStatus" value="2"
                                                   required="">
                                            <span class="lbl"> Disabled </span>
                                        </label>
                                    </div>
                                    <br>
                                </div>
                                <div class="modal-footer">
                                    <button id="button_SingleDisNone" class="btn btn-sm btn-primary pull-right">
                                        <i class="fa-check-square-o fa fa-times"></i>Submit
                                    </button> &nbsp;&nbsp;
                                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                        <i class="ace-icon fa fa-times"></i>Close
                                    </button>
                                </div>
                            </div><!-- /.modal-content -->
                            <div id="aside-inside-modal"
                                 class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                 data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">
                            </div>
                        </div><!-- /.modal-dialog -->
                    </div>
                </form>
                <!-- -----------Single  contact model- end --------- -->


                <!-- -----------Single contact edit model start--------- -->
                <form action="<?php echo e(route('user.phonebook.updateContact')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div id="edit_single_contact_modal" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> Edit Contact </h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="edit_contact_number">Contact No </label>
                                        <input type="text" name="contact_number" id="edit_contact_number"
                                               class="form-control" required="">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_contact_name">Name </label>
                                        <input type="text" name="contact_name" id="edit_contact_name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_designation">Designation </label>
                                        <input type="text" name="designation" id="edit_designation" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label >Groups</label>
                                        <select name="category_id" class="chosen-select form-control group_id"  required="">
                                            <option value="">Nothing Selected</option>
                                            <?php $__currentLoopData = $contact_groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($contact_group->id); ?>"><?php echo e($contact_group->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label><br>
                                        <label>
                                            <input type="radio" class="ace" name="contactStatus" value="1"
                                                   required="" checked="">
                                            <span class="lbl">  Enabled </span>
                                        </label>
                                        <label>
                                            <input type="radio" class="ace" name="contactStatus" value="2"
                                                   required="">
                                            <span class="lbl"> Disabled </span>
                                        </label>
                                    </div>
                                    <br>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="contact_id" id="edit_contact_id">
                                    <button id="button_SingleDisNone" class="btn btn-sm btn-primary pull-right">
                                        <i class="fa-check-square-o fa fa-times"></i>Update
                                    </button> &nbsp;&nbsp;
                                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                        <i class="ace-icon fa fa-times"></i>Close
                                    </button>
                                </div>
                            </div><!-- /.modal-content -->
                            <div id="aside-inside-modal"
                                 class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                 data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">
                            </div>
                        </div><!-- /.modal-dialog -->
                    </div>
                </form>
                <!-- -----------Single contact edit model end --------- -->

            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 padding" style="min-height: 550px;">
                <table id="contact_details_table" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Contact</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Group</th>
                        <th> Status</th>
                        <th> Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php ($serial=1); ?>
                    <?php ($contacts=\App\Model\PhonebookContact::where('category_id', $group_id)->orderBy('name','asc')->get()); ?>
                    <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($serial++); ?></td>
                            <td id="phone_number_<?php echo e($contact->id); ?>"><?php echo e($contact->phone_number); ?></td>
                            <td id="name_<?php echo e($contact->id); ?>"><?php echo e($contact->name); ?></td>
                            <td id="designation_<?php echo e($contact->id); ?>"><?php echo e($contact->designation); ?></td>
                            <td><?php echo e($group_name); ?></td>
                            <?php if($contact->status=='1'): ?>
                            <td style="color: #1bcb00">Active</td>
                            <?php else: ?>
                                <td style="color: #cb0021">InActive</td>
                            <?php endif; ?>
                            <td>
                                <label>
                                    <a href="#edit_single_contact_modal" onclick="get_contact_details(<?php echo e($contact->id); ?>)" role="button" data-toggle="modal"
                                       class="serialNumberId btn-none-edit"> Edit </a>
                                </label>
                                <label>
                                    | <a href="<?php echo e(route('user.phonebook.deleteContact', $contact->id)); ?>" class="btn-none-delete"
                                         onclick="return confirm('Are you sure you want to delete ?');"> Delete </a>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->

<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/chosen.min.css" />
    <style>
        .chosen-container { width: 100% !important; }
        .tab-content { border: none !important; }
    </style>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.bootstrap.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/chosen.jquery.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/ajax_import_contact.js"></script>

    <script type="text/javascript">
        $(".group_id").val(<?php echo e($group_id); ?>);
        $('.chosen-select').chosen({allow_single_deselect:true});
        $('#contact_details_table').DataTable();

        function get_contact_details(id) {
            var phoneNumber = $("#phone_number_"+id).html();
            var contactName = $("#name_"+id).html();
            var designation = $("#designation_"+id).html();

            $("#edit_contact_number").val(phoneNumber);
            $("#edit_contact_name").val(contactName);
            $("#edit_designation").val(designation);
            $("#edit_contact_id").val(id);

        }

        import_contact_form_submit('import_contact_form','<?php echo e(route('user.phonebook.importContact')); ?>', 'import_contact_modal');
    </script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>