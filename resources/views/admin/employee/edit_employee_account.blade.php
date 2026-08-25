@extends('admin.master')

@section('reseller_menu_class','open')
@section('reseller_list_menu_class', 'active')
@section('page_location')
	<ul class="breadcrumb">
		<li>
			<i class="ace-icon fa fa-home home-icon"></i>
			<a href="{{ route('admin.index') }}">Dashboard</a>
		</li>
		<li class="active">Reseller</li>
	</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
	<h1>
		Reseller
		<small>
			<i class="ace-icon fa fa-angle-double-right"></i>
			Edit
		</small>
	</h1>
@endsection

@section('main_content')

	<div class="space-6"></div>

	@include('admin.partials.session_messages')

	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<form action="{{ route('admin.reseller.update', $userInfo->id) }}" method="post" class="form-horizontal" role="form"
				  enctype="multipart/form-data">

				@csrf

				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-company-1"> Company name : </label>

					<div class="col-sm-9">
						<input type="text" id="companyName" placeholder="Company name" name="company_name"
							   class="col-xs-10 col-sm-5" required="" value="{{ $userInfo->userDetail->company_name }}"/>
						<span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle text-danger" id="companyShow"> ** </span>

							@if ($errors->has('company_name'))
								<span class="text-danger">{{ $errors->first('company_name') }}</span>
							@endif
                        </span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Name : </label>

					<div class="col-sm-9">
						<input type="text" id="ResellerName" placeholder="Name" name="reseller_name"
							   class="col-xs-10 col-sm-5" required="" value="{{ $userInfo->name }}"/>
						<span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle text-danger" id="resellerName_Show"> ** </span>
							@if ($errors->has('reseller_name'))
								<span class="text-danger">{{ $errors->first('reseller_name') }}</span>
							@endif
                        </span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-email-1"> Email : </label>


					<div class="col-sm-9">
						<input type="email" id="EmaileNumber" placeholder="Email" name="email"
							   class="col-xs-10 col-sm-5" required="" onkeyup="checkEmailExistenceForUpdate(this.value, '{{$userInfo->id}}')" value="{{ $userInfo->email }}"/>
						<span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle text-danger" id="Emailestate"> ** </span>
                            <span class="invalid-email text-danger"></span>
                            <span class="valid-email text-success"></span>
							@if ($errors->has('email'))
								<span class="text-danger retErrEmail">{{ $errors->first('email') }}</span>
							@endif
                        </span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-phone-1"> Phone : </label>

					<div class="col-sm-9">
						<input type="text" id="mobileNumber" placeholder="Phone" name="phone"
							   class="col-xs-10 col-sm-5 input-mask-phone" onkeyup="checkPhoneExistenceForUpdate(this.value, '{{$userInfo->id}}', event)" value="{{ $userInfo->cellphone }}" data-mask="___________" required=""/>
						<span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle text-danger" id="status"> ** </span>
                            <span class="invalid-phone text-danger"></span>
                            <span class="valid-phone text-success"></span>
							@if ($errors->has('phone'))
								<span class="text-danger retErrPhone">{{ $errors->first('phone') }}</span>
							@endif
                        </span>
					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-pass-2"> Password : </label>

					<div class="col-sm-9">
						<input type="password" id="form-pass-2" placeholder="Password" name="password"
							   class="col-xs-10 col-sm-5" value="" required=""/>
						<span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle text-danger"> ** </span>
							@if ($errors->has('password'))
								<span class="text-danger">{{ $errors->first('password') }}</span>
							@endif
                        </span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-designation-1"> Designation
						: </label>

					<div class="col-sm-9">
						<input type="text" id="form-designation-1" placeholder="Designation" class="col-xs-10 col-sm-5"
							   name="designation" value="{{ $userInfo->userDetail->designation }}" />
						<span class="help-inline col-xs-12 col-sm-7">
                            @if ($errors->has('designation'))
								<span class="text-danger">{{ $errors->first('designation') }}</span>
							@endif
                        </span>
					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-address-1"> Address : </label>

					<div class="col-sm-9">
						<input type="text" id="form-address-1" placeholder="Address" class="col-xs-10 col-sm-5"
							   name="address" value="{{ $userInfo->userDetail->address }}"/>
						<span class="help-inline col-xs-12 col-sm-7">
                            @if ($errors->has('address'))
								<span class="text-danger">{{ $errors->first('address') }}</span>
							@endif
                        </span>
					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-image-1"> Logo : </label>

					<div class="col-sm-9">
						<input type="file" name="image" id="form-image-1">
					</div>
					<img src="{{ OtherHelpers::user_logo($userInfo->userDetail->logo) }}" alt="" style="max-width:60px;">
				</div>

				<div class="clearfix form-group">
					<div class="col-md-offset-3 col-md-9">

						<input type="submit" class="btn btn-info" value="Update">

						&nbsp; &nbsp; &nbsp;
						<button class="btn btn-danger" type="reset">
							<i class="ace-icon fa fa-undo bigger-110"></i>
							Reset
						</button>

					</div>
				</div>
			</form>


		</div><!-- /.col -->
	</div><!-- /.row -->


@endsection

@section('custom_script')
	<script src="{{ asset('assets') }}/js/data-mask.js" type="text/javascript"></script>
	@include('admin.ajax.check_existence')
@endsection