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
		Employee list
	</small>
</h1>
@endsection

@section('main_content')
    <div class="row">
        <div class="col-sm-12 table">
        @include('reseller.partials.session_messages')
        
            <table id="employee-list-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Employee name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Number of Users</th>
                    <th>Commission</th>
                    <th>Payable Balance</th>
                    <th>System</th>
                </tr>
                </thead>
                <tbody>
                @php($serial=1)
                @foreach($data['allEmployee'] as $employee)
                    <tr>
                        <td>{{ Auth::id() }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td class=""> {{ $employee->phone }}</td>
                        <td class="text-center"><a href="{{ route('reseller.employee.employee_users_list', $employee->id) }}" class="badge badge-success"> {{ OtherHelpers::get_number_of_user($employee->id) }} </a></td>
                        <td>{{ $employee->commission." %" }}</td>

                        <td>{{ BalanceHelper::getEmployeeBalance($employee->id) }}</td>
                        
                        <td>
                            <div class="widget-toolbar no-border">

                                <button class="btn btn-xs bigger btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                    Active
                                    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-primary dropdown-menu-right dropdown-caret dropdown-close">
                                    
                                    
                                    <li class="divider"></li>
                                    <li>
                                        <a class="green" href="{{ route('reseller.employee.edit', $employee->id) }}"
                                           class="tooltip-error" data-rel="tooltip" title="Customer Edit">
                                            <span class="label label-sm btn-success"
                                                  style="padding: 3px;">Edit This Employee</span>
                                        </a>
                                        
                                        <a class="" href="{{ route('reseller.employee.employee_users_list', $employee->id) }}"
                                           class="tooltip-error" data-rel="tooltip" title="Customer Edit">
                                            <span class="label label-sm btn-warning "
                                                  style="padding: 3px;" >See Users of this Emplyee</span>
                                        </a>
                                    </li>
                                    
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection
@section('custom_style')
    <link href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/datatable/rowReorder.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        @media(max-width:575px){
            .abcd{
                width: 130px;
            }
        }
        
        </style>
@endsection
@section('custom_script')
    {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
    $('#employee-list-table').DataTable();
    </script> --}}
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#employee-list-table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 3 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 4 },
                    { responsivePriority: 6, targets: 5 },
                    { responsivePriority: 7, targets: 6 },
            ]
        } );
    } );
</script>
@endsection