		<div id="sidebar" class="sidebar responsive ace-save-state">
			<script type="text/javascript">
				try{ace.settings.loadState('sidebar')}catch(e){}
			</script>

			<div class="sidebar-shortcuts" id="sidebar-shortcuts">
				<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
					<button class="btn btn-success">
						<i class="ace-icon fa fa-signal"></i>
					</button>

					<button class="btn btn-info">
						<i class="ace-icon fa fa-pencil"></i>
					</button>

					<button class="btn btn-warning">
						<i class="ace-icon fa fa-users"></i>
					</button>

					<button class="btn btn-danger">
						<i class="ace-icon fa fa-cogs"></i>
					</button>
				</div>

				<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
					<span class="btn btn-success"></span>

					<span class="btn btn-info"></span>

					<span class="btn btn-warning"></span>

					<span class="btn btn-danger"></span>
				</div>
			</div><!-- /.sidebar-shortcuts -->

			<ul class="nav nav-list">
				<li class="@yield('dashboard_menu_class')">
					<a href="{{ route('admin.index') }}">
						<i class="menu-icon fa fa-tachometer"></i>
						<span class="menu-text"> Dashboard </span>
					</a>
				</li>

                <li class="@yield('pending_sms_menu_class')">
					<a href="{{ route('admin.pending-campaign-sms') }}">
						<i class="menu-icon fa fa-tachometer"></i>
						<span class="menu-text"> Pending Campaign </span>
					</a>
				</li>
