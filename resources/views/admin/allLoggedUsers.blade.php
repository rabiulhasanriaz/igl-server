@extends('admin.master')

@section('all_logged_in_users_menu', 'active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('admin.index') }}">Dashboard</a>
	</li>
	<li>
		<a href="{{ route('admin.senderID.index') }}">Users</a>
	</li>
	<li class="active">Logged Users</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	All logged users
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		 Lists
	</small>
</h1>
@endsection

@section('main_content')

<div class="space-6"></div>


<div class="row">

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f8f8f8;"><hr>
			<h3> Current Logged Users  </h3>
			<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					 <tr>
	                    <th>SL</th>
	                    <th>Company Name</th>
	                    <th>User Name</th>
	                    <th>Customer Type</th>
	                    <th>Email</th>
	                    <th>Phone</th>
	                    <th class="hidden-480">Balance</th>
	                    <th class="hidden-480">Reseller</th>
	                    <th>Last Login Time</th>
	                    <th>Action</th>
	                </tr>
				</thead>

				<tbody>
					@foreach($logged_users as $logged_user)
					    <tr>
					        <td>{{ $loop->iteration }}</td>
					        <td>
                                @if($logged_user->login_status == 1)
                                    <i class="ace-icon fa fa-circle" style="color: #00ffa3"></i>
                                @elseif($logged_user->login_status == 2)
                                    <i class="ace-icon fa fa-circle-o" style="color: #cede7c"></i>
                                @endif
                                {{ $logged_user->company_name }}
                            </td>
					        <td>{{ $logged_user->userDetail['name'] }}</td>
					        <td><p style='color:#428BCA;'>
					                @if(($logged_user->role==1) || ($logged_user->role==2) || ($logged_user->role==3))
					                    Root User {{ $logged_user->role }}
					                @elseif($logged_user->role==4)
					                    Reseller
					                @elseif($logged_user->role==5)
					                    User
					                @endif
					            </p></td>
					        <td>{{ $logged_user->email }}</td>
					        <td class="hidden-480">{{ $logged_user->cellphone }}</td>
					        <td class="hidden-480">{{ number_format(BalanceHelper::user_available_balance($logged_user->id), 2) }} <b>৳</b></td>
					        <td class="hidden-480">{{ @$logged_user->parentInfo->company_name }}</td>
					        <td class="hidden-480">
					        	@if($logged_user->last_login_time != null)
					        		{{ $logged_user->last_login_time->format('Y-m-d h:i:s a') }}
					        	@endif
					        </td>
					        <td>
					            <div class="widget-toolbar no-border">
					                <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
					                        aria-expanded="false">
					                    Action
					                    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
					                </button>
					                <ul class="dropdown-menu dropdown-primary dropdown-menu-right dropdown-caret dropdown-close">
					                    <li>
					                        <a href="{{ route('admin.reseller.priceView', $logged_user->id ) }}">
					                            <i class="ace-icon fa fa-search-plus bigger-130"></i> Price View
					                        </a>
					                    </li>
					                    <li>
					                        <a href="{{ route('admin.reseller.transactionHistory', $logged_user->id) }}"
					                           class="tooltip-error" data-rel="tooltip" title="Account Details">
					                            <span class="label label-sm label-primary">Account</span>
					                        </a>
					                    </li>
					                    <li>
					                        @if($logged_user->status=='1')
					                            <a href="{{ route('admin.reseller.suspend', $logged_user->id) }}" class="tooltip-error" data-rel="tooltip" title="Conform">
					                                <span class="label label-sm label-warning">Suspend</span>
					                            </a>
					                        @else
					                            <a href="{{ route('admin.reseller.active', $logged_user->id) }}" class="" data-rel="tooltip" title="Conform">
					                                <span class="label label-sm label-success">Re-Active</span>
					                            </a>
					                        @endif
					                    </li>
					                    <li class="divider"></li>
					                    <li>
					                        <a class="green" href="{{ route('admin.reseller.edit', $logged_user->id) }}">
					                            <i class="ace-icon fa fa-pencil bigger-130"></i> Edit
					                        </a>
					                    </li>
					                    <li class="divider"></li>
					                    <li>
					                        <a href="{{ route('admin.reseller.goToThisAccount', $logged_user->id) }}"
					                           class="tooltip-error" data-rel="tooltip" title="Account Details">
					                            <span class="label label-sm label-primary">Go to this account</span>
					                        </a>
					                    </li>
					                </ul>
					            </div>
					        </td>
					    </tr>
					@endforeach
				</tbody>
			</table>
		</div>
		<!-- PAGE CONTENT ENDS -->
	</div><!-- /.col -->
</div><!-- /.row -->


@endsection

@section('custom_style')
	<style type="text/css">

	</style>
@endsection

@section('custom_script')
	<style type="text/css">

	</style>
@endsection


