@extends('employee.master')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('employee.index') }}">Dashboard</a>
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
            @include('employee.partials.session_messages')
            @include('employee.partials.all_error_messages')

            <form action="{{ route('employee.profileUpdate') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div id="user-profile-1" class="user-profile row">

                    
                    

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <h3 class="text-center text-primary">Account Information</h3>
                            <hr>
                            <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                <h4 class="text-primary">Credit
                                    : {{ number_format($total_credit, 2) }} ৳</h4>
                            </div>
                            <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                <h4 class="text-danger">Debit
                                    : {{ number_format($total_debit, 2) }} ৳</h4>
                            </div>
                            <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                <h4 class="text-success">Balance
                                    : {{ number_format($total_balance, 2) }} ৳</h4>
                            </div>
                        </div>
                        <br>


                        <div class="profile-user-info profile-user-info-striped row">

                            <div class="col-sm-3 ">
                                @php($logo = 'assets/uploads/User_Logo/' . Auth::guard('employee')->user()->avatar )
                                @if( file_exists( $logo ) )
                                    <img class="nav-user-photo" style="width: 200px" src="{{ asset('assets') }}/uploads/User_Logo/{{ Auth::guard('employee')->user()->avatar }}" alt="{{ Auth::guard('employee')->user()->name }}" />
                                @else
                                    <img class="nav-user-photo" style="width: 200px" src="{{ asset('assets') }}/images/avatars/avatar5.png" alt="{{ Auth::guard('employee')->user()->name }}" />
                                @endif
                            </div>
                            

                            <div class="col-sm-9">
                                
                            
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Name</div>

                                    <div class="profile-info-value">
                                        <span class="editable" id="username">{{ Auth::guard('employee')->user()->name }}</span>
                                        <span class="pull-right">
                                            
                                            <input type="text" name="name" class="input-sm"
                                                   id="inputsm" placeholder="Name"
                                                   value="{{ Auth::guard('employee')->user()->name }}"
                                                   style="height: 25px;" required>
                                            
                                        </span>
                                    </div>
                                </div>
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Email</div>

                                    <div class="profile-info-value">
                                        <span class="editable" id="email">{{ Auth::guard('employee')->user()->email }}</span>
                                    </div>
                                </div>
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Mobile</div>

                                    <div class="profile-info-value">
                                        <span class="editable" id="mobile">{{ Auth::guard('employee')->user()->phone }}</span>
                                    </div>
                                </div>
                                {{--
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Location</div>
                                    <div class="profile-info-value">
                                        <i class="fa fa-map-marker light-orange bigger-110"></i>
                                        {{ "Address" }}
                                    </div>
                                </div>
                                --}}
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Joined</div>

                                    <div class="profile-info-value">
                                        <span class="editable" id="signup">{{ Auth::guard('employee')->user()->created_at->format('Y-m-j h:i:s a') }}</span>
                                    </div>
                                </div>
                                {{-- comment
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Designation</div>

                                    <div class="profile-info-value">
                                        <span class="editable" id="about">{{ "cv" }}</span>
                                        <span class="pull-right">
                                            <input type="text" value="{{ Auth::user()->userDetail->designation }}"
                                                   name="designation" class="input-sm" id="inputsm"
                                                   placeholder="Designation" style="height: 25px;" required>
                                        </span>

                                    </div>

                                </div>
                                --}}
                                <div class="profile-info-row">
                                    <div class="profile-info-name">Photo</div>
                                    <div class="profile-info-value">
                                        <input type="file" name="profile_image">
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

                </div>
            </form>
        </div><!-- /.col -->
    </div>

@endsection