<li class="@yield('sms_monitoring')">
					<a href="{{ route('admin.reseller.sms_monitoring') }}">
						<i class="menu-icon fa fa-tachometer"></i>
						<span class="menu-text"> Monitoring </span>
					</a>
				</li>
				<li class="@yield('api_comission_class')">
					<a href="{{ route('admin.api-permission') }}">
						<i class="menu-icon fa fa-tachometer"></i>
						<span class="menu-text"> API Permission </span>
					</a>
				</li>

				<li class="@yield('dynamic_comission_class')">
					<a href="{{ route('admin.dynamic-permission') }}">
						<i class="menu-icon fa fa-tachometer"></i>
						<span class="menu-text"> Dynamic Permission </span>
					</a>
				</li>

				<li class="@yield('campaign_reshedule_class')">
					<a href="{{ route('admin.campaign-reshedule-permission') }}">
						<i class="menu-icon fa fa-tachometer"></i>
						<span class="menu-text"> Reschedule </span>
					</a>
				</li>
				

				<li class="@yield('campaign_permission_class')">
					<a href="{{ route('admin.campaign-permission') }}">
						<i class="menu-icon fa fa-tachometer"></i>
						<span class="menu-text"> Camp. Permission </span>
					</a>
				</li>

				<li class="@yield('route2_class')">
					<a href="{{ route('admin.route-2-report') }}">
						<i class="menu-icon fa fa-tachometer"></i>
						<span class="menu-text"> Route 2 Report </span>
					</a>
				</li>

				<li class="@yield('English_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-users"></i>
						<span class="menu-text">
							English
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('route_register')">
							<a href="{{ route('admin.english.route-registers') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Route Register
							</a>
						</li>
						<li class="@yield('assign_route')">
							<a href="{{ route('admin.english.assign-route') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Assign Route
							</a>
						</li>

						


					</ul>
				</li>

				<!--Reseller Menu-->
				<li class="@yield('reseller_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-users"></i>
						<span class="menu-text">
							Reseller
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('reseller_registration_menu_class')">
							<a href="{{ route('admin.reseller.create') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Reseller Registration
							</a>
						</li>

						<li class="@yield('reseller_list_menu_class')">
							<a href="{{ route('admin.reseller.index') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Reseller List
							</a>
						</li>

						<li class="@yield('reseller_tree_menu_class')">
							<a href="{{ route('admin.reseller.tree') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Reseller Tree
							</a>
						</li>
						

						<!--
							<li class="@yield('assign_employee_limit')">
								<a href="{{ route('admin.reseller.employee_limit') }}">
									<i class="menu-icon fa fa-caret-right"></i>
									Employee limit
								</a>
							</li>
						-->


					</ul>
				</li>


				<!--Employee Menu-->
				<!--
				<li class="@yield('employee_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-users"></i>
						<span class="menu-text">
							Employee
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('employee_registration_menu_class')">
							<a href="{{--{{ route('admin.employee.create') }}--}}#">
								<i class="menu-icon fa fa-caret-right"></i>
								Employee Registration
							</a>
						</li>

						<li class="@yield('employee_list_menu_class')">
							<a href="{{--{{ route('admin.employee.index') }}--}}#">
								<i class="menu-icon fa fa-caret-right"></i>
								Employee List
							</a>
						</li>

					</ul>
				</li>
				-->


				<!--Reseller Limit-->
				<li class="@yield('reseller_ac_limit_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon  fa fa-tag"></i>
						<span class="menu-text"> Reseller A/C Limit </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('limit_apply_menu_class')">
							<a href="{{ route('admin.reseller.limitApply') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Limit Apply
							</a>
						</li>
					</ul>
				</li>

				<!-- Virtual Number -->
				<li class="@yield('virtual_number_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-fighter-jet"></i>
						<span class="menu-text"> Virtual Number </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('add_virtual_number_menu_class')">
							<a href="{{ route('admin.virtualNumber.create') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Add Virtual Number
							</a>
						</li>

						<li class="@yield('virtual_number_list_menu_class')">
							<a href="{{ route('admin.virtualNumber.index') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Virtual Number List
							</a>
						</li>
						<li class="@yield('low_balance_virtual_number_menu_class')">
						    <a href="{{ route('admin.virtualNumber.low_balance') }}">
							<i class="menu-icon fa fa-caret-right"></i>
							Low Balance Virtual Number List
						    </a>
						</li>
					</ul>
				</li>


			<!-- Whitelisted IP Management -->
			<li class="@yield('whitelisted_ip_menu_class')">
			    <a href="#" class="dropdown-toggle">
				<i class="menu-icon fa fa-shield"></i>
				<span class="menu-text"> Whitelisted IP </span>

				<b class="arrow fa fa-angle-down"></b>
			    </a>

			    <ul class="submenu">
				<li class="@yield('add_whitelisted_ip_menu_class')">
				    <a href="{{ route('admin.whitelistedIp.create') }}">
					<i class="menu-icon fa fa-caret-right"></i>
					Add Whitelisted IP
				    </a>
				</li>

				<li class="@yield('whitelisted_ip_list_menu_class')">
				    <a href="{{ route('admin.whitelistedIp.index') }}">
					<i class="menu-icon fa fa-caret-right"></i>
					Whitelisted IP List
				    </a>
				</li>
				  <li class="@yield('non_whitelisted_menu_class')">
            <a href="{{ route('admin.whitelistedIp.non_whitelisted') }}">
                <i class="menu-icon fa fa-caret-right"></i>
                Non-Whitelisted IP
               
            </a>
        </li>
			    </ul>
			</li>
				<!--Sender Id-->
				<li class="@yield('sender_id_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-pencil-square-o"></i>
						<span class="menu-text"> Sender ID </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('add_sender_id_menu_class')">
							<a href="{{ route('admin.senderID.create') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Add Sender ID
							</a>
						</li>

						<li class="@yield('sender_id_list_menu_class')">
							<a href="{{ route('admin.senderID.index') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Sender ID List
							</a>
						</li>

						<li class="@yield('delivery_sender_id_menu_class')">
							<a href="{{ route('admin.senderID.deliverySenderIDList') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Delivery Sender ID
							</a>
						</li>

						<li class="@yield('user_sender_id_menu_class')">
							<a href="{{ route('admin.senderID.userSenderID.index') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								User Sender ID
							</a>
						</li>
					</ul>
				</li>

				<!--Non Masking-->
				<li class="@yield('non_masking_menu_class')">
					<a href="{{ route('admin.senderID.nonMaskingSenderID.index') }}">
						<i class="menu-icon fa fa-calendar"></i>
						<span class="menu-text"> Non Masking </span>
					</a>
				</li>

				{{-- Flexiload --}}
				<li class="@yield('flexiload_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-usd"></i>

						<span class="menu-text">
							Flexiload
							<span class="badge badge-primary"></span>
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>
					<ul class="submenu">
						<li class="@yield('flexiload_view_menu_class')">
							<a href="{{ route('admin.flexiload.allUsers') }}">
								<i class=""></i>
								<span class="menu-text"> View All </span>
							</a>
						</li>
						<li class="@yield('flexi_commission_view_menu_class')">
							<a href="{{ route('admin.flexiload.setComissions') }}">
								<i class=""></i>
								<span class="menu-text"> Set Commissions </span>
							</a>
						</li>
						<li class="@yield('flexiload_packages_menu_class')">
							<a href="{{ route('admin.flexiload.allPackages') }}">
								<i class=""></i>
								<span class="menu-text"> All Packages </span>
							</a>
						</li>
						<li class="@yield('flexiload_trx_class')">
							<a href="{{ route('admin.flexiload.set-trx-page') }}">
								<i class=""></i>
								<span class="menu-text"> Set Trx ID </span>
							</a>
						</li>
						<li class="@yield('flexiload_balance_class')">
							<a href="{{ route('admin.flexiload.balance-enquiry') }}">
								<i class=""></i>
								<span class="menu-text"> Balance </span>
							</a>
						</li>
									<li class="@yield('flexiload_history_menu_class')">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon fa fa-history bg-orange"></i>
        <span class="menu-text"> Load History </span>
        <b class="arrow fa fa-angle-down"></b>
    </a>
    <ul class="submenu">
        <li class="@yield('flexiload_history_summary_class')">
            <a href="{{ route('admin.flexiload.history-summary') }}">
                <i class="menu-icon fa fa-dashboard bg-blue"></i>
                History Summary
            </a>
            <b class="arrow"></b>
        </li>
        <li class="@yield('flexiload_user_wise_history_class')">
            <a href="{{ route('admin.flexiload.user-wise-history') }}">
                <i class="menu-icon fa fa-users bg-green"></i>
                User-wise History
            </a>
            <b class="arrow"></b>
        </li>
        <li class="@yield('flexiload_number_wise_history_class')">
            <a href="{{ route('admin.flexiload.number-wise-history') }}">
                <i class="menu-icon fa fa-phone bg-purple"></i>
                Number-wise History
            </a>
            <b class="arrow"></b>
        </li>
    </ul>
