		<?php ($permission = explode(',',Auth::user()->permission)); ?>
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
		<?php if(Auth::user()->userDetail->dynamic_permission == 1): ?>
			<?php ($dynamic_permission = true); ?>
		<?php else: ?>
			<?php ($dynamic_permission = false); ?>
		<?php endif; ?>
		<div id="sidebar" class="sidebar responsive ace-save-state">
			<script type="text/javascript">
				try{ace.settings.loadState('sidebar')}catch(e){}
			</script>

			<div class="sidebar-shortcuts" id="sidebar-shortcuts">
				<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
					<a href="<?php echo e(route('user.sms.create')); ?>" class="btn btn-success">
						<i class="fa-envelope fa fa-pencil"></i>
					</a>

					<a href="<?php echo e(route('user.reports.todays_sms')); ?>" class="btn btn-info">
						<i class="fa-credit-card fa fa-signal"></i>
					</a>

					<a href="<?php echo e(route('user.profile')); ?>" class="btn btn-warning">
						<i class="ace-icon fa fa-user"></i>
					</a>

					<a href="<?php echo e(route('user.change-password')); ?>" class="btn btn-danger">
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
					<a href="<?php echo e(route('user.index')); ?>">
						  <i class="menu-icon fa fa-tachometer"></i>
        <span class="menu-text">Dashboard</span>
					</a>
				</li>
			
				

				<!--Messaging Menu-->
				<?php if($sms_permission): ?>
				<li class="<?php echo $__env->yieldContent('messaging_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-envelope-o"></i>
						<span class="menu-text">
							Messaging
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="<?php echo $__env->yieldContent('send_sms_menu_class'); ?>">
							<a href="<?php echo e(route('user.sms.create')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Send SMS
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('campaign_menu_class'); ?>">
							<a href="<?php echo e(route('user.sms.campaignCreate')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Campaign
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('sender_id_menu_class'); ?>">
							<a href="<?php echo e(route('user.senderIDList')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Sender ID
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('templates_menu_class'); ?>">
							<a href="<?php echo e(route('user.template.index')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Templates
							</a>
						</li>
					</ul>
				</li>

				<!--Price & Converage-->
				<li class="<?php echo $__env->yieldContent('price_and_converage_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-credit-card"></i>
						<span class="menu-text"> Price & Converage </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="<?php echo $__env->yieldContent('price_menu_class'); ?>">
							<a href="<?php echo e(route('user.priceList')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Price
							</a>
						</li>
					</ul>
				</li>

				<!-- Reports -->
				<li class="<?php echo $__env->yieldContent('reports_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-bar-chart-o"></i>
						<span class="menu-text"> Reports </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
                        <li class="<?php echo $__env->yieldContent('pending_sms_report_menu_class'); ?>">
                            <a href="<?php echo e(route('user.reports.pending_sms')); ?>">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"> Pending SMS Report </span>
                            </a>
                        </li>
                        <li class="<?php echo $__env->yieldContent('rejected_sms_report_menu_class'); ?>">
                            <a href="<?php echo e(route('user.reports.rejected_sms')); ?>">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"> Rejected SMS Report </span>
                            </a>
                        </li>
						<li class="<?php echo $__env->yieldContent('view_dlr_menu_class'); ?>">
							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> View DLR </span>
								<b class="arrow fa fa-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="<?php echo $__env->yieldContent('todays_sms_report_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.todays_sms')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Today's SMS Report </span>
									</a>
								</li>

								<li class="<?php echo $__env->yieldContent('archived_report_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.archived_sms')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Archived Report </span>
									</a>
								</li>
							</ul>
						</li>

						<li class="<?php echo $__env->yieldContent('campaign_dlr_menu_class'); ?>">
							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> Campaign DLR </span>
								<b class="arrow fa fa-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="<?php echo $__env->yieldContent('todays_campaign_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.todays_campaign')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Today's Campaign </span>
									</a>
								</li>

								<li class="<?php echo $__env->yieldContent('archived_campaign_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.archived_campaign')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Archived Campaign </span>
									</a>
								</li>
							</ul>
						</li>

						<li class="<?php echo $__env->yieldContent('schedule_sms_menu_class'); ?>">
							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> Schedule SMS </span>
								<b class="arrow fa fa-angle-down"></b>
							</a>
							<ul class="submenu">

								<li class="<?php echo $__env->yieldContent('general_pending_sms_send_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.schedule_pending_sms')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Pending SMS</span>
									</a>
								</li>

								<li class="<?php echo $__env->yieldContent('today_pending_sms_send_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.schedule_today_sms')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Today SMS Send </span>
									</a>
								</li>

								<li class="<?php echo $__env->yieldContent('campaign_sms_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.schedule_archieved_sms')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Archieved SMS </span>
									</a>
								</li>
							</ul>
						</li>

						<li class="<?php echo $__env->yieldContent('sms_bill_report_menu_class'); ?>">
							<a href="<?php echo e(route('user.reports.bill-report')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> SMS Bill Report </span>
							</a>
						</li>
					</ul>
				</li>

				<!--Phone Book-->
				<li class="<?php echo $__env->yieldContent('phone_book_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-book"></i>

						<span class="menu-text">
							Phone Book
							<span class="badge badge-primary"></span>
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="<?php echo $__env->yieldContent('contact_and_group_menu_class'); ?>">
							<a href="<?php echo e(route('user.phonebook.index')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Contacts & Group
							</a>
						</li>
					</ul>
				</li>
				<?php endif; ?>
				<?php if($dynamic_permission): ?>
				<li class="<?php echo $__env->yieldContent('dynamic_messaging_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-envelope-o"></i>
						<span class="menu-text">
							Route 2 Messaging
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						
						<li class="<?php echo $__env->yieldContent('send_sms_modem_class'); ?>">
							<a href="<?php echo e(route('user.dynamic-sms.send')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Send
							</a>
						</li>

						<li class="<?php echo $__env->yieldContent('campaign_menu_class'); ?>">
							<a href="<?php echo e(route('user.dynamic-sms.campaign')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Campaign
							</a>
						</li>

						<!-- <li class="<?php echo $__env->yieldContent('sender_id_menu_class'); ?>">
							<a href="<?php echo e(route('user.senderIDList')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Sender ID
							</a>
						</li>
 -->
						<!-- <li class="<?php echo $__env->yieldContent('templates_menu_class'); ?>">
							<a href="<?php echo e(route('user.template.index')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Templates
							</a>
						</li> -->
					</ul>
				</li>

				<!--Price & Converage-->
				<li class="<?php echo $__env->yieldContent('dynamic_price_and_converage_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-credit-card"></i>
						<span class="menu-text">Route 2 Price</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="<?php echo $__env->yieldContent('dynamic_price_menu_class'); ?>">
							<a href="<?php echo e(route('user.priceListDynamic')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								Price
							</a>
						</li>
					</ul>
				</li>

				<!-- Reports -->
				<li class="<?php echo $__env->yieldContent('dynamic_reports_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-bar-chart-o"></i>
						<span class="menu-text"> Route 2 Reports </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<?php if(Auth::user()->userDetail->campaign_reschedule == 1): ?>
                       <li class="<?php echo $__env->yieldContent('pending_sms_report_menu_class'); ?>">
                            <a href="<?php echo e(route('user.reports.pending-sms-dynamic')); ?>">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"> Pending SMS Report </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
						<li class="<?php echo $__env->yieldContent('dynamic_view_dlr_menu_class'); ?>">
							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> View DLR </span>
								<b class="arrow fa fa-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="<?php echo $__env->yieldContent('dynamic_todays_sms_report_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.todays-sms-dynamic')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Today's SMS Report </span>
									</a>
								</li>

								<li class="<?php echo $__env->yieldContent('dynamic_archived_report_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.dynamic-archived-sms')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Archived Report </span>
									</a>
								</li>
							</ul>
						</li>

						<li class="<?php echo $__env->yieldContent('dynamic_campaign_dlr_menu_class'); ?>">
							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> Campaign DLR </span>
								<b class="arrow fa fa-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="<?php echo $__env->yieldContent('dynamic_todays_campaign_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.todays_campaign_dynamic')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Today's Campaign </span>
									</a>
								</li>

								<li class="<?php echo $__env->yieldContent('dynamic_archived_campaign_menu_class'); ?>">
									<a href="<?php echo e(route('user.reports.archived_campaign_dynamic')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> Archived Campaign </span>
									</a>
								</li>
							</ul>
						</li>

						

						
					</ul>
				</li>

				

				<!--Phone Book-->
				
				<?php endif; ?>

				
				<li class="<?php echo $__env->yieldContent('load_menu_class'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-book"></i>

						<span class="menu-text">
							Flexiload
							<span class="badge badge-danger">new</span>
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<!-- Flexiload F -->


					
					<ul class="submenu">
						<?php if($flexi_permission): ?>
						<li class="<?php echo $__env->yieldContent('menu_flexibook'); ?>">
							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> Flexibook </span>
								<b class="arrow fa fa-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="<?php echo $__env->yieldContent('menu_create_flexibook'); ?>">
									<a href="<?php echo e(route('user.flexiload.flexibook_create')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										<span class="menu-text"> All Books   </span>
									</a>
								</li>

							</ul>
						</li>
						<?php endif; ?>
						<li class="<?php echo $__env->yieldContent('submenu_load'); ?>">
							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> Load </span>
								<b class="arrow fa fa-angle-down"></b>
							</a>
								<ul class="submenu">

								<?php ($permission = explode('-', auth()->user()->flexiload_type )); ?>
								<?php if($flexi_permission): ?>
								<?php if( in_array(1, $permission)): ?>
								<li class="<?php echo $__env->yieldContent('flexiload_make_menu_class'); ?>">
									<a href="<?php echo e(route('user.flexiload.flexiloadForm')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										Single Flexiload
									</a>
								</li>
								<?php endif; ?>
								<?php endif; ?>

								
								<!--
																								<li class="<?php echo $__env->yieldContent('load_make_menu_class'); ?>">
																								    <a href="<?php echo e(route('user.create')); ?>">
																									<i class="menu-icon fa fa-caret-right"></i>
																									Buy a Package
																								    </a>
																								</li>
																								-->

								<li class="<?php echo $__env->yieldContent('load_make_check_class'); ?>">
									<a href="<?php echo e(url('offer-check')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										Buy a Offer
									</a>
								</li>
								

								<?php if($flexi_permission): ?>
								<?php if( in_array(3, $permission)): ?>
								<li class="<?php echo $__env->yieldContent('bulk_flexiload_make_menu_class'); ?>">
									<a href="<?php echo e(route('user.flexiload.bulkLoadForm')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										Bulk Flexiload
									</a>
								</li>
								<?php endif; ?>
								<?php endif; ?>
							</ul>
						</li>
						
						<li class="<?php echo $__env->yieldContent('submenu_load_history'); ?>">
							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> History </span>
								<b class="arrow fa fa-angle-down"></b>
							</a>
							<ul class="submenu">
								<?php if($flexi_permission): ?>
								<li class="<?php echo $__env->yieldContent('load_view_menu_class'); ?>">
									<a href="<?php echo e(route('user.flexiload.history')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										Current Month
									</a>
								</li>
								<?php endif; ?>
								<li class="<?php echo $__env->yieldContent('load_package_history_menu_class'); ?>">
									<a href="<?php echo e(route('user.package_history')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										Package History
									</a>
								</li>
								<?php if($flexi_permission): ?>
								<li class="<?php echo $__env->yieldContent('load_archieve_history_menu_class'); ?>">
									<a href="<?php echo e(route('user.flexiload.history_archieve')); ?>">
										<i class="menu-icon fa fa-caret-right"></i>
										Archieve
									</a>
								</li>
								<?php endif; ?>
							</ul>
						</li>
						
                        <!--API documentation-->
                        

                    </ul>
					
				</li>

				<!--API documentation-->
				

				<li class="<?php echo $__env->yieldContent('developer_api_progress_menu_class'); ?>">
					<a href="<?php echo e(route('user.developer.api')); ?>">
						<i class="menu-icon fa fa-hdd-o"></i>
						<span class="menu-text"> Developer API </span>
					</a>
				</li>

				<li class="<?php echo $__env->yieldContent('api_menu'); ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-hdd-o"></i>
						<span class="menu-text"> API </span>
						<b class="arrow fa fa-angle-down"></b>
					</a>
					<ul class="submenu">
						<?php if($sms_permission && Auth::user()->userDetail->api_permission == 1): ?>
						<li class="<?php echo $__env->yieldContent('api_progress_menu_class'); ?>">
							<a href="<?php echo e(route('user.developerApi')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								SMS
							</a>
						</li>
						<?php endif; ?>
						<?php if($flexi_permission): ?>
						<li class="<?php echo $__env->yieldContent('flexiload_api_progress_menu_class'); ?>">
							<a href="<?php echo e(route('user.flexiload.developer-api')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								FLEXI
							</a>
						</li>
						<?php endif; ?>
						<?php if($dynamic_permission): ?>
						<li class="<?php echo $__env->yieldContent('dynamic_api_progress_menu_class'); ?>">
							<a href="<?php echo e(route('user.desktopApi')); ?>">
								<i class="menu-icon fa fa-caret-right"></i>
								DYNAMIC
							</a>
						</li>
						<?php endif; ?>
					</ul>
				</li>
				<li class="<?php echo $__env->yieldContent('support_ticket'); ?>">
				    <a href="<?php echo e(route('user.support.tickets')); ?>">
					<i class="menu-icon fa fa-life-ring"></i>
					<span class="menu-text"> Support Tickets </span>
				    </a>
				</li>
				<li class="<?php echo $__env->yieldContent('balance_menu'); ?>">
					<a href="<?php echo e(route('user.user-balance-statements.balance')); ?>" >
						<i class="menu-icon fa fa-hdd-o"></i>
						<span class="menu-text"> Statements </span>
					</a>
					
				</li>

				<?php if($sms_permission || $dynamic_permission): ?>
				<li class="<?php echo $__env->yieldContent('campaign_report_menu'); ?>">
					<a href="<?php echo e(route('user.campaign-report.campaign-report')); ?>" >
						<i class="menu-icon fa fa-hdd-o"></i>
						<span class="menu-text"> Campaign Report </span>
					</a>
					
				</li>
				<?php endif; ?>
		<li class="">
    <a href="<?php echo e(route('user.balance.topup')); ?>" style="height: 50px; display: flex; align-items: center; padding: 0 10px;">
        <img src="<?php echo e(asset('assets/images/topup_logo.png')); ?>" 
             style="width: 30px; height: 30px; margin-right: 10px;">
        <span style="font-size: 16px; font-weight: bold; color: #28a745;">Add Funds</span>
    </a>

    <b class="arrow"></b>
</li>




				<li class="">
					<a href="http://unicodeconverter.info/" target="_blank" style="height: 50px;">
						<img src="<?php echo e(asset('assets')); ?>/images/unicode_logo.png" style="width: 100%;">
					</a>

					<b class="arrow"></b>
				</li>
				
			</ul><!-- /.nav-list -->

			<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
				<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
			</div>
		</div>
