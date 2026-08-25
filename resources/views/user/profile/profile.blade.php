@extends('user.master')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('user.index') }}">Dashboard</a>
        </li>
        <li class="active">Profile</li>
    </ul><!-- /.breadcrumb -->
@endsection

@section('page_header')
    <h1>
        Profile
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Update
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
            @include('user.partials.session_messages')
            @include('user.partials.all_error_messages')

            <form action="{{ route('user.profile') }}" method="post" enctype="multipart/form-data" id="profile_form">
                @csrf
                <div id="user-profile-1" class="user-profile row">

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <h3 class="text-center text-primary">Account Information</h3>
                            <hr>
                            <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                <h4 class="text-primary">Credit
                                    : {{ number_format(BalanceHelper::user_total_credit(Auth::id()),2) }} ৳</h4>
                            </div>
                            <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                <h4 class="text-danger">Debit
                                    : {{ number_format(BalanceHelper::user_total_debit(Auth::id()),2) }} ৳</h4>
                            </div>
                            <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                <h4 class="text-success">Balance
                                    : {{ number_format(BalanceHelper::user_available_balance(Auth::id()),2) }} ৳</h4>
                            </div>
                        </div>
                        <br>

                        <!-- OTP Toggle Row with Auto-save indicator -->
                        <div class="profile-user-info profile-user-info-striped">
                            <div class="profile-info-row">
                                <div class="profile-info-name"> OTP Verification </div>
                                <div class="profile-info-value">
                                    <label class="switch">
                                        <input type="checkbox" name="otp_check" id="otp_toggle"
                                            {{ optional(Auth::user()->userDetail)->otp_check == 1 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                    <span style="margin-left: 10px; font-weight: bold;" id="otp_status_text">
                                        {{ optional(Auth::user()->userDetail)->otp_check == 1 ? '🔴 OTP Disabled' : '🟢 OTP Required' }}
                                    </span>
                                    <span id="otp_save_status" style="margin-left: 10px; font-size: 12px;"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Message when OTP is disabled -->
                        <div id="otp_warning_message" class="alert alert-danger" style="display: {{ optional(Auth::user()->userDetail)->otp_check == 1 ? 'block' : 'none' }}; margin-top: 10px;">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <i class="ace-icon fa fa-exclamation-triangle"></i>
                            <strong>⚠️ SECURITY RISK!</strong> OTP verification is currently <strong>DISABLED</strong>. 
                            This makes your account vulnerable to unauthorized access. For your panel's security and to prevent potential financial loss, 
                            we strongly recommend enabling OTP verification.
                            <br><br>
                            <small><i class="ace-icon fa fa-shield"></i> Without OTP, anyone with your password can access your account and perform transactions.</small>
                        </div>

                        <div class="profile-user-info profile-user-info-striped">
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Name</div>

                                <div class="profile-info-value">
                                    <span class="editable" id="username">{{ Auth::user()->userDetail->name }}</span>
                                    <span class="pull-right">
                                        <input type="text" name="name" class="input-sm"
                                               id="inputsm" placeholder="Name"
                                               value="{{ Auth::user()->userDetail->name }}"
                                               style="height: 25px;" required>
                                    </span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name">Company Name</div>

                                <div class="profile-info-value">
                                    <span class="editable" id="username">{{ Auth::user()->company_name }}</span>
                                    <span class="pull-right">
                                        <input type="text" name="company_name" class="input-sm"
                                               id="inputsm" placeholder="company_name"
                                               value="{{ Auth::user()->company_name }}"
                                               style="height: 25px;" required>
                                    </span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Email</div>

                                <div class="profile-info-value">
                                    <span class="editable" id="email">{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Mobile</div>

                                <div class="profile-info-value">
                                    <span class="editable" id="mobile">{{ Auth::user()->cellphone }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name"> Location</div>
                                <div class="profile-info-value">
                                    <i class="fa fa-map-marker light-orange bigger-110"></i>
                                    {{ Auth::user()->userDetail->address }}
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Joined</div>

                                <div class="profile-info-value">
                                    <span class="editable" id="signup">{{ Auth::user()->created_at->format('Y-m-j h:i:s a') }}</span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Designation</div>

                                <div class="profile-info-value">
                                    <span class="editable" id="about">{{ Auth::user()->userDetail->designation }}</span>
                                    <span class="pull-right">
                                        <input type="text" value="{{ Auth::user()->userDetail->designation }}"
                                               name="designation" class="input-sm" id="inputsm"
                                               placeholder="Designation" style="height: 25px;" required>
                                    </span>

                                </div>

                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name">Photo</div>
                                <div class="profile-info-value">
                                    <input type="file" name="profile_image">
                                </div>
                            </div>

                        </div>
                        <div class="space-20"></div>
                        <div class="col-md-12">
                            <button class="btn btn-primary btn-sm pull-right">Update
                            </button>
                        </div>
                    </div><!-- /.col -->

                </div>
            </form>
        </div><!-- /.col -->
    </div>

    <!-- Toggle Switch CSS -->
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .switch input { 
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #5cb85c;
            transition: .4s;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
        }
        /* When checkbox is CHECKED -> OTP Disabled -> Show Red */
        input:checked + .slider {
            background-color: #d9534f;
        }
        /* When checkbox is UNCHECKED -> OTP Required -> Show Green */
        input:not(:checked) + .slider {
            background-color: #5cb85c;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .slider.round {
            border-radius: 24px;
        }
        .slider.round:before {
            border-radius: 50%;
        }
        
        @keyframes warningBlink {
            0% { border-left-color: #d9534f; }
            50% { border-left-color: #ff0000; }
            100% { border-left-color: #d9534f; }
        }
        
        #otp_warning_message {
            border-left: 5px solid #d9534f;
            animation: warningBlink 1s ease-in-out infinite;
        }
        
        .save-indicator {
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .save-success {
            color: #5cb85c;
        }
        .save-error {
            color: #d9534f;
        }
        .save-loading {
            color: #f0ad4e;
        }
    </style>

    <!-- JavaScript for Auto-save OTP -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('otp_toggle');
            const statusText = document.getElementById('otp_status_text');
            const warningMessage = document.getElementById('otp_warning_message');
            const saveStatus = document.getElementById('otp_save_status');
            
            function updateUI(isDisabled) {
                if (isDisabled) {
                    statusText.innerHTML = '🔴 OTP Disabled';
                    statusText.style.color = '#d9534f';
                    if (warningMessage) {
                        warningMessage.style.display = 'block';
                    }
                } else {
                    statusText.innerHTML = '🟢 OTP Required';
                    statusText.style.color = '#5cb85c';
                    if (warningMessage) {
                        warningMessage.style.display = 'none';
                    }
                }
            }
            
            function showSaveStatus(message, type) {
                saveStatus.innerHTML = message;
                saveStatus.className = 'save-indicator save-' + type;
                setTimeout(() => {
                    saveStatus.innerHTML = '';
                }, 3000);
            }
            
            function autoSaveOTP(otpValue) {
                // Show loading indicator
                showSaveStatus('Saving...', 'loading');
                
                // Get CSRF token
                const csrfToken = document.querySelector('input[name="_token"]').value;
                
                // Make AJAX request to your existing route
                fetch('{{ route("user.update-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        otp_check: otpValue ? 1 : 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSaveStatus('✓ Saved', 'success');
                        updateUI(otpValue);
                    } else {
                        showSaveStatus('✗ Failed: ' + (data.message || 'Unknown error'), 'error');
                        // Revert toggle on error
                        toggle.checked = !otpValue;
                        updateUI(!otpValue);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showSaveStatus('✗ Network error', 'error');
                    // Revert toggle on error
                    toggle.checked = !otpValue;
                    updateUI(!otpValue);
                });
            }
            
            function updateOTPStatus() {
                const isDisabled = toggle.checked; // Checked = Disabled
                updateUI(isDisabled);
                autoSaveOTP(isDisabled);
            }
            
            // Add change event listener with confirmation for disabling
            toggle.addEventListener('change', function(e) {
                // If trying to DISABLE OTP (checking the checkbox)
                if (this.checked) {
                    const userConfirmed = confirm(
                        '⚠️ SECURITY ALERT! ⚠️\n\n' +
                        'You are about to DISABLE OTP verification.\n\n' +
                        'This will:\n' +
                        '• Remove an extra layer of security from your account\n' +
                        '• Make your account vulnerable to unauthorized access\n' +
                        '• Put your funds and panel data at risk\n' +
                        '• Anyone with your password can access your account\n\n' +
                        'Are you absolutely sure you want to disable OTP?\n\n' +
                        'We strongly recommend keeping OTP enabled for your security.'
                    );
                    
                    if (!userConfirmed) {
                        // Revert the toggle if user cancels
                        e.preventDefault();
                        this.checked = false;
                        updateUI(false);
                        return;
                    }
                } 
                
                // Save the new state
                updateOTPStatus();
            });
            
            // Initial UI setup
            updateUI(toggle.checked);
        });
    </script>

@endsection
