@extends('employee.master')


@section('account_menu_class','open active')
@section('employee_transaction_history','active')
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
	                <th>Transaction Amount</th>
	                <th>Transaction Type</th>
	                <th>Commission</th>
	                <th>Date</th>
	                <th>Action</th>
	            </tr>
	            </thead>

	            <tbody>

	            <!-- Transaction History -->
	            	@php($serial = 1)
		            
		            	
		        		<tr>
		        		    <td>{{ $serial++ }}</td>
		        		    <td>{{ "" }}</td>
		        		    <td>{{ "" }}</td>
		        		    <td><p style='color:#428BCA;'>
		        		    		
		        		        </p>
		        		    </td>
		        		    <td></td>
		        		    <td class="hidden-480"></td>
		        		    <td>
		        		        <div class="widget-toolbar no-border">
		        		            <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
		        		                    aria-expanded="false">
		        		                Action
		        		                <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
		        		            </button>
		        		            <ul class="dropdown-menu dropdown-primary dropdown-menu-right dropdown-caret dropdown-close">
		        		                <li>
		        		                    <a href="">
		        		                        <i class="ace-icon fa fa-search-plus bigger-130"></i> Price View
		        		                    </a>
		        		                </li>
		        		                
		        		            </ul>
		        		        </div>
		        		    </td>
		        		</tr>    	
		            
	            
	                
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
