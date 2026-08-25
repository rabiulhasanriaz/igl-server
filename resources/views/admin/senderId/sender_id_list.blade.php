@extends('admin.master')

@section('sender_id_menu_class','open')
@section('sender_id_list_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Sender View</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Sender View
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            All information
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            @include('admin.partials.all_error_messages')
            @include('admin.partials.session_messages')

            <table id="senderid-list-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Sender ID</th>
                    <th>V/N Robi</th>
                    <th>V/N Teletalk</th>
                    <th>V/N Grameen</th>
                    <th>V/N Airtel</th>
                    <th>V/N BanglaLink</th>
                    <th>Active</th>
                    <th>Update</th>
                </tr>
                </thead>

                <tbody>
                @php($serial=1)
                @foreach($senderIds as $senderId)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>{{ $senderId->sir_sender_id }}</td>
                        <td id="robi_vn_{{$serial}}" my_val="{{ @$senderId->robi_virtual_number->id }}">{{ @$senderId->robi_virtual_number->sivn_number }}</td>
                        <td id="teletalk_vn_{{$serial}}" my_val="{{ @$senderId->teletalk_virtual_number->id }}">{{ @$senderId->teletalk_virtual_number->sivn_number }}
                            <input type="hidden" id="teletalk_api_user_{{$serial}}" value="{{ @$senderId->sir_teletalk_user_name }}">
                            <input type="hidden" id="teletalk_api_pass_{{$serial}}" value="{{ @$senderId->sir_teletalk_user_password }}">

                        </td>
                        <td id="gp_vn_{{$serial}}" my_val="{{ @$senderId->gp_virtual_number->id }}">{{ @$senderId->gp_virtual_number->sivn_number }}</td>
                        <td id="airtel_vn_{{$serial}}" my_val="{{ @$senderId->airtel_virtual_number->id }}">{{ @$senderId->airtel_virtual_number->sivn_number }}</td>
                        <td id="banglalink_vn_{{$serial}}" my_val="{{ @$senderId->banglalink_virtual_number->id }}">{{ @$senderId->banglalink_virtual_number->sivn_number }}</td>
                        <td>
                            @if($senderId->sir_active=='0')
                                <a href="{{ route('admin.senderID.update_status',[$senderId->id]) }}" class="btn-none-edit"
                                   onclick="return confirm('Are you sure you want to On ?');"> On </a>
                            @elseif($senderId->sir_active=='1')
                                <a href="{{ route('admin.senderID.update_status',[$senderId->id]) }}" class="btn-none-delete"
                                   onclick="return confirm('Are you sure you want to Off ?');"> Off </a>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="" 
                                    onclick="update_sender_id('{{ $senderId->id }}' , '{{ $senderId->sir_sender_id }}', '{{ $serial }}')"
                                    data-toggle="modal"
                                    data-target="#modal_update">Update
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>


        </div><!-- /.col -->
    </div><!-- /.row -->

    <!-- Modal for editing a sender ID -->
    <div id="modal_update" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Updating a Sender ID</h4>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <form action="{{ route('admin.senderID.update') }}" method="post" class="form-horizontal"
                          role="form">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="sender_ids_id" name="sender_ids_id" value="">
                        <div class="form-group">
                            <label for="">Edit Sender ID</label>
                            <input type="text" name="sender_id" value="" class="form-control" placeholder="Sender ID"
                                   maxlength="15" id="SenderId" required>
                            <span class="duplicate-senderId text-danger"></span>
                            <span class="valid-senderId text-success"></span>
                        </div>
                        <div class="form-group">
                            <label for=""> Virtual number Robi </label>
                            <br/>
                            <select class="chosen-select form-control  robi-select" id="robi_virtual_number_select" data-placeholder="Robi"
                                    name="robi_virtual_number" required>
                                <option value=""></option>
                                @foreach($robiVirtualNumbers as $robiVirtualNumber)
                                    <option value="{{ $robiVirtualNumber->id }}">{{ $robiVirtualNumber->sivn_number }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for=""> Virtual number Teletalk </label>
                            <br/>
                            <select class="teletalk-select col-sm-4" id="teletalk_virtual_number_select"
                                    data-placeholder="Teletalk" name="teletalk_virtual_number" required style="border-radius: 5px;">
                                <option value=""></option>
                                @foreach($teletalkVirtualNumbers as $teletalkVirtualNumber)
                                    <option value="{{ $teletalkVirtualNumber->id }}">{{ $teletalkVirtualNumber->sivn_number }}</option>
                                @endforeach
                            </select>

                            <input type="text" class="col-sm-4" id="teletalk_virtual_number_api_user"  name="teletalk_user_name" placeholder="User Name" required>
                            <input type="text" class="col-sm-4" id="teletalk_virtual_number_api_pass" name="teletalk_user_password" placeholder="User Password" required>


                        </div>

                        <div class="form-group">
                            <label for=""> Virtual number Grameen </label>
                            <br/>
                            <select class="chosen-select form-control gp-select" id="gp_virtual_number_select"
                                    data-placeholder="Grameen" name="gp_virtual_number" required>
                                <option value=""></option>
                                @foreach($gpVirtualNumbers as $gpVirtualNumber)
                                    <option value="{{ $gpVirtualNumber->id }}">{{ $gpVirtualNumber->sivn_number }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for=""> Virtual number Airtel </label>
                            <br/>
                            <select class="chosen-select form-control airtel-select" id="airtel_virtual_number_select"
                                    data-placeholder="Airtel " name="airtel_virtual_number" required>
                                <option value=""></option>
                                @foreach($airtelVirtualNumbers as $airtelVirtualNumber)
                                    <option value="{{ $airtelVirtualNumber->id }}">{{ $airtelVirtualNumber->sivn_number }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for=""> Virtual number BanglaLink </label>
                            <br/>
                            <select class="chosen-select form-control bl-select" id="banglalink_virtual_number_select"
                                    data-placeholder="BanglaLink" name="bl_virtual_number" required>
                                <option value=""></option>
                                @foreach($blVirtualNumbers as $blVirtualNumber)
                                    <option value="{{ $blVirtualNumber->id }}">{{ $blVirtualNumber->sivn_number }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="clearfix form-group" id="create_btn">

                            <input type="submit" class="btn btn-info" value="Submit">
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-danger" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>

                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
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
        var table = $('#senderid-list-table').DataTable( {
            rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
            
        } );
        
    } );
    </script>

    {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script> --}}
    {{--<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>--}}
    <script type="text/javascript">
        // $('#senderid-list-table').DataTable();
    //     $(document).ready(function() {
    //     var table = $('#senderid-list-table').DataTable( {
    //         responsive: true,
    //         columnDefs: [
    //                 { responsivePriority: 1, targets: 0 },
    //                 { responsivePriority: 2, targets: 1 },
    //                 { responsivePriority: 3, targets: 5 },
    //                 { responsivePriority: 4, targets: 2 },
    //                 { responsivePriority: 5, targets: 3 },
    //                 { responsivePriority: 6, targets: 4 },
    //                 { responsivePriority: 7, targets: 6 },
    //         ]
    //     } );
    // } );
        function update_sender_id(senderIdsId, SenderId, serial) {
            $('#SenderId').val(SenderId);
            $('#sender_ids_id').val(senderIdsId);
            var gp_vn = $('#gp_vn_'+serial).attr('my_val');
            var robi_vn = $('#robi_vn_'+serial).attr('my_val');
            var teletalk_vn = $('#teletalk_vn_'+serial).attr('my_val');
            var teletalk_api_user = $('#teletalk_api_user_'+serial).val();
            var teletalk_api_pass = $('#teletalk_api_pass_'+serial).val();
            var airtel_vn = $('#airtel_vn_'+serial).attr('my_val');
            var banglalink_vn = $('#banglalink_vn_'+serial).attr('my_val');
            console.log(gp_vn);
            console.log(robi_vn);
            console.log(teletalk_vn);
            console.log(airtel_vn);
            console.log(banglalink_vn);
            $('#gp_virtual_number_select').val(gp_vn).prop('selected', true);
            $('#robi_virtual_number_select').val(robi_vn).prop('selected', true);
            $('#teletalk_virtual_number_select').val(teletalk_vn).prop('selected', true);
            $('#teletalk_virtual_number_api_user').val(teletalk_api_user);
            $('#teletalk_virtual_number_api_pass').val(teletalk_api_pass);
            $('#airtel_virtual_number_select').val(airtel_vn).prop('selected', true);
            $('#banglalink_virtual_number_select').val(banglalink_vn).prop('selected', true);
        }
    </script>

@endsection