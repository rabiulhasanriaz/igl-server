<div id="navbar" class="navbar navbar-default ace-save-state">
    <div class=" ace-save-state" id="">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>
        </button>

			<div class="navbar-header pull-left">
               <a href="{{ route('employee.index') }}" class="navbar-brand" style="padding: 0;background: #fff;">
                   <img src="{{ OtherHelpers::emp_company_logo() }}" alt=" {{ OtherHelpers::emp_company_logo() }} " class="nav-user-photo"
                        style="height: 45px; width: 190px; padding: 0;">
               </a>
			</div>

			<div class="col-lg-8 col-md-8 hidden-sm hidden-xs navbar-buttons navbar-header" role="navigation">
			    <h4 class="text-center" style="color: #fff;">Hotline:{{ OtherHelpers::employee_hotline() }}</h4>
			</div>

			<div class="navbar-buttons navbar-header pull-right" role="navigation">
				<ul class="nav ace-nav">
					@php($logo = 'assets/uploads/User_Logo/' . Auth::guard('employee')->user()->avatar )
					
					<li class="light-blue dropdown-modal">
						<a data-toggle="dropdown" href="#" class="dropdown-toggle">
							@if( file_exists( $logo ) )
								<img class="nav-user-photo" src="{{ asset('assets') }}/uploads/User_Logo/{{ Auth::guard('employee')->user()->avatar }}" alt="{{ Auth::guard('employee')->user()->name }}" />
							@else
								<img class="nav-user-photo" src="{{ asset('assets') }}/images/avatars/avatar5.png" alt="{{ Auth::guard('employee')->user()->name }}" />
							@endif
							<span class="user-info">
								<small>Welcome,</small>
								{{ Auth::guard('employee')->user()->name }}
							</span>

							<i class="ace-icon fa fa-caret-down"></i>
						</a>

						<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
							<li>
							    <a href="{{ route('employee.profile') }}">
							        <i class="ace-icon fa fa-user"></i>
							        Profile
							    </a>
							</li>

							<li>
								<a href="{{ route('employee.change_password') }}">
									<i class="ace-icon fa fa-cog"></i>
									Change Password
								</a>
							</li>

							<li class="divider"></li>

							<li>
								<a href="{{ route('employee.logout') }}">
									<i class="ace-icon fa fa-power-off"></i>
									Logout
								</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div><!-- /.navbar-container -->
	</div>
