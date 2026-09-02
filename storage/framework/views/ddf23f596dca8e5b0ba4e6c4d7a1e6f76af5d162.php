<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="utf-8"/>
    <title>OTP Verification</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/fonts.googleapis.com.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ace.min.css')); ?>"/>

    <style>
        .otp-text {
            font-size: 14px;
            color: #666;
            text-align: center;
        }
        .countdown {
            color: red;
            font-weight: bold;
            text-align: center;
            font-size: 18px;
        }
        .resend-btn {
            margin-top: 10px;
        }
        .text-danger {
            color: red;
        }
        .text-success {
            color: green;
        }
        .hidden {
            display: none;
        }
    </style>
</head>

<body class="login-layout">
<div>
    <img src="<?php echo e(asset('assets/uploads/login_bg.png')); ?>" 
         style="z-index: -999; position: absolute; width: 100%; height: 100%;">
</div>

<div class="main-container">
    <div class="main-content">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="login-container">

                    <div class="space-6"></div>
                    <div class="space-16"></div>

                    <div class="position-relative">

                        <div class="login-box visible widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">

                                    <h4 class="header blue lighter bigger text-center">
                                        <i class="ace-icon fa fa-lock"></i>
                                        OTP Verification
                                    </h4>

                                    <div class="space-6"></div>

                                    
                                    <div id="message-area">
                                        <?php if(session('message')): ?>
                                            <p class="text-danger text-center">
                                                <?php echo e(session('message')); ?>

                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <p class="otp-text">
                                        Please enter the OTP sent to your phone
                                    </p>

                                    <div class="space-6"></div>

                                    
                                    <form id="otpForm" action="<?php echo e(route('auth.otp.check')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>

                                        <fieldset>

                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="text" name="otp" id="otp"
                                                           class="form-control"
                                                           placeholder="Enter 6 digit OTP"
                                                           maxlength="6"
                                                           required autofocus />
                                                    <i class="ace-icon fa fa-key"></i>
                                                </span>
                                            </label>

                                            <div class="space"></div>

                                            <div class="clearfix">
                                                <button type="submit" id="verifyBtn"
                                                        class="width-100 btn btn-sm btn-success">
                                                    <i class="ace-icon fa fa-check"></i>
                                                    Verify OTP
                                                </button>
                                            </div>

                                        </fieldset>
                                    </form>

                                    <div class="space-6"></div>

                                    
                                    <p class="countdown">
                                        OTP expires in: <span id="timer">--:--</span>
                                    </p>

                                    
                                    <div id="resendContainer" class="resend-btn text-center hidden">
                                        <button id="resendBtn" class="btn btn-sm btn-info" onclick="resendOtp()">
                                            <i class="ace-icon fa fa-refresh"></i>
                                            Resend OTP
                                        </button>
                                    </div>

                                </div><!-- /.widget-main -->

                               <div class="toolbar center">
                                    <a href="<?php echo e(route('auth.login')); ?>" class="back-to-login-link" style="color: #000000;">
                                        <i class="ace-icon fa fa-arrow-left"></i>
                                        Back to Login
                                    </a>
                                </div>

                            </div><!-- /.widget-body -->
                        </div><!-- /.login-box -->

                    </div><!-- /.position-relative -->
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo e(asset('assets/js/jquery-2.1.4.min.js')); ?>"></script>

<script>
    // Get expire time from session (passed from server)
    let expireTime = '<?php echo e(session('otp_expire')); ?>';
    let countdownInterval;

    // Function to calculate remaining time
    function getRemainingSeconds() {
        if (!expireTime) return 0;
        let now = new Date().getTime();
        let expire = new Date(expireTime).getTime();
        let remaining = Math.floor((expire - now) / 1000);
        return remaining > 0 ? remaining : 0;
    }

    // Function to update timer display
    function updateTimer() {
        let remainingSeconds = getRemainingSeconds();
        
        if (remainingSeconds <= 0) {
            // OTP Expired
            document.getElementById('timer').innerHTML = "Expired";
            document.getElementById('timer').style.color = "red";
            document.getElementById('verifyBtn').disabled = true;
            document.getElementById('otp').disabled = true;
            clearInterval(countdownInterval);
            
            // Show expired message
            $('#message-area').html('<p class="text-danger text-center">OTP has expired. Please resend to get a new OTP.</p>');
            
            // Show resend button after expiry
            $('#resendContainer').removeClass('hidden');
            
        } else {
            let minutes = Math.floor(remainingSeconds / 60);
            let seconds = remainingSeconds % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            document.getElementById('timer').innerHTML = minutes + ":" + seconds;
            document.getElementById('timer').style.color = "red";
            
            // Hide resend button while countdown is active
            $('#resendContainer').addClass('hidden');
        }
    }

    // Start countdown from expire time (not on reload)
    function startCountdown() {
        updateTimer(); // Initial update
        countdownInterval = setInterval(updateTimer, 1000);
    }

    // Resend OTP function
    function resendOtp() {
        // Disable resend button
        $('#resendBtn').prop('disabled', true);
        $('#resendBtn').html('<i class="ace-icon fa fa-spinner fa-spin"></i> Sending...');
        
        // AJAX request to resend OTP
        $.ajax({
            url: '<?php echo e(route("auth.otp.resend")); ?>',
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Update new expire time
                    expireTime = response.otp_expire;
                    
                    // Reset timer
                    if (countdownInterval) {
                        clearInterval(countdownInterval);
                    }
                    startCountdown();
                    
                    // Re-enable verify button and OTP input
                    document.getElementById('verifyBtn').disabled = false;
                    document.getElementById('otp').disabled = false;
                    document.getElementById('otp').value = '';
                    
                    // Hide resend button again (will show after new countdown expires)
                    $('#resendContainer').addClass('hidden');
                    
                    // Clear any previous messages
                    $('#message-area').html('<p class="text-success text-center">' + response.message + '</p>');
                    
                    // Reset resend button
                    $('#resendBtn').prop('disabled', false);
                    $('#resendBtn').html('<i class="ace-icon fa fa-refresh"></i> Resend OTP');
                    
                } else {
                    $('#message-area').html('<p class="text-danger text-center">' + response.message + '</p>');
                    $('#resendBtn').prop('disabled', false);
                    $('#resendBtn').html('<i class="ace-icon fa fa-refresh"></i> Resend OTP');
                }
            },
            error: function() {
                $('#message-area').html('<p class="text-danger text-center">Failed to resend OTP. Please try again.</p>');
                $('#resendBtn').prop('disabled', false);
                $('#resendBtn').html('<i class="ace-icon fa fa-refresh"></i> Resend OTP');
            }
        });
    }

    // Start countdown when page loads
    if (getRemainingSeconds() > 0) {
        startCountdown();
    } else {
        document.getElementById('timer').innerHTML = "Expired";
        document.getElementById('verifyBtn').disabled = true;
        document.getElementById('otp').disabled = true;
        // Show resend button immediately if already expired
        $('#resendContainer').removeClass('hidden');
    }
</script>

</body>
</html>
