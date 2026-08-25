

<?php $__env->startSection('reseller_menu_class','open'); ?>
<?php $__env->startSection('reseller_registration_menu_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Reseller</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        Reseller
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            create
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>

    <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form action="<?php echo e(route('admin.reseller.store')); ?>" method="post" class="form-horizontal" role="form"
                  enctype="multipart/form-data">

                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-company-1"> Company name : </label>

                    <div class="col-sm-9">
                        <input type="text" id="companyName" placeholder="Company name" name="company_name"
                               class="col-xs-10 col-sm-5" required="" value="<?php echo e(old('company_name')); ?>"/>
                        <span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle text-danger" id="companyShow"> ** </span>

                            <?php if($errors->has('company_name')): ?>
                                <span class="text-danger"><?php echo e($errors->first('company_name')); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Name : </label>

                    <div class="col-sm-9">
                        <input type="text" id="ResellerName" placeholder="Name" name="reseller_name"
                               class="col-xs-10 col-sm-5" required="" value="<?php echo e(old('reseller_name')); ?>"/>
                        <span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle text-danger" id="resellerName_Show"> ** </span>
                            <?php if($errors->has('reseller_name')): ?>
                                <span class="text-danger"><?php echo e($errors->first('reseller_name')); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-email-1"> Email : </label>


                    <div class="col-sm-9">
                        <input type="email" id="EmaileNumber" placeholder="Email" name="email"
                               class="col-xs-10 col-sm-5" required="" onkeyup="checkEmailExistence(this.value)" value="<?php echo e(old('email')); ?>"/>
                        <span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle text-danger" id="Emailestate"> ** </span>
                            <span class="invalid-email text-danger"></span>
                            <span class="valid-email text-success"></span>
                            <?php if($errors->has('email')): ?>
                                <span class="text-danger retErrEmail"><?php echo e($errors->first('email')); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-phone-1"> Phone : </label>

                    <div class="col-sm-9">
                        <input type="text" id="mobileNumber" placeholder="Phone" name="phone"
                               class="col-xs-10 col-sm-5 input-mask-phone" onkeyup="checkPhoneExistence(this.value)" value="<?php echo e(old('phone')); ?>" data-mask="___________" required=""/>
                        <span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle text-danger" id="status"> ** </span>
                            <span class="invalid-phone text-danger"></span>
                            <span class="valid-phone text-success"></span>
                            <?php if($errors->has('phone')): ?>
                                <span class="text-danger retErrPhone"><?php echo e($errors->first('phone')); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-pass-2"> Password : </label>

                    <div class="col-sm-9">
                        <input type="password" id="form-pass-2" placeholder="Password" name="password"
                               class="col-xs-10 col-sm-5" value="<?php echo e(old('password')); ?>" required=""/>
                        <span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle text-danger"> ** </span>
                            <?php if($errors->has('password')): ?>
                                <span class="text-danger"><?php echo e($errors->first('password')); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-designation-1"> Designation
                        : </label>

                    <div class="col-sm-9">
                        <input type="text" id="form-designation-1" placeholder="Designation" class="col-xs-10 col-sm-5"
                               name="designation" value="<?php echo e(old('designation')); ?>" />
                        <span class="help-inline col-xs-12 col-sm-7">
                            <?php if($errors->has('designation')): ?>
                                <span class="text-danger"><?php echo e($errors->first('designation')); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-designation-1"> NID
                        : </label>

                    <div class="col-sm-9">
                        <input type="text" id="form-designation-1" placeholder="National Identification Number" class="col-xs-10 col-sm-5"
                               name="nid" value="<?php echo e(old('nid')); ?>" />
                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-designation-1"> D.O.B
                        : </label>

                    <div class="col-sm-9">
                        <input type="text" id="start" data-date-format="yyyy-mm-dd" autocomplete="off" placeholder="Date of Birth" class="col-xs-10 col-sm-5"
                               name="dob" value="<?php echo e(old('dob')); ?>" />
                        
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-address-1"> Address : </label>

                    <div class="col-sm-9">
                        <input type="text" id="form-address-1" placeholder="Address" class="col-xs-10 col-sm-5"
                               name="address" value="<?php echo e(old('address')); ?>"/>
                        <span class="help-inline col-xs-12 col-sm-7">
                            <?php if($errors->has('address')): ?>
                                <span class="text-danger"><?php echo e($errors->first('address')); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-image-1"> Logo : </label>

                    <div class="col-sm-9">
                        <input type="file" name="image" id="form-image-1">
                    </div>
                </div>

                <div class="clearfix form-group">
                    <div class="col-md-offset-3 col-md-9">

                        <input type="submit" class="btn btn-info" value="Registration">

                        &nbsp; &nbsp; &nbsp;
                        <button class="btn btn-danger" type="reset">
                            <i class="ace-icon fa fa-undo bigger-110"></i>
                            Reset
                        </button>

                    </div>
                </div>
            </form>


        </div><!-- /.col -->
    </div><!-- /.row -->


<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom_style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/bootstrap-datepicker3.min.css"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/data-mask.js" type="text/javascript"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/bootstrap-datepicker.min.js"></script>
    <?php echo $__env->make('admin.ajax.check_existence', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <script>
        // $('#view_archived_report').DataTable();
        $(document).ready(function () {
            $('#start').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>