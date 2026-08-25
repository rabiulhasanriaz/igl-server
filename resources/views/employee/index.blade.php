@extends('employee.master')


@section('dashboard_menu_class','active')
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
		overview &amp; stats
	</small>
</h1>
@endsection


@section('main_content')
	<div class="row">
		<div class="space-6"></div>


		<div class="vspace-12-sm"></div>

				<div class="col-sm-9">
					<div class="widget-box">

		                <div class="widget-body">
		                    <div class="widget-main">
		                        <div id="piechart-placeholder"></div>

		                        <div class="clearfix">
		                            <div class="grid4">
		                                <h3 style="color: rgba(0, 107, 16, 0.5);margin: 0;">{{ $data['users'] }}</h3>
		                                <div class="infobox-content"><h5 style="font-size: 13px;">Users</h5></div>
		                            </div>

		                            <div class="grid4">
		                                <h3 style="color: rgba(255, 0, 0, 0.5);margin: 0;">{{ $data['balance'] }}</h3>
		                                <div class="infobox-content"><h5 style="font-size: 13px;">Balance</h5></div>
		                            </div>

		                            <div class="grid4">
		                                <h3 style="color: rgba(24, 0, 255, 0.5);margin: 0;">
		                                {{ $data['debit'] }}</h3>
		                                <div class="infobox-content"><h5 style="text-transform: uppercase;font-size: 13px;">Debit</h5></div>
		                            </div>

		                            <div class="grid4">
		                                <h3 style="color: rgb(142, 170, 88);margin: 0;">{{ $data['credit'] }}</h3>
		                                <div class="infobox-content"><h5 style="text-transform: uppercase;font-size: 13px;">Credit</h5></div>
		                            </div>
		                        </div>
		                    </div><!-- /.widget-main -->
		                </div><!-- /.widget-body -->
					</div><!-- /.widget-box -->
				

					<div class="hr hr32 hr-dotted"></div>

					<div class="widget-box transparent">
					    <div class="widget-header widget-header-flat">
					        <h4 class="widget-title lighter">
					            <i class="ace-icon fa fa-star orange"></i>
					            Your History
					        </h4>

					        <div class="widget-toolbar">
					            <a href="#" data-action="collapse">
					                <i class="ace-icon fa fa-chevron-up"></i>
					            </a>
					        </div>
					    </div>

					    <div class="widget-body">
					        <div class="widget-main no-padding">
					            <table class="table table-bordered table-striped">
					                <thead class="thin-border-bottom">
					                <tr>
					                    <th>
					                        <i class="ace-icon fa fa-caret-right blue"></i>Month
					                    </th>

					                    <th>
					                        <i class="ace-icon fa fa-caret-right blue"></i>Transaction Amount
					                    </th>

					                    <th class="">
					                        <i class="ace-icon fa fa-caret-right blue"></i>Coommission
					                    </th>

					                </tr>
					                </thead>

					                <tbody>
					                
					                
					                    <tr>
					                        <td style="text-transform: uppercase;">{{ '' }}</td>

					                        <td class="hidden-480">
					                            <span class="label label-info arrowed-right arrowed-in">{{ "" }}</span>
					                        </td>

					                        <td class="hidden-480">
					                            <span class="label label-info arrowed-right arrowed-in">{{ "" }}</span>
					                        </td>
					                    </tr>
					                
					                

					                </tbody>
					            </table>
					        </div><!-- /.widget-main -->
					    </div><!-- /.widget-body -->
					</div><!-- /.widget-box -->
			</div>

			<div class="col-sm-3">
			            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dashboard-leftBalance">
			                <h3>STATISTICS</h3><hr>

			                <p>Balance BDT: {{ $data['balance'] }}
			                            <b style="font-size: 15px;">৳</b></span> 
			                </p>
			                <h3>LAST 4 TRANSACTIONS</h3>

                            <table class="table table-striped table-bordered">
			                    <thead>
			                        <tr>
			                            <th colspan="3" class="text-center">Transaction</th>
			                        </tr>
			                        <tr>
			                            <th>Date</th>
			                            <th>Debit</th>
			                            <th>Credit</th>
			                        </tr>
			                    </thead>
			                    <tbody>

			                      
			                        @foreach($data['transactions'] as $transaction)
			                            <tr>
			                                <td>{{ $transaction->created_at }}</td>
			                                <td>{{ $transaction->euc_credit }}&nbsp;৳</td>
			                                <td>{{ $transaction->euc_debit }}&nbsp;৳</td>
			                            </tr>
			                        @endforeach
			                      

			                        

			                    </tbody>
			                </table>
			            </div>

	        </div>
	</div>
@endsection
