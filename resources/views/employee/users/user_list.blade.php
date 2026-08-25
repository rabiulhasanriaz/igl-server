@extends('employee.master')

@section('employee_users', 'open')
@section('employee_users_lists', 'active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('employee.index') }}">Dashboard</a>
        </li>
        <li class="active">Users List</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Users
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            List
        </small>
    </h1>
@endsection

@section('main_content')
	<div class="space-6"></div>


	<div class="row">
	    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

	        <table id="user-list-table" class="table table-striped table-bordered table-hover">
	            <thead>
	            <tr>
	                <th>SL</th>
	                <th>Company Name</th>
	                <th>User Name</th>
	                <th>Customer Type</th>
	                <th>Email</th>
	                <th>Phone</th>
	                
	            </tr>
	            </thead>

	            <tbody>

	            <!-- Users lists -->
	            	@php($serial = 1)
		            @foreach($data['allUsers'] as $user)
		            	
		        		<tr>
		        		    <td>{{ $serial++ }}</td>
		        		    <td>{{ $user->company_name }}</td>
		        		    <td>{{ $user->userDetail['name'] }}</td>
		        		    <td><p style='color:#428BCA;'>
		        		    		@if($user->rolee == 4)
		        		    			{{ 'reseller' }}        
		        		    		@else
		        		    			{{ 'user' }}
		        		    		@endif
		        		        </p>
		        		    </td>
		        		    <td>{{ $user->email }}</td>
		        		    <td class="">{{ $user->cellphone }}</td>
		        		    
		        		    	
		            @endforeach
	            
	                
	            </tbody>
	        </table>


	    </div><!-- /.col -->
	</div><!-- /.row -->
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#user-list-table').DataTable();
    </script>

@endsection
