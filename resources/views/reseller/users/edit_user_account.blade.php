        @php($permission = explode(',',$user->permission))
		@if (in_array(1,$permission))
			@php($sms_permission = true)
		@else
			@php($sms_permission = false)	
		@endif
		@if (in_array(2,$permission))
			@php($flexi_permission = true)
		@else
			@php($flexi_permission = false)	
		@endif
        @if (in_array(3,$permission))
            @php($dynamic_permission = true)
        @else
            @php($dynamic_permission = false) 
        @endif
@extends('reseller.master')

@section('user_list_menu_class','active')
@section('user_menu_class','open')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('reseller.index') }}">Dashboard</a>
        </li>
        <li class="active">User</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        User
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Edit
             <i class="ace-icon fa fa-angle-double-right"></i>
            {{ $user->company_name }}
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            @include('reseller.partials.all_error_messages')
            @include('reseller.partials.session_messages')

            <form action="{{ route('reseller.user.update', $user->id) }}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-company-1"> Company name : </label>
                    <div class="col-sm-9">
                        <input type="text" id="companyName" placeholder="Company name" name="company_name"
                               class="col-xs-10 col-sm-5" required="" value="{{ $user->company_name }}"/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger" id="companyShow"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Name : </label>
                    <div class="col-sm-9">
                        <input type="text" id="UserName" placeholder="Name" name="user_name"
                               class="col-xs-10 col-sm-5" required="" value="{{ $user->userDetail['name'] }}"/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger" id="resellerName_Show"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-email-1"> Email : </label>
                    <div class="col-sm-9">
                        <input type="email" id="EmaileNumber" placeholder="Email" name="email"
                               class="col-xs-10 col-sm-5" required="" value="{{ $user->email }}"/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger" id="Emailestate"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-phone-1"> Phone : </label>
                    <div class="col-sm-9">
                        <input type="text" id="mobileNumber" placeholder="Phone" name="phone"
                               class="col-xs-10 col-sm-5 input-mask-phone" value="{{ $user->cellphone }}" required=""/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger" id="status"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-pass-2"> Password : </label>

                    <div class="col-sm-9">
                        <input type="password" id="form-pass-2" placeholder="Password" name="password"
                               class="col-xs-10 col-sm-5" value="" />
                        <span class="help-inline col-xs-12 col-sm-7">
						
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-designation-1"> Designation
                        : </label>
                    <div class="col-sm-9">
                        <input type="text" id="form-designation-1" placeholder="Designation" class="col-xs-10 col-sm-5"
                               name="designation" value="{{ $user->userDetail->designation }}" required=""/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-address-1"> Address : </label>

                    <div class="col-sm-9">
                        <input type="text" id="form-address-1" placeholder="Address" class="col-xs-10 col-sm-5"
                               name="address" value="{{ $user->userDetail->address }}" required=""/>
                        <span class="help-inline col-xs-12 col-sm-7">
						<span class="middle text-danger"> ** </span>
					</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-designation-1"> Access type
                        : </label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="ace" name="status" onchange="show_terget(this.value)"
                                       value="Reseller" {{ ($user->role==4)?'checked':'' }} required="">
                                <span class="lbl"> Reseller </span>
                            </label>
                            <label>
                                <input type="radio" class="ace" name="status" onchange="show_terget(this.value)"
                                       value="User" {{ ($user->role==5)?'checked':'' }} required="">
                                <span class="lbl"> User </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="permission" style="{{ ($user->role==5)?'':'display: none;'  }}">
                    <label class="col-sm-3 control-label no-padding-right" for=""> Permission </label>
                    <div class="col-sm-9">
                        <input type="checkbox" name="permission[]" id="" value="1" {{ ($sms_permission)? 'checked' : '' }}> SMS
                        <input type="checkbox" name="permission[]" id="" value="2" {{ ($flexi_permission)? 'checked' : '' }}> Flexiload
                        
                        @if(Auth::user()->create_by == 1)
                        <input type="checkbox" name="permission[]" id="" value="3" {{ ($dynamic_permission)? 'checked' : '' }}> Dynamic
                        @endif
                        
                        <input type="checkbox" name="" id="checkAll" value=""> All
                    </div>
                </div>
                <div class="form-group" id="user_logo" style="{{  ($user->role==4)?'':'display: none;' }}">
                    <label class="col-sm-3 control-label no-padding-right" for="form-image-1"> Logo : </label>
                    <div class="col-sm-9">
                        <input type="file" name="image" id="form-image-1">
                        <span><img src="{{ OtherHelpers::user_logo($user->userDetail->logo) }}" style="height: 60px;"></span>
                    </div>
                </div>
                <div class="clearfix form-group">
                    <div class="col-md-offset-3 col-md-9">
                        <input type="submit" class="btn btn-primary" value="Update">
                    </div>
                </div>
            </form>


        </div><!-- /.col -->
    </div><!-- /.row -->


@endsection



@section('custom_script')
    <script type="text/javascript">
        function show_terget(value) {
            if (value == 'User') {
                $('#user_logo').hide();
                $('#permission').show();           
            }
            else if (value == 'Reseller') {
                $('#user_logo').show();
                $('#permission').hide();           
            }
        }
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    </script>

    @include('admin.ajax.check_existence')
@endsection

