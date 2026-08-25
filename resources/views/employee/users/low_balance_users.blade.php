@extends('employee.master')


@section('employee_users','open active')
@section('employee_low_balance_users_lists','active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('employee.index') }}">Dashboard</a>
	</li>
	
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Dashboard
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Transaction History
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
	                <th>User Name</th>
	                <th>Company</th>
	                <th>Contact</th>
	                <th>Last Transaction Date</th>
	                <th>Balance</th>
	            </tr>
	            </thead>

	            <tbody>

	            <!-- Low balance users -->
	            	@php($serial = 1)
		            @foreach($low_users as $l_user)
		            	
		        		<tr>
		        		    <td>{{ $serial++ }}</td>
		        		    <td>{{ $l_user->userDetail->name }}</td>
		        		    <td>{{ $l_user->company_name }}</td>
		        		    <td>{{ $l_user->cellphone }}</td>
		        		    <td class=""> {{ \BalanceHelper::last_transaction_date($l_user->id) ? \BalanceHelper::last_transaction_date($l_user->id) : 'N / A' }} </td>
		        		    <td><p style='color:red; font-weight: bold; text-align: right;'>
		        		    		{{ number_format( \BalanceHelper::user_available_balance($l_user->id) , 2) }}
		        		        </p>
		        		    </td>
		        		    
		        		</tr>    	
		            
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
