@extends('admin.master')

@section('reseller_menu_class','open')
@section('reseller_list_menu_class', 'active')
@section('page_location')
	<ul class="breadcrumb">
		<li>
			<i class="ace-icon fa fa-home home-icon"></i>
			<a href="{{ route('admin.index') }}">Dashboard</a>
		</li>
		<li class="active">System Configuration</li>
	</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
	<h1>
		System Configuration
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
			<form action="{{ route('admin.update-system-configuration') }}" method="post" class="form-horizontal" role="form"
				  enctype="multipart/form-data">

				@csrf


				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-address-1"> Campaign Permission : </label>

					<div class="col-sm-9">
                        <div class="col-xs-10 col-sm-5">
                            <input type="radio" name="campaign_permission" value="1" id="campaign_permission_yes" {{ empty($configuration)? '':(($configuration->campaign_permission == 1)? 'checked':'') }} required> <label for="campaign_permission_yes">Yes</label>
                            <input type="radio" name="campaign_permission" value="0" id="campaign_permission_no" {{ empty($configuration)? '':(($configuration->campaign_permission == 0)? 'checked':'') }} required> <label for="campaign_permission_no">No</label>
                        </div>

						<span class="help-inline col-xs-12 col-sm-7">
                            @if ($errors->has('campaign_permission'))
								<span class="text-danger">{{ $errors->first('campaign_permission') }}</span>
							@endif
                        </span>
					</div>
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
