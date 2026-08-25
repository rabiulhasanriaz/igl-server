@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexi_commission_view_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Reseller flexiload Commisions Limit</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        All Reseller
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Comission
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            @include('admin.partials.session_messages')
            @include('admin.partials.all_error_messages')

            <table class="table table-striped table-bordered table-hover" id="reseller_list">
                <thead>
                <tr>
                    <th>SL</th>
                    <th class="hidden-480">Company name</th>
                    <th>User name</th>
                    <th>Email</th>
                    <th>Comissions</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

                @php($serial=1)
                @foreach($resellers as $reseller)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>{{ $reseller->company_name }}</td>
                        <td>{{ $reseller->userDetail['name'] }}</td>
                        <td>{{ $reseller->email }}</td>
                        <td>
                            <form action="{{ route('admin.flexiload.setComissions') }}" id="form_{{$reseller->id}}" method="post">
                                    @csrf
                                <input type="text" name="reseller_id" value="{{ $reseller->id }}" hidden>
                                
                                    <input type="text" name="comission" class="input-sm" value="{{ $reseller->flexiload_commission }}">
                                
                            </form>
                       </td>
                        <td>
                            <button class="btn btn-primary btn-xs" onclick="submitLimitForm('form_{{$reseller->id}}')">Submit</button>
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
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script> --}}
    {{--<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        var table = $('#reseller_list').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 5 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
                    { responsivePriority: 6, targets: 4 },
            ]
        } );
    } );
        function submitLimitForm(formName){
            if(confirm('Are you Sure')) {
                $("#" + formName).submit();
            }
        }
    </script>--}}

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#reseller_list').DataTable( {
            rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
            
        } );
        
    } );

        function submitLimitForm(formName){
            if(confirm('Are you Sure')) {
                $("#" + formName).submit();
            }
        }
    </script>


@endsection