</li>
						<li class="@yield('flexiload_load_message_class')">
							<a href="{{ route('admin.flexiload.load-message') }}">
								<i class=""></i>
								<span class="menu-text"> Load Message </span>
							</a>
						</li>
						{{-- API Registration Menu Developed --}}
						{{-- Ab A Noman --}}
						<li class="@yield('flexiload_api_view_class')">
							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-book"></i>
								<span class="menu-text"> Load API Port </span>
		
								<b class="arrow fa fa-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="@yield('flexiload_load_api_class')">
									<a href="{{ route('admin.flexiload.load-api-create') }}">
										<i class=""></i>
										<span class="menu-text"> Port Register </span>
									</a>
								</li>
								<li class="@yield('flexiload_load_api_all_class')">
									<a href="{{ route('admin.flexiload.load-api') }}">
										<i class=""></i>
										<span class="menu-text"> All Port </span>
									</a>
								</li>
							</ul> 
						</li>
					</ul>
				</li>

				<!--Accounts-->
				<li class="@yield('account_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-folder-open-o"></i>
						<span class="menu-text"> Account's </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('add_fund_credit_menu_class')">
							<a href="{{ route('admin.balance.credit.create') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Add Fund Credit
							</a>
						</li>

						<li class="@yield('add_fund_debit_menu_class')">
							<a href="{{ route('admin.balance.debit.create') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Add Fund Debit
							</a>
						</li>

						<!-- <li class="@yield('check_api_balance_menu_class')">
							<a href="pricing.html">
								<i class="menu-icon fa fa-caret-right"></i>
								Check API Balance
							</a>
						</li> -->
					</ul>
				</li>

				<!--Phone Book-->
				<li class="@yield('phone_book_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-book"></i>

						<span class="menu-text">
							Phone Book
							<span class="badge badge-primary"></span>
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('category_contact_menu_class')">
							<a href="{{ route('admin.categoryContact.index') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Category Contact
							</a>
						</li>
					</ul>
				</li>

					<li class="@yield('sms_flexi_report_class')">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon fa fa-book"></i>
        <span class="menu-text"> Reports </span>

        <b class="arrow fa fa-angle-down"></b>
    </a>

    <ul class="submenu">
        <li class="@yield('sms_flexi_class')">
            <a href="{{ route('admin.reports.sms-flexi-reports') }}">
                <i class="menu-icon fa fa-caret-right"></i>
                SMS/Flexi
            </a>
        </li>
        <li class="@yield('operator_class')">
            <a href="{{ route('admin.reports.operator-reports') }}">
                <i class="menu-icon fa fa-caret-right"></i>
                <span class="menu-text"> Operator Reports </span>
            </a>
        </li>
        <li class="@yield('balance_report_active_class')">
            <a href="{{ route('admin.reports.balance-transaction-reports') }}">
                <i class="menu-icon fa fa-caret-right"></i>
                <span class="menu-text"> Balance Transaction Reports </span>
            </a>
        </li>
        <li class="@yield('sender_operator_report_class')">
            <a href="{{ route('admin.reports.sender-operator-report') }}">
                <i class="menu-icon fa fa-caret-right"></i>
                <span class="menu-text"> Sender Operator Report </span>
            </a>
        </li>
    </ul> 
