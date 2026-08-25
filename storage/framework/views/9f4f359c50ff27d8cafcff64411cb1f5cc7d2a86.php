        <?php ($permission = explode(',',$user->permission)); ?>
		<?php if(in_array(1,$permission)): ?>
			<?php ($sms_permission = true); ?>
		<?php else: ?>
			<?php ($sms_permission = false); ?>	
		<?php endif; ?>
		<?php if(in_array(2,$permission)): ?>
			<?php ($flexi_permission = true); ?>
		<?php else: ?>
			<?php ($flexi_permission = false); ?>	
		<?php endif; ?>
        <?php if(in_array(3,$permission)): ?>
            <?php ($dynamic_permission = true); ?>
        <?php else: ?>
            <?php ($dynamic_permission = false); ?> 
        <?php endif; ?>


<?php $__env->startSection('user_list_menu_class','active'); ?>
<?php $__env->startSection('user_menu_class','open'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('reseller.index')); ?>">Dashboard</a>
        </li>
        <li class="active">User</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        User
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Edit
             <i class="ace-icon fa fa-angle-double-right"></i>
            <?php echo e($user->company_name); ?>

        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <?php echo $__env->make('reseller.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('reseller.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <form action="<?php echo e(route('reseller.user.update', $user->id)); ?>" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-company-1"> Company name : </label>
                    <div class="col-sm-9">
                        <input type="text" id="companyName" placeholder="Company name" name="company_name"
                               class="col-xs-10 col-sm-5" required="" value="<?php echo e($user->company_name); ?>"/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger" id="companyShow"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Name : </label>
                    <div class="col-sm-9">
                        <input type="text" id="UserName" placeholder="Name" name="user_name"
                               class="col-xs-10 col-sm-5" required="" value="<?php echo e($user->userDetail['name']); ?>"/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger" id="resellerName_Show"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-email-1"> Email : </label>
                    <div class="col-sm-9">
                        <input type="email" id="EmaileNumber" placeholder="Email" name="email"
                               class="col-xs-10 col-sm-5" required="" value="<?php echo e($user->email); ?>"/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger" id="Emailestate"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-phone-1"> Phone : </label>
                    <div class="col-sm-9">
                        <input type="text" id="mobileNumber" placeholder="Phone" name="phone"
                               class="col-xs-10 col-sm-5 input-mask-phone" value="<?php echo e($user->cellphone); ?>" required=""/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger" id="status"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-pass-2"> Password : </label>

                    <div class="col-sm-9">
                        <input type="password" id="form-pass-2" placeholder="Password" name="password"
                               class="col-xs-10 col-sm-5" value="" />
                        <span class="help-inline col-xs-12 col-sm-7">
						
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-designation-1"> Designation
                        : </label>
                    <div class="col-sm-9">
                        <input type="text" id="form-designation-1" placeholder="Designation" class="col-xs-10 col-sm-5"
                               name="designation" value="<?php echo e($user->userDetail->designation); ?>" required=""/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-address-1"> Address : </label>

                    <div class="col-sm-9">
                        <input type="text" id="form-address-1" placeholder="Address" class="col-xs-10 col-sm-5"
                               name="address" value="<?php echo e($user->userDetail->address); ?>" required=""/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-designation-1"> Access type
                        : </label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="ace" name="status" onchange="show_terget(this.value)"
                                       value="Reseller" <?php echo e(($user->role==4)?'checked':''); ?> required="">
                                <span class="lbl"> Reseller </span>
                            </label>
                            <label>
                                <input type="radio" class="ace" name="status" onchange="show_terget(this.value)"
                                       value="User" <?php echo e(($user->role==5)?'checked':''); ?> required="">
                                <span class="lbl"> User </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="permission" style="<?php echo e(($user->role==5)?'':'display: none;'); ?>">
                    <label class="col-sm-3 control-label no-padding-right" for=""> Permission </label>
                    <div class="col-sm-9">
                        <input type="checkbox" name="permission[]" id="" value="1" <?php echo e(($sms_permission)? 'checked' : ''); ?>> SMS
                        <input type="checkbox" name="permission[]" id="" value="2" <?php echo e(($flexi_permission)? 'checked' : ''); ?>> Flexiload
                        
                        <?php if(Auth::user()->create_by == 1): ?>
                        <input type="checkbox" name="permission[]" id="" value="3" <?php echo e(($dynamic_permission)? 'checked' : ''); ?>> Dynamic
                        <?php endif; ?>
                        
                        <input type="checkbox" name="" id="checkAll" value=""> All
                    </div>
                </div>
                <div class="form-group" id="user_logo" style="<?php echo e(($user->role==4)?'':'display: none;'); ?>">
                    <label class="col-sm-3 control-label no-padding-right" for="form-image-1"> Logo : </label>
                    <div class="col-sm-9">
                        <input type="file" name="image" id="form-image-1">
                        <span><img src="<?php echo e(OtherHelpers::user_logo($user->userDetail->logo)); ?>" style="height: 60px;"></span>
                    </div>
                </div>
                <div class="clearfix form-group">
                    <div class="col-md-offset-3 col-md-9">
                        <input type="submit" class="btn btn-primary" value="Update">
                    </div>
                </div>
            </form>


        </div><!-- /.col -->
    </div><!-- /.row -->


<?php $__env->stopSection(); ?>



<?php $__env->startSection('custom_script'); ?>
    <script type="text/javascript">
        function show_terget(value) {
            if (value == 'User') {
                $('#user_logo').hide();
                $('#permission').show();           
            }
            else if (value == 'Reseller') {
                $('#user_logo').show();
                $('#permission').hide();           
            }
        }
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    </script>

    <?php echo $__env->make('admin.ajax.check_existence', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('reseller.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>