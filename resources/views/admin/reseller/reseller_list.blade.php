@extends('admin.master')

@section('reseller_menu_class','open')
@section('reseller_list_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Reseller List</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Reseller
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

            @include('admin.partials.session_messages')

            <table id="reseller-list-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Company Name</th>
                    <th>User Name</th>
                    <th>Customer Type</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>

                <!-- reseller lists -->
                @php($serial=1)
                @foreach($users as $user)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>{{ $user->company_name }}</td>
                        <td>{{ $user->userDetail['name'] }}</td>
                        <td><p style='color:#428BCA;'>
                                @if(($user->role==1) || ($user->role==2) || ($user->role==3))
                                    Root User {{ $user->role }}
                                @elseif($user->role==4)
                                    Reseller
                                @elseif($user->role==5)
                                    User
                                @endif
                            </p></td>
                        <td>{{ $user->email }}</td>
                        <td class=""><a href="tel:{{ $user->cellphone }}">{{ $user->cellphone }}</a></td>
                        <td>
                            <div class="widget-toolbar no-border">
                                <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                    Action
                                    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-primary dropdown-menu-right dropdown-caret dropdown-close">
                                    <li>
                                        <a href="{{ route('admin.reseller.priceView', $user->id ) }}">
                                            <i class="ace-icon fa fa-search-plus bigger-130"></i> Price View
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.reseller.transactionHistory', $user->id) }}"
                                           class="tooltip-error" data-rel="tooltip" title="Account Details">
                                            <span class="label label-sm label-primary">Account</span>
                                        </a>
                                    </li>
                                    <li>
                                        @if($user->status=='1')
                                            <a href="{{ route('admin.reseller.suspend', $user->id) }}" class="tooltip-error" data-rel="tooltip" title="Conform">
                                                <span class="label label-sm label-warning">Suspend</span>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.reseller.active', $user->id) }}" class="" data-rel="tooltip" title="Conform">
                                                <span class="label label-sm label-success">Re-Active</span>
                                            </a>
                                        @endif
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a class="green" href="{{ route('admin.reseller.edit', $user->id) }}">
                                            <i class="ace-icon fa fa-pencil bigger-130"></i> Edit
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="{{ route('admin.reseller.goToThisAccount', $user->id) }}"
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
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#reseller-list-table').DataTable( {
            responsive: true,
            
        } );
    } );
</script>
@endsection