</li>
													<li class="@yield('support_menu_class')">
		    <a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-life-ring"></i>
			<span class="menu-text"> Support Tickets </span>
			<b class="arrow fa fa-angle-down"></b>
		    </a>

		    <ul class="submenu">
			<li class="@yield('support_tickets_class')">
			    <a href="{{ route('admin.ticket.tickets') }}">
				<i class="menu-icon fa fa-caret-right"></i>
				All Tickets
			    </a>
			</li>
			<li class="@yield('support_my_tickets_class')">
			    <a href="{{ route('admin.ticket.myTickets') }}">
				<i class="menu-icon fa fa-caret-right"></i>
				My Tickets
			    </a>
			</li>
			
		    </ul>
		</li>




					{{-- All looged in Users --}}
					<li class="@yield('all_logged_in_users_menu')">
						<a href="{{ route('admin.loggedInUsers') }}">
							<i class="menu-icon fa fa-calendar"></i>
							<span class="menu-text"> Logged Users </span>
						</a>
					</li>

				<!-- editing -->
					<li class="@yield('extra_operation_menu_class')">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon fa fa-book"></i>

							<span class="menu-text">
								Sensitive Operations
								<span class="badge badge-primary"></span>
							</span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<ul class="submenu">
							<!-- Log in background image  -->
							<li class="@yield('change_background_menu_class')">
								<a href="{{ route('admin.changeBackground') }}">
									<i class=""></i>
									<span class="menu-text"> Login Background </span>
								</a>
							</li>

							<!-- Delete data before one month  -->
							<li class="@yield('delete_data_menu_class')">
								<a href="{{ route('admin.deleteDataBeforeOneMonth') }}">
									<i class=""></i>
									<span class="menu-text"> Delete Data </span>
								</a>
							</li>




						</ul>
					</li>
				<!-- editing  end-->


			</ul><!-- /.nav-list -->

			<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
				<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
			</div>
		</div>
