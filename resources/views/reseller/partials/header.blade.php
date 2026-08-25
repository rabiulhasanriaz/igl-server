<div id="navbar" class="navbar navbar-default          ace-save-state">
		<div class=" ace-save-state" id="navbar">
			<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
				<span class="sr-only">Toggle sidebar</span>

				<span class="icon-bar"></span>

				<span class="icon-bar"></span>

				<span class="icon-bar"></span>
			</button>

			<div class="navbar-header pull-left">
				<a href="{{ route('user.index') }}" class="navbar-brand" style="padding: 0;background: #fff;">
					<img src="{{ OtherHelpers::website_logo() }}" alt="Bulk Sms" class="nav-user-photo"
						 style="height: 45px; width: 190px; padding: 0;">
				</a>
			</div>

            <div class="col-lg-8 col-md-8 hidden-sm hidden-xs navbar-buttons navbar-header" role="navigation">
                <h4 class="text-center" style="color: #fff;">Hotline:{{ OtherHelpers::user_hotline() }}</h4>

            </div>

			<div class="navbar-buttons navbar-header pull-right" role="navigation">
				<ul class="nav ace-nav">
					
					<li class="light-blue dropdown-modal">
						<a data-toggle="dropdown" href="#" class="dropdown-toggle">
							<img class="nav-user-photo" src="{{ OtherHelpers::user_logo(Auth::user()->userDetail->logo)  }}" alt="Jason's Photo" />
							<span class="user-info">
								<small>Welcome,</small>
								{{ \Illuminate\Support\Facades\Auth::user()->userDetail['name'] }}
							</span>

							<i class="ace-icon fa fa-caret-down"></i>
						</a>

						<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
							<li>
								<a href="{{ route('reseller.profile') }}">
									<i class="ace-icon fa fa-user"></i>
									Profile
								</a>
							</li>

							<li>
								<a href="{{ route('reseller.change-password') }}">
									<i class="ace-icon fa fa-cog"></i>
									Change Password
								</a>
							</li>

							<li class="divider"></li>

							<li>
								<a href="{{ route('logout') }}">
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
