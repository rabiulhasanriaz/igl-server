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
	                <th>Date</th>
	                <th>Credit</th>
	                <th>Debit</th>
	                <th>Referance</th>
	            </tr>
	            </thead>

	            <tbody>

	            <!-- Low balance users -->
	            	@php($serial = 1 )
	            	@php($balance = 0)
		            @foreach($transactions as $transaction)
		            	
		        		<tr>
		        		    <td>{{ $serial++ }}</td>
		        		    <td>{{ $transaction->asb_submit_time }}</td>
		        		    <td>{{ $transaction->asb_credit }}</td> @php($balance += $transaction->asb_credit )
		        		    <td>{{ $transaction->asb_debit }}</td> @php($balance -= $transaction->asb_debit )
		        		    <td>{{ $transaction->asb_pay_ref }}</td>
		        		</tr>    	
		            
	            	@endforeach
	                
	            </tbody>
	            {{--
	            <tfoot>
	            	<tr>
	            		<th colspan="4">Balance </th>
	            		<th>{{ "" }} tk.</th>
	            	</tr>
	            </tfoot>
	            --}}
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
