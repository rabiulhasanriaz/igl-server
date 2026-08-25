

<?php $__env->startSection('reseller_menu_class','open'); ?>
<?php $__env->startSection('reseller_tree_menu_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Reseller Graph</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        Reseller
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Tree & Graph
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div id="test" class="tree">
                <h3 class="text-center text-primary">Tree Of Customer</h3>
                <hr>
                <ul>
                    <?php $__currentLoopData = $roots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $root): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="parent_li">
                            <span title="Root" style="background: green; color: #fff;"><?php echo e(@$root->userDetail->company_name); ?></span>
                            <ul>

                                <!-- main -reseller -->
                                <?php if($root->myUsers->count()>'0'): ?>
                                    <?php $__currentLoopData = $root->myUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="parent_li">
                                            <span title="IGL Web Lmt"
                                                  style="color: green;font-weight: bold;"><?php echo e(@$user1->company_name); ?></span>
                                            <ul>
                                                <!-- 2nd -reseller -->
                                                <?php if($user1->myUsers->count()>'0'): ?>
                                                    <?php $__currentLoopData = $user1->myUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li class="parent_li">
                                                            <span title="IGL Web Lmt"
                                                                  style="color: #77017e;font-size: 12px; "><?php echo e(@$user2->company_name); ?></span>
                                                            <ul>
                                                                <!-- 3rd reseller-->
                                                                <?php if($user2->myUsers->count()>'0'): ?>
                                                                    <?php $__currentLoopData = $user2->myUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <li class="parent_li">
                                                                    <span title="IGL Web Lmt"
                                                                          class="text-primary"><?php echo e(@$user3->company_name); ?></span>
                                                                    <ul>
                                                                        <!-- 4th reseller -->
                                                                        <?php if($user3->myUsers->count()>'0'): ?>
                                                                            <?php $__currentLoopData = $user3->myUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user4): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <li class="parent_li">
                                                                                    <span title="IGL Web Lmt" class="text-primary"><?php echo e(@$user4->company_name); ?></span>
                                                                                    <ul>
                                                                                        <!-- 5th reseller -->
                                                                                        <?php if($user4->myUsers->count()>'0'): ?>
                                                                                            <?php $__currentLoopData = $user4->myUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user5): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                <li class="parent_li">
                                                                                                    <span title="IGL Web Lmt" class="text-danger"><?php echo e(@$user5->company_name); ?></span>
                                                                                                    <ul>
                                                                                                        <!--last users-->
                                                                                                        <?php if($user5->myUsers->count()>'0'): ?>
                                                                                                            <?php $__currentLoopData = $user5->myUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user6): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                        <li class="parent_li">
                                                                                                            <span title="IGL Web Lmt" class="text-danger"><?php echo e(@$user6->company_name); ?></span>
                                                                                                        </li>
                                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                                        <?php endif; ?>
                                                                                                    </ul>
                                                                                                </li>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                        <?php endif; ?>
                                                                                    </ul>
                                                                                </li>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        <?php endif; ?>
                                                                    </ul>
                                                                </li>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>

                                            </ul>
                                        </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <!-- main -reseller end -->

                            </ul>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>


            </div>

        </div><!-- /.col -->
    </div><!-- /.row -->


<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>