		@php
		$emp_id = Auth::guard('employee')->id();
        $emp_create = App\Model\EmployeeUser::where('id',$emp_id)->first(); 
        
		$create_by = App\Model\User::where('id',$emp_create->create_by)->first();

		$permission = App\Model\User::where('id',$create_by->create_by)->first();

		// dd($permission);
		if ($permission->report_permission == 1 || $create_by->report_permission == 1) {
			$permit = true;
		}else {
			$permit = false;
		}
		@endphp
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
					<a href="{{ route('employee.index') }} ">
						<i class="menu-icon fa fa-bell-o"></i>
						<span class="menu-text" style="font-weight: bold;"> BDT {{ number_format(BalanceHelper::getEmployeeBalance(Auth::guard('employee')->user()->id), 2) }} </span>
						<b style="font-size: 15px;">৳</b>
					</a>
				</li>

				
				<!--Reseller Limit-->
				<li class="@yield('employee_users')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon  fa fa-tag"></i>
						<span class="menu-text"> Users </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('employee_users_lists')">
							<a href=" {{route('employee.user_list')}} ">
								<i class="menu-icon fa fa-caret-right"></i>
								User List
							</a>
						</li>

						<li class="@yield('employee_low_balance_users_lists')">
							<a href=" {{route('employee.low_balance_users_list')}} ">
								<i class="menu-icon fa fa-caret-right"></i>
								Low Balance List
							</a>
						</li>
					</ul> 
				</li>

				<li class="@yield('submenu_load')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-caret-right"></i>
						<span class="menu-text"> Load </span>
						<b class="arrow fa fa-angle-down"></b>
					</a>
						<ul class="submenu">

						
						
						
						{{-- @if( in_array(2, $permission)) --}}
						<li class="@yield('load_make_menu_class')">
							<a href="{{ route('employee.package.package-list') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Buy a Package
							</a>
						</li>
						<li class="@yield('employee_load_history')">
							<a href="{{ route('employee.package.package-history') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Load History
							</a>
						</li>
						{{-- @endif --}}
						
					
						
					</ul>
				</li>

				@if($permit)
				<li class="@yield('report_class')">
					<a href="{{ route('employee.report') }}">
						<i class="menu-icon fa fa-caret-right"></i>
						<span class="menu-text"> Report </span>
					</a>
				</li>
				@endif

				
				<!--Accounts-->
				<!--
				<li class="@yield('account_menu_class')">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-folder-open-o"></i>
						<span class="menu-text"> Account's </span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<ul class="submenu">
						<li class="@yield('employee_transaction_history')">
							<a href="{{ '' }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Transaction History
							</a>
						</li>

					</ul> 
				</li>
				-->
				
			</ul><!-- /.nav-list -->

			<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
				<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
			</div>
		</div>
