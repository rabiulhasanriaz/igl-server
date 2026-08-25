@extends('admin.master')

@section('reseller_ac_limit_menu_class','open')
@section('limit_apply_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Reseller Limit</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        All Reseller
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Balance limit access
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            @include('admin.partials.session_messages')
            @include('admin.partials.all_error_messages')

            <table class="table table-striped table-bordered table-hover" id="reseller-limit-apply-table">
                <thead>
                <tr>
                    <th class="abcd">SL</th>
                    <th class="company">Company name</th>
                    <th>User name</th>
                    <th>Email</th>
                    <th>Credit limit</th>
                    <th>Employee limit</th>
                    <th>System</th>
                </tr>
                </thead>
                <tbody>

                @php($serial=1)
                @foreach($users as $user)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>{{ $user->company_name }}</td>
                        <td>{{ $user->userDetail['name'] }}</td>
                        <td>{{ $user->email }}</td>
                        
                        <form action="{{ route('admin.reseller.limitUpdate', $user->id) }}" id="form_{{$user->id}}" method="post">
                                @csrf
                            <td>
                                <input type="text" name="balanceLimit" class="input-sm" value="{{ $user->userDetail['limit'] }}">
                            </td>

                            <td>
                                <input type="text" name="employeeLimit" class="input-sm" value="{{ $user->employee_limit }}">
                            </td>
                        </form>
                       
                        <td>
                            <button class="btn btn-primary btn-xs" onclick="submitLimitForm('form_{{$user->id}}')">Submit</button>
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
                width: 10px;
            }
            .company{
                width: 60px;
            }
        }
        
        </style>
@endsection

@section('custom_script')
    {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script> --}}
    <script type="text/javascript">
        // $('#reseller-limit-apply-table').DataTable();
        function submitLimitForm(formName){
            if(confirm('Are you Sure')) {
                $("#" + formName).submit();
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#reseller-limit-apply-table').DataTable( {
            rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
            
        } );
        
    } );
    </script>

@endsection

