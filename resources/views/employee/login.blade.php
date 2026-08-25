<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="utf-8"/>
    <title>Bulk SMS Portal</title>
    <meta name="description" content="User login page"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link rel="stylesheet" href="{{ asset('') }}/assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="{{ asset('') }}/assets/font-awesome/4.5.0/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="{{ asset('') }}/assets/css/fonts.googleapis.com.css"/>
    <link rel="stylesheet" href="{{ asset('') }}/assets/css/ace.min.css"/>
    <link rel="stylesheet" href="{{ asset('') }}/assets/css/ace-rtl.min.css"/>
    <!-- <link rel="stylesheet" href="./assets/css/customice.css"/> -->
    <link rel="icon" href="" type="/favicon.ico">

</head>

<body class="login-layout">
    <div>
        <img src="/assets/uploads/login_bg.png" style="z-index: -999; position: absolute; width: 100%; height: 100%;">
    </div>
<div class="main-container">
    <div class="main-content">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="login-container">
                    <div class="space-6"></div>
                    <div class="space-6"></div>
                    <div class="space-6"></div>
                    <div class="space-16"></div>
                    <div class="space-16"></div>

                    <div class="position-relative">
                        <div id="login-box" class="login-box visible widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header blue lighter bigger">
                                        <p class="text-center">Employee Login Panel</p>
                                    </h4>

                                    <div class="space-6"></div>
                                    @if(session()->has('message'))
                                        <h5 class="header blue lighter bigger">
                                            <p class="text-center text-danger">{{ session()->get('message') }}</p>
                                        </h5>
                                        <div class="space-6"></div>
                                    @endif

                                    <form action="{{ route('auth.employeeLogin') }}" method="post">
                                        @csrf
                                        <fieldset>
                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="text" class="form-control" placeholder="Email or phone" value="{{ old('email') }}" required="" name="email"/>
                                                    @if ($errors->has('email'))
                                                        <div class="text-danger">{{ $errors->first('email') }}</div>
                                                    @endif
                                                    <i class="ace-icon fa fa-user"></i>
                                                </span>
                                            </label>

                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="password" class="form-control" placeholder="Password" required="" name="password"/>
                                                    @if($errors->has('password'))
                                                        <div class="text-danger">{{ $errors->first('password') }}</div>
                                                    @endif
                                                    <i class="ace-icon fa fa-lock"></i>
                                                </span>
                                            </label>

                                            <div class="space"></div>

                                            <div class="clearfix">
                                                <label class="inline">
                                                    <span style="color: red;"></span>
                                                </label>

                                                <button type="submit" class="width-35 pull-right btn btn-sm btn-success" value="Login">
                                                    <i class="ace-icon fa fa-key"></i>
                                                    <span class="bigger-110">Login</span>
                                                </button>
                                            </div>

                                            <div class="space-4"></div>
                                        </fieldset>
                                    </form>

                                    <div class="social-or-login center">

                                    </div>

                                    <div class="space-6"></div>


                                </div><!-- /.widget-main -->

                                <div class="toolbar clearfix">
                                    <div>
                                        <a href="#" data-target="#forgot-box" class="forgot-password-link">
                                            <i class="ace-icon fa fa-arrow-left"></i>
                                            I forgot my password
                                        </a>
                                    </div>
                                </div>
                            </div><!-- /.widget-body -->
                        </div><!-- /.login-box -->

                        <div id="forgot-box" class="forgot-box widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header red lighter bigger">
                                        <i class="ace-icon fa fa-key"></i>
                                        Retrieve Password
                                    </h4>

                                    <div class="space-6"></div>
                                    <p>
                                        Enter your Phone and to receive instructions
                                    </p>

                                    <form action="{{ route('auth.employee_forgot_password') }}" method="POST">
                                        @csrf
                                        
                                        <fieldset>
                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="text" name="verification_number"
                                                           class="form-control no-spin" placeholder="Phone"
                                                           maxlength="13" required="" autofocus />
                                                    <i class="ace-icon fa fa-envelope"></i>
                                                </span>
                                            </label>

                                            <div class="clearfix">
                                                <input type="submit" value="Send Me!"
                                                       class="width-35 pull-right btn btn-sm btn-danger">
                                            </div>
                                        </fieldset>
                                    </form>
                                </div><!-- /.widget-main -->


                                <div class="toolbar center">
                                    <a href="#" data-target="#login-box" class="back-to-login-link">
                                        Back to login
                                        <i class="ace-icon fa fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div><!-- /.widget-body -->
                        </div><!-- /.forgot-box -->

                    </div><!-- /.position-relative -->
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.main-content -->
</div><!-- /.main-container -->

<!-- basic scripts -->

<!--[if !IE]> -->
<script src="/assets/js/jquery-2.1.4.min.js"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
<script type="text/javascript">
    if ('ontouchstart' in document.documentElement) document.write("<script src='/assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
</script>

<!-- inline scripts related to this page -->
<script type="text/javascript">
    jQuery(function ($) {
        $(document).on('click', '.toolbar a[data-target]', function (e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('.widget-box.visible').removeClass('visible');//hide others
            $(target).addClass('visible');//show target
        });
    });


    //you don't need this, just used for changing background
    jQuery(function ($) {
        $('#btn-login-dark').on('click', function (e) {
            $('body').attr('class', 'login-layout');
            $('#id-text2').attr('class', 'white');
            $('#id-company-text').attr('class', 'blue');

            e.preventDefault();
        });
        $('#btn-login-light').on('click', function (e) {
            $('body').attr('class', 'login-layout light-login');
            $('#id-text2').attr('class', 'grey');
            $('#id-company-text').attr('class', 'blue');

            e.preventDefault();
        });
        $('#btn-login-blur').on('click', function (e) {
            $('body').attr('class', 'login-layout blur-login');
            $('#id-text2').attr('class', 'white');
            $('#id-company-text').attr('class', 'light-blue');

            e.preventDefault();
        });

    });
</script>
</body>
</html>
