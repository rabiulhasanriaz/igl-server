@extends('reseller.master')

@section('employee_menu_class','open')
@section('employee_list_menu_class','active')


@section('page_location')

<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('reseller.index') }}">Dashboard</a>
	</li>
	
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Dashboard
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Users
	</small>
</h1>
@endsection

@section('main_content')
    <div class="row">
        <div class="col-sm-12 table">
        @include('reseller.partials.session_messages')

            <table id="user-list-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Company Name</th>
                    <th>User name</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                    @php($serial=1)
                    @foreach($users as $user)
                        <tr>
                            <td>{{ ++$serial }}</td>
                            <td>{{ $user->company_name }}</td>
                            <td>{{ $user->userDetail['name'] }}</td>
                            <td>{{ $user->cellphone }}</td>
                            <td>{{ $user->email }}</td>
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