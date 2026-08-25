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
                        <td>{{ $user->userDetail['company_name'] }}</td>
                        <td>{{ $user->name }}</td>
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
                        <td class="hidden-480">{{ $user->cellphone }}</td>
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
                                           class="tooltip-error" data-rel="tooltip" title="Account Details" onclick="return confirm('Are you sure to log in this account. you will be logged out')">
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


@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#reseller-list-table').DataTable();
    </script>

@endsection
