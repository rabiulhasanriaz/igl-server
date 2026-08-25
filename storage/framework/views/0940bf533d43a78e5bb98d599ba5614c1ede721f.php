		<div id="sidebar" class="sidebar responsive ace-save-state">
			<script type="text/javascript">
				try{ace.settings.loadState('sidebar')}catch(e){}
			</script>

			<div class="sidebar-shortcuts" id="sidebar-shortcuts">
				<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
					<a href="<?php echo e(route('reseller.user.index')); ?>" class="btn btn-warning">
						<i class="ace-icon fa fa-users"></i>
					</a>

					<a href="<?php echo e(route('reseller.senderIDList')); ?>" class="btn btn-info">
						<i class="fa-credit-card fa fa-signal"></i>
					</a>

					<a href="<?php echo e(route('reseller.profile')); ?>" class="btn btn-success">
						<i class="ace-icon fa fa-user"></i>
					</a>

					<a href="<?php echo e(route('reseller.change-password')); ?>" class="btn btn-danger">
						<i class="ace-icon fa fa-cogs"></i>
					</a>
				</div>

				<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
					<span class="btn btn-success"></span>

					<span class="btn btn-info"></span>

					<span class="btn btn-warning"></span>

					<span class="btn btn-danger"></span>
				</div>
			</div><!-- /.sidebar-shortcuts -->

			<ul class="nav nav-list">
			<li>
				    <a href="javascript:void(0)" id="showBalanceBtn">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text">
					    <span id="current_balance">Tap For Balance</span>
					</span>
				    </a>
				</li>
				<li class="<?php echo $__env->yieldContent('dashboard_menu_class'); ?>">
					<a href="<?php echo e(route('reseller.index')); ?>">
						  <i class="menu-icon fa fa-tachometer"></i>
        <span class="menu-text">Dashboard</span>
					</a>
				</li>

				<!--Reseller Menu-->
				<li class="<?php echo $__env->yieldContent('user_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-users"></i>
						<span class="menu-text">
							User
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="<?php echo $__env->yieldContent('user_registration_menu_class'); ?>">
							<a href="<?php echo e(route('reseller.user.create')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								User Registration
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('user_list_menu_class'); ?>">
							<a href="<?php echo e(route('reseller.user.index')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								User List
							</a>
						</li>
	<?php
							$user = Auth::user();  // or Auth::guard('web')->user()
						?>
						<?php if($user && in_array('3', explode(',', $user->permission))): ?>
							<li class="<?php echo $__env->yieldContent('inactive_user'); ?>">
										<a href="<?php echo e(route('reseller.user.inactiveUser')); ?>">
											<i class="menu-icon fa fa-caret-right"></i>
										Inactive User List
									</a>
								</li>
								
								<li class="<?php echo $__env->yieldContent('sms_activity_menu'); ?>">
    <a href="<?php echo e(route('reseller.user.smsActivity')); ?>">
        <i class="menu-icon fa fa-caret-right"></i>
        User Activity
    </a>
    <b class="arrow"></b>
</li>
<li class="<?php echo $__env->yieldContent('sms_activity_menu_with'); ?>">
    <a href="<?php echo e(route('reseller.user.smsActivitywithoutbalance')); ?>">
        <i class="menu-icon fa fa-caret-right"></i>
        Activity Without Balance
    </a>
    <b class="arrow"></b>
</li>
<li class="<?php echo $__env->yieldContent('sms_activity_menu_monitoring'); ?>">
    <a href="<?php echo e(route('reseller.user.smsMonitoringDashboard', ['date' => now()->toDateString()])); ?>">
        <i class="menu-icon fa fa-caret-right"></i>
        Monitoring
    </a>
    <b class="arrow"></b>
</li>
							<?php endif; ?>

						<li class="<?php echo $__env->yieldContent('suspend_user_menu_class'); ?>">
							<a href="<?php echo e(route('reseller.user.suspendUser')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Suspend User List
							</a>
						</li>
						<li class="<?php echo $__env->yieldContent('low_balance_user_menu'); ?>">
    <a href="<?php echo e(route('reseller.user.low_balance')); ?>">
        <i class="menu-icon fa fa-exclamation-triangle"></i>
        Low Balance Users
    </a>
    <b class="arrow"></b>
</li>
					</ul>
				</li>

				<!-- Employee Section -->
				<li class="<?php echo $__env->yieldContent('employee_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-users"></i>
						<span class="menu-text">
							Employee
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="<?php echo $__env->yieldContent('employee_registration_menu_class'); ?>">
							<a href="<?php echo e(route('reseller.employee.create')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Employee Registration
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('employee_list_menu_class'); ?>">
							<a href="<?php echo e(route('reseller.employee.index')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Employee List
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('user_assign_to_employee'); ?>">
							<a href="<?php echo e(route('reseller.employee.asignUser')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Asign User
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('change_employee'); ?>">
							<a href="<?php echo e(route('reseller.employee.change_employee')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Change Employee
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('pay_to_employee'); ?>">
							<a href="<?php echo e(route('reseller.employee.pay_balance')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Pay to Employee
							</a>
						</li>

					</ul>
				</li>
				<!-- Employee section end -->

				<!--Reseller Limit-->
				<li class="<?php echo $__env->yieldContent('price_and_converage_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-credit-card"></i>
						<span class="menu-text"> Price & Converage </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="<?php echo $__env->yieldContent('price_menu_class'); ?>">
							<a href="<?php echo e(route('reseller.priceList')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Price
							</a>
						</li>
					</ul> 
				</li>

				<!--Non Masking-->
				<li class="<?php echo $__env->yieldContent('sender_id_menu_class'); ?>">
					<a href="<?php echo e(route('reseller.senderIDList')); ?>">
						<i class="menu-icon fa fa-calendar"></i>
						<span class="menu-text"> Sender ID </span>
					</a>
				</li>

				<li class="<?php echo $__env->yieldContent('send_sms_to_all_users'); ?>">
					<a href="<?php echo e(route('reseller.sendSmsToAll')); ?>">
						<i class="menu-icon fa fa-calendar"></i>
						<span class="menu-text"> Send Notice </span>
					</a>
				</li>


				<!--Accounts-->
				<li class="<?php echo $__env->yieldContent('acc_details_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon  fa fa-book"></i>
						<span class="menu-text"> Acc Details </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="<?php echo $__env->yieldContent('add_fund_credit_menu_class'); ?>">
							<a href="<?php echo e(route('reseller.balance.credit.create')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Add Fund Credit
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('add_fund_debit_menu_class'); ?>">
							<a href="<?php echo e(route('reseller.balance.debit.create')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Add Fund Debit
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('user_statement_menu_class'); ?>">
							<a href="<?php echo e(route('reseller.transactionHistory')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								User Statement
							</a>
						</li>

						<!-- <li class="<?php echo $__env->yieldContent('check_api_balance_menu_class'); ?>">
							<a href="pricing.html">
								<i class="menu-icon fa fa-caret-right"></i>
								Check API Balance
							</a>
						</li> -->
					</ul> 
				</li>
			</ul><!-- /.nav-list -->

			<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
				<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
			</div>
		</div>
