@extends('reseller.master')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('reseller.index') }}">Dashboard</a>
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
            @include('reseller.partials.all_error_messages')
            @include('reseller.partials.session_messages')


            <form action="{{ route('reseller.profile') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div id="user-profile-1" class="user-profile row">
                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 center">
                        <div>
						<span class="profile-picture">
							<img id="avatar" class="editable img-responsive" alt="Alex's Avatar"
                                 src="{{ OtherHelpers::user_logo(Auth::user()->userDetail->logo)  }}" style="width: 100%;"/>
                                 
						</span>
                            <div class="space-4"></div>
                        </div>

                        <div class="profile-contact-info">
                            <div class="profile-contact-links align-left">
                                <a href="#" class="btn btn-link">
                                    <i class="ace-icon fa fa-plus-circle bigger-120 "></i>
                                    http:/facebook.com/
                                </a>
                                <span>
                                    <input type="text" style="height: 20px; margin-left: 10px;" name="facebookId"
                                           class="input-sm" id="inputsm"
                                           value="{{ Auth::user()->userDetail->facebookid }}"
                                           placeholder="facebook Id">
                                </span>
                                <a href="#" class="btn btn-link">
                                    <i class="ace-icon fa fa-globe bigger-125 blue"></i>
                                    Your Domain
                                </a>
                                <span>
                                    <input type="text" style="height: 20px; margin-left: 10px;" name="website"
                                           class="input-sm" id="inputsm"
                                           value="{{ Auth::user()->userDetail->domain_name }}" placeholder="abc.com">
                                </span>

                                <a href="#" class="btn btn-link">
                                    <i class="ace-icon fa fa-mobile bigger-150"></i>
                                    Hotline <span style="color: red;">*</span>
                                </a>
                                <span>
                                    <input type="text" style="height: 20px; margin-left: 10px;" name="hotline"
                                           class="input-sm" id="inputsm" value="{{ Auth::user()->userDetail->hotline }}"
                                           placeholder="+8801800000000" required>
                                </span>

                                <a href="#" class="btn btn-link">
                                    <i class="ace-icon fa fa-mobile bigger-150"></i>
                                    Logout URL
                                </a>
                                <span>
                                    <input type="text" style="height: 20px; margin-left: 10px;" name="logout_url"
                                           class="input-sm" id="inputsm"
                                           value="{{ (Auth::user()->userDetail->logout_url!=null)? Auth::user()->userDetail->logout_url:'http://' }}"
                                           placeholder="http://www.example.com">
                                </span>

                            </div>
                            <div class="space-6"></div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <h3 class="text-center text-primary">Account Information</h3>
                            <hr>
                            <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                <h4 class="text-primary">Credit
                                    : {{ number_format(BalanceHelper::user_total_credit(Auth::user()->id), 2) }} ৳</h4>
                            </div>
                            <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                <h4 class="text-danger">Debit
                                    : {{ number_format(BalanceHelper::user_total_debit(Auth::user()->id), 2) }} ৳</h4>
                            </div>
                            <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                <h4 class="text-success">Balance
                                    : {{ number_format(BalanceHelper::user_available_balance(Auth::user()->id), 2) }}
                                    ৳</h4>
                            </div>
                        </div>
                        <br>


                        <div class="profile-user-info profile-user-info-striped">
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Name</div>

                                <div class="profile-info-value">
                                    <span class="editable" id="username">{{ Auth::user()->userDetail->name }}</span>
                                    <span class="pull-right">
                                        <input type="text" name="name" class="input-sm"
                                               id="inputsm" placeholder="Name"
                                               value="{{ Auth::user()->userDetail->name }}"
                                               style="height: 25px;">
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
                                    <span class="editable"
                                          id="signup">{{ Auth::user()->created_at->format('Y-m-j h:i:s a') }}</span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> Designation</div>

                                <div class="profile-info-value">
                                    <span class="editable" id="about">{{ Auth::user()->userDetail->designation }}</span>
                                    <span class="pull-right">
                                        <input type="text"
                                               value="{{ Auth::user()->userDetail->designation }}"
                                               name="designation" class="input-sm" id="inputsm"
                                               placeholder="Designation" style="height: 25px;">  </span>

                                </div>

                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name">Photo</div>

                                <div class="profile-info-value">
                                    <input type="file" name="profile_image">
                                    <div class="image" style="float: left;">
                                        <img src="{{ OtherHelpers::user_logo(Auth::user()->userDetail->logo) }}"
                                             alt="{{ Auth::user()->name }}" style="max-width: 60px;">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="space-20"></div>
                        <div class="col-md-12">
                            <button class="btn btn-primary btn-sm pull-right">Update
                            </button>
                        </div>
                    </div><!-- /.col -->

                    <div class="col-md-12" style="margin-top: 20px;">
                        <div class="alert alert-info text-center">
                            <h4>For White URL: 27.147.172.18 </br>Like: sms.YourDomain</h4>
                        </div>
                    </div>
                </div>
            </form>
        </div><!-- /.col -->
    </div>

@endsection

