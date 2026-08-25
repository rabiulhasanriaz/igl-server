@extends('admin.master')

@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('admin.index') }}">Dashboard</a>
	</li>
	<li class="active">Change Password</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('main_content')

<div class="space-6"></div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
		@include('admin.partials.all_error_messages')
		@include('admin.partials.session_messages')
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 col-sm-offset-4">
		 	<h3 class="text-center text-primary"> Change password </h3>

		 	<form action="{{ route('admin.change-password') }}" method="post">
				@csrf

		 		<div class="form-group">
		 			<label for="old">Old password :</label>
		 			<input type="password" name="old_password" class="form-control" id="old" placeholder="Old Password" required="">
		 		</div>

		 		<div class="form-group">
		 			<label for="new">New password :</label>
		 			<input type="password" name="new_password" class="form-control" id="new" placeholder="New Password" required="">
		 		</div>

		 		<div class="form-group">
		 			<label for="re">Re-password :</label>
		 			<input type="password" name="re_password" class="form-control" id="re" placeholder="Re-Password">
		 		</div>
		 		
		 		<div class="form-group">
		 			<input type="submit" class="btn btn-sm btn-primary" value="Change password">
		 		</div>
		 	</form>
		</div>
	</div><!-- /.col -->
</div>

@endsection

