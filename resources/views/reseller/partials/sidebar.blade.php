		<div id="sidebar" class="sidebar responsive ace-save-state">
			<script type="text/javascript">
				try{ace.settings.loadState('sidebar')}catch(e){}
			</script>

			<div class="sidebar-shortcuts" id="sidebar-shortcuts">
				<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
					<a href="{{ route('reseller.user.index') }}" class="btn btn-warning">
						<i class="ace-icon fa fa-users"></i>
					</a>

					<a href="{{ route('reseller.senderIDList') }}" class="btn btn-info">
						<i class="fa-credit-card fa fa-signal"></i>
					</a>

					<a href="{{ route('reseller.profile') }}" class="btn btn-success">
						<i class="ace-icon fa fa-user"></i>
					</a>

					<a href="{{ route('reseller.change-password') }}" class="btn btn-danger">
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
				<li class="@yield('dashboard_menu_class')">
					<a href="{{ route('reseller.index') }}">
						  <i class="menu-icon fa fa-tachometer"></i>
        <span class="menu-text">Dashboard</span>
					</a>
				</li>

				<!--Reseller Menu-->
				<li class="@yield('user_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-users"></i>
						<span class="menu-text">
							User
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('user_registration_menu_class')">
							<a href="{{ route('reseller.user.create') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								User Registration
							</a>
						</li>

						<li class="@yield('user_list_menu_class')">
							<a href="{{ route('reseller.user.index') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								User List
							</a>
						</li>
	@php
							$user = Auth::user();  // or Auth::guard('web')->user()
						@endphp
						@if($user && in_array('3', explode(',', $user->permission)))
							<li class="@yield('inactive_user')">
										<a href="{{ route('reseller.user.inactiveUser') }}">
											<i class="menu-icon fa fa-caret-right"></i>
										Inactive User List
									</a>
								</li>
								
								<li class="@yield('sms_activity_menu')">
    <a href="{{ route('reseller.user.smsActivity') }}">
        <i class="menu-icon fa fa-caret-right"></i>
        User Activity
    </a>
    <b class="arrow"></b>
</li>
<li class="@yield('sms_activity_menu_with')">
    <a href="{{ route('reseller.user.smsActivitywithoutbalance') }}">
        <i class="menu-icon fa fa-caret-right"></i>
        Activity Without Balance
    </a>
    <b class="arrow"></b>
</li>
<li class="@yield('sms_activity_menu_monitoring')">
    <a href="{{ route('reseller.user.smsMonitoringDashboard', ['date' => now()->toDateString()]) }}">
        <i class="menu-icon fa fa-caret-right"></i>
        Monitoring
    </a>
    <b class="arrow"></b>
</li>
							@endif

						<li class="@yield('suspend_user_menu_class')">
							<a href="{{ route('reseller.user.suspendUser') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Suspend User List
							</a>
						</li>
						<li class="@yield('low_balance_user_menu')">
    <a href="{{ route('reseller.user.low_balance') }}">
        <i class="menu-icon fa fa-exclamation-triangle"></i>
        Low Balance Users
    </a>
    <b class="arrow"></b>
</li>
					</ul>
				</li>

				<!-- Employee Section -->
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
							<a href="{{ route('reseller.employee.create') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Employee Registration
							</a>
						</li>

						<li class="@yield('employee_list_menu_class')">
							<a href="{{ route('reseller.employee.index') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Employee List
							</a>
						</li>

						<li class="@yield('user_assign_to_employee')">
							<a href="{{ route('reseller.employee.asignUser') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Asign User
							</a>
						</li>

						<li class="@yield('change_employee')">
							<a href="{{ route('reseller.employee.change_employee') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Change Employee
							</a>
						</li>

						<li class="@yield('pay_to_employee')">
							<a href="{{ route('reseller.employee.pay_balance') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Pay to Employee
							</a>
						</li>

					</ul>
				</li>
				<!-- Employee section end -->

				<!--Reseller Limit-->
				<li class="@yield('price_and_converage_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-credit-card"></i>
						<span class="menu-text"> Price & Converage </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('price_menu_class')">
							<a href="{{ route('reseller.priceList') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Price
							</a>
						</li>
					</ul> 
				</li>

				<!--Non Masking-->
				<li class="@yield('sender_id_menu_class')">
					<a href="{{ route('reseller.senderIDList') }}">
						<i class="menu-icon fa fa-calendar"></i>
						<span class="menu-text"> Sender ID </span>
					</a>
				</li>

				<li class="@yield('send_sms_to_all_users')">
					<a href="{{ route('reseller.sendSmsToAll') }}">
						<i class="menu-icon fa fa-calendar"></i>
						<span class="menu-text"> Send Notice </span>
					</a>
				</li>


				<!--Accounts-->
				<li class="@yield('acc_details_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon  fa fa-book"></i>
						<span class="menu-text"> Acc Details </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('add_fund_credit_menu_class')">
							<a href="{{ route('reseller.balance.credit.create') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Add Fund Credit
							</a>
						</li>

						<li class="@yield('add_fund_debit_menu_class')">
							<a href="{{ route('reseller.balance.debit.create') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Add Fund Debit
							</a>
						</li>

						<li class="@yield('user_statement_menu_class')">
							<a href="{{ route('reseller.transactionHistory') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								User Statement
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
			</ul><!-- /.nav-list -->

			<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
				<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
			</div>
		</div>
