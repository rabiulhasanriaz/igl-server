<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Bulk SMS</title>


		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<!-- bootstrap & fontawesome -->
        <link rel="icon" href="{{ OtherHelpers::website_logo() }}" type="/favicon.ico">
		<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap.min.css" />
		<link rel="stylesheet" href="{{ asset('assets') }}/font-awesome/4.5.0/css/font-awesome.min.css" />

		<!-- page specific plugin styles -->

		<!-- text fonts -->
		<link rel="stylesheet" href="{{ asset('assets') }}/css/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="{{ asset('assets') }}/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

		<link rel="stylesheet" href="{{ asset('assets') }}/css/ace-skins.min.css" />
		<link rel="stylesheet" href="{{ asset('assets') }}/css/ace-rtl.min.css" />

		<style>
			::-webkit-scrollbar {
            width: 10px;
            height: 8px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 2px grey;
            border-radius: 10px;
        }
        /* Track */
        ::-webkit-scrollbar-track:hover {
            box-shadow: inset 0 0 5px black;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #1d6cc7;
            border-radius: 10px;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #b32a00;
        }
		</style>

	@yield('custom_style')
		<link rel="stylesheet" href="{{ asset('assets') }}/css/loader.css" />
		<link rel="stylesheet" href="{{ asset('assets') }}/css/custom.css?v=1.0.1" />


		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="{{ asset('assets') }}/js/ace-extra.min.js"></script>


	</head>

<body class="no-skin">



<div id="loading">
    <div id="loading-center">
        <div id="loading-center-absolute">
            <div class="object"></div>
            <div class="object"></div>
            <div class="object"></div>
            <div class="object"></div>
            <div class="object"></div>
            <div class="object"></div>
            <div class="object"></div>
            <div class="object"></div>
            <div class="object"></div>
            <div class="object"></div>
        </div>
    </div>
</div>


	@include('user.partials.header')


	<div class="main-container ace-save-state" id="main-container">
		<script type="text/javascript">
			try{ace.settings.loadState('main-container')}catch(e){}
		</script>

		@include('user.partials.sidebar')

		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs ace-save-state" id="breadcrumbs">

					@yield('page_location')


					<!-- <div class="nav-search" id="nav-search">
						<form class="form-search">
							<span class="input-icon">
								<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
								<i class="ace-icon fa fa-search nav-search-icon"></i>
							</span>
						</form>
					</div> --><!-- /.nav-search -->
				</div>

				<div class="page-content">
					<div class="ace-settings-container" id="ace-settings-container">
						{{--
						<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
							<i class="ace-icon fa fa-cog bigger-130"></i>
						</div>
						--}}

						<div class="ace-settings-box clearfix" id="ace-settings-box">
							<div class="pull-left width-50">
								<div class="ace-settings-item">
									<div class="pull-left">
										<select id="skin-colorpicker" class="hide">
											<option data-skin="no-skin" value="#438EB9">#438EB9</option>
											<option data-skin="skin-1" value="#222A2D">#222A2D</option>
											<option data-skin="skin-2" value="#C6487E">#C6487E</option>
											<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
										</select>
									</div>
									<span>&nbsp; Choose Skin</span>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar" autocomplete="off" />
									<label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar" autocomplete="off" />
									<label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs" autocomplete="off" />
									<label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" autocomplete="off" />
									<label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-add-container" autocomplete="off" />
									<label class="lbl" for="ace-settings-add-container">
										Inside
										<b>.container</b>
									</label>
								</div>
							</div><!-- /.pull-left -->

							<div class="pull-left width-50">
								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off" />
									<label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off" />
									<label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" autocomplete="off" />
									<label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
								</div>
							</div><!-- /.pull-left -->
						</div><!-- /.ace-settings-box -->
					</div><!-- /.ace-settings-container -->

					<div class="page-header">

						@yield('page_header')

					</div><!-- /.page-header -->

					<div class="row">
						<div class="col-xs-12">
							<!-- PAGE CONTENT BEGINS -->


							@yield('main_content')


							<!-- PAGE CONTENT ENDS -->
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.page-content -->
			</div>
		</div><!-- /.main-content -->

		@include('user.partials.footer')

		<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
			<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
		</a>
	</div><!-- /.main-container -->

	<!-- basic scripts -->

	<!--[if !IE]> -->

	<script src="{{ asset('assets') }}/js/jquery-2.1.4.min.js"></script>
	<!-- <![endif]-->

	<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->

	<script src="{{ asset('assets') }}/js/bootstrap.min.js"></script>

	<!-- page specific plugin scripts -->

	<!--[if lte IE 8]>
	  <script src="assets/js/excanvas.min.js"></script>
	<![endif]-->
	<script src="{{ asset('assets') }}/js/jquery-ui.custom.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.ui.touch-punch.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.easypiechart.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.sparkline.index.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.flot.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.flot.pie.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.flot.resize.min.js"></script>

	<!-- ace scripts -->
	<script src="{{ asset('assets') }}/js/ace-elements.min.js"></script>
	<script src="{{ asset('assets') }}/js/ace.min.js"></script>
    <script src="{{ asset('assets') }}/js/moment.min.js"></script>

    <script type="text/javascript">
        if('ontouchstart' in document.documentElement) document.write("<script src='{{ asset('assets') }}/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");

        $(document).ready(function () {

            let currentSecond = 0;
            let csrfToken = "{{ csrf_token() }}";
            let postUrl = "{{ route('update-login-status') }}";

            /*update active time first time visit into page*/
            $.ajax({
                type: "POST",
                url: postUrl,
                data: {_token: csrfToken},
                success: function (html) {
                }
            });
            setInterval(function () {
                currentSecond++;
                if (currentSecond <= 1201) {
                    if((currentSecond % 60) == 0) {
                        /*update active time every 1 minute max 20*/
                        $.ajax({
                            type: "POST",
                            url: postUrl,
                            data: {_token: csrfToken, currentSecond: currentSecond},
                            success: function (html) {
                            }
                        });
                    }
                }
            }, 1000);
        });
    </script>
     <script>
let balanceTimeout;

document.getElementById('showBalanceBtn').addEventListener('click', function () {
    let balanceSpan = document.getElementById('current_balance');

    // Prevent multiple clicks while showing
    if (balanceSpan.dataset.visible === "true") return;

    balanceSpan.textContent = 'Loading...';

    fetch("{{ route('get-balance') }}")
        .then(response => response.json())
        .then(data => {
            // Show balance + bold ৳
            balanceSpan.innerHTML = data.balance + ' <b>৳</b>';
            balanceSpan.dataset.visible = "true";

            // Clear previous timer if any
            clearTimeout(balanceTimeout);

            // After 20 seconds, hide again
            balanceTimeout = setTimeout(() => {
                balanceSpan.textContent = "Balance";
                balanceSpan.dataset.visible = "false";
            }, 20000);
        })
        .catch(() => {
            balanceSpan.textContent = "Error";
            balanceSpan.dataset.visible = "false";
        });
});
</script>
@yield('custom_script')


</body>
</html>
