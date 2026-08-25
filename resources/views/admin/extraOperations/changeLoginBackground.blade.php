@extends('admin.master')

@section('extra_operation_menu_class','open')
@section('change_background_menu_class','active')

@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('admin.index') }}">Dashboard</a>
	</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Dashboard
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Change Login Background Image
	</small>
</h1>
@endsection

@section('main_content')
	
	<div class="row">
		<div class="col-md-4">
			@include('admin.partials.session_messages')
			<form action="{{ route('admin.changeBackgroundPost') }}" method="POST" enctype="multipart/form-data">
				@csrf
				
				<div class="form-group">
					<label for="loginBg">Select an Image (Must be in .jpg format)</label>
					<input type="file" id="loginBg" name="bg_image" class="form-control">
				</div>

				<div class="form-group">
				    <input type="submit" class="form-control btn btn-succes btn-sm" value="Change Background">
			  </div>


			</form>	
		</div>

		<div class="col-md-8">
			<img src="/assets/uploads/login_bg.png" width="100%">
		</div>
	</div>
	

@endsection