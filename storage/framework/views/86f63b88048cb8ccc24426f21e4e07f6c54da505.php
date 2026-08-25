

<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Change Password</li>
    </ul><!-- /.breadcrumb -->
    <?php if(session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo e(session()->get('success')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
    <?php endif; ?>
    <?php if(session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <?php echo e(session()->get('error')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
    <?php endif; ?>
    <?php if(session()->has('err')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <?php echo e(session()->get('err')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
            <?php echo $__env->make('admin.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="col-md-6">
                <h3 class="text-center text-primary"> Change Flexiload pin </h3>

                <form action="<?php echo e(route('user.change-flexipin')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="old">Old Pin :</label>
                        <input type="password" name="old_pin" class="form-control" id="old" placeholder="Old Pin" 
                        <?php echo e((auth()->user()->flexipin == null) ? 'disabled':'required'); ?>>
                    </div>

                    <div class="form-group">
                        <label for="new">New PIN :</label>
                        <input type="password" name="new_pin" class="form-control" id="new"
                               placeholder="New Pin" required="">
                    </div>

                    <div class="form-group">
                        <label for="re">Re-Type New PIN :</label>
                        <input type="password" name="new_pin_confirmation" class="form-control" id="re"
                               placeholder="Re-Pin">
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-sm btn-primary" value="Change PIN">
                    </div>
                </form>
            </div>

            <div class="col-md-6">
                <h3 class="text-center text-primary"> Forgot Flexipin? </h3>

                <ul class="list-group" style="margin-top: 35px;">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      Forgot Flexipin?
                      <a href="" style="float: right;" data-toggle="modal" data-target="#forgot_flexipin">
                        <span class="badge badge-primary badge-pill">
                            Click Here
                          </span>
                      </a>
                    </li>
                  </ul>
            </div>
        </div><!-- /.col -->
    </div>
    <div class="modal" id="forgot_flexipin" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Retrieve FlexiPin</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('user.forgot-flexipin')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Enter Your Password</label>
                      <input type="password" name="password" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Password">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>