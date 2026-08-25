@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_api_view_class','open')
@section('flexiload_load_api_all_class', 'active')
@section('page_location')

<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="{{ route('admin.index') }}">Dashboard</a>
    </li>
    <li class="active">API List</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
    API
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        List
    </small>
</h1>
@endsection

@section('main_content')

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <style>
        input[type=checkbox].ace.ace-switch.ace-switch-4:checked+.lbl::before, input[type=checkbox].ace.ace-switch.ace-switch-5:checked+.lbl::before {
            background-color: #25af56;
        }
        input[type=checkbox].ace.ace-switch.ace-switch-4+.lbl::before, input[type=checkbox].ace.ace-switch.ace-switch-5+.lbl::before {
            background-color: #e41d1d;
            border: 1px solid #ce2b42;
            
        }
    </style>
    <div class="col-sm-6">
        @foreach ($apiController as $apiCtr)

            @if ($apiCtr->api_one_status == 1)
                <style>
                input[type=checkbox].ace.ace-switch.ace-switch-4:checked+.lbl::before, inp[type=checkbox].ace.ace-switch.ace-switch-5:checked+.lbl::before {
                background-color: #25af56;
                }
                </style>
                        
            @else
                <style>
                input[type=checkbox].ace.ace-switch.ace-switch-4+.lbl::before, input[type=checkbox].ace.ace-switch.ace-switch-5+.lbl::before {
                background-color: #e41d1d;
                border: 1px solid #ce2b42;
                                    
                }
                </style>
            @endif
                <h4>Active Regular API</h4>
                <input name="switch-field-1" id="contactID" {{ ($apiCtr->api_one_status == 1)?'checked' : '' }} onchange="updateStatusOne('{{ $apiCtr->api_one_status }}')" value="{{ $apiCtr->api_one_status }}" class="ace ace-switch ace-switch-5" type="checkbox" />
                <span class="lbl"></span>
                

    </div>
    <div class="col-sm-6">

            @if ($apiCtr->api_two_status == 1)
                <style>
                input[type=checkbox].ace.ace-switch.ace-switch-4:checked+.lbl::before, inp[type=checkbox].ace.ace-switch.ace-switch-5:checked+.lbl::before {
                background-color: #25af56;
                }
                </style>
                        
            @else
                <style>
                input[type=checkbox].ace.ace-switch.ace-switch-4+.lbl::before, input[type=checkbox].ace.ace-switch.ace-switch-5+.lbl::before {
                background-color: #e41d1d;
                border: 1px solid #ce2b42;
                                    
                }
                </style>
            @endif
                <h4>Active External API</h4>
                <input name="switch-field-1" id="contactID" {{ ($apiCtr->api_two_status == 1)?'checked' : '' }} onchange="updateStatusTwo('{{ $apiCtr->api_two_status }}')" value="{{ $apiCtr->api_two_status }}" class="ace ace-switch ace-switch-5" type="checkbox" />
                <span class="lbl"></span>
                

        @endforeach
    </div>
</div>
</div>

<div class="space-6"></div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        @include('admin.partials.session_messages')

        <table id="api-list-table" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>SL</th>
                <th>Operator Name</th>
                <th>Operator IP</th>
                <th>Operator Port</th>
                <th>User</th>
                <th>User Port</th>
                <th>Operator USSD</th>
                <th>Operator Balance</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>

            <!-- API lists -->
            @php($sl=0)
            
            @foreach ($apiInfo as $apiItem)
                <tr>
                    <td style="width:8px">{{ ++$sl }}</td>
                    <td>{{ $apiItem->operator_name }}</td>
                    <td>{{ $apiItem->operator_ip }}</td>
                    <td>{{ $apiItem->operator_port }}</td>
                    <td>{{ $apiItem->operator_user }}</td>
                    <td>{{ $apiItem->operator_user_port }}</td>
                    <td>Prepaid: *{{ $apiItem->operator_ussd_prepaid }}#<br>
                        Postpaid: *{{ $apiItem->operator_ussd_postpaid }}#</td>

                        @if ($apiItem->operator_name == "Airtel")
                        <td>{{ $latestbal->airtel }}</td>
                        @endif
                        @if ($apiItem->operator_name == "Blink")
                        <td>{{ $latestbal->blink }}</td>
                        @endif
                        @if ($apiItem->operator_name == "Robi")
                        <td>{{ $latestbal->robi }}</td>
                        @endif
                        @if ($apiItem->operator_name == "GP")
                        <td>{{ $latestbal->gp }}</td>
                        @endif
                        @if ($apiItem->operator_name == "Teletalk")
                        <td>{{ $latestbal->teletalk }}</td>
                        @endif
                        
                    <td style="width:10px">
                        @if ($apiItem->operator_status == 1)
                        <span class="text-success">Active</span>
                        @else
                        <span class="text-danger">In-Active</span>
                        @endif
                    </td>
                    <td style="width:8px">
                        <div class="widget-toolbar no-border">
                            <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                Action
                                <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-primary dropdown-menu-right dropdown-caret dropdown-close">
                                <li>
                                    @if($apiItem->operator_status=='1')
                                        <a href="{{ route('admin.flexiload.load-api-inactive', $apiItem->operator_id) }}" class="tooltip-error" data-rel="tooltip" title="Confirm">
                                            <span class="label label-sm label-warning">Inactive</span>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.flexiload.load-api-active', $apiItem->operator_id) }}" class="" data-rel="tooltip" title="Conform">
                                            <span class="label label-sm label-success">Active</span>
                                        </a>
                                    @endif
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a class="green" href="{{ route('admin.flexiload.load-api-edit',$apiItem->operator_id) }}">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="red" href="{{ route('admin.flexiload.load-api-delete',$apiItem->operator_id) }}" onclick="return confirm('Are you sure to delete this API? If you delete this, Your all port wise pending flexi could be cancel.')">
                                        <i class="ace-icon fa fa-trash bigger-130"></i> Delete
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.flexiload.load-api-details',$apiItem->operator_user_port) }}"
                                       class="tooltip-error" data-rel="tooltip" title="Port Details">
                                        <span class="label label-sm label-primary">Port Details</span>
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
    var table = $('#api-list-table').DataTable( {
        responsive: true,
        
    } );
} );
</script>

<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

 function updateStatusOne(statusValue){

   $.ajax({
    url:"{{ route('admin.flexiload.api-one-status') }}",
    method:"POST",
    data: {statusValue:statusValue},
    
    success:function(data)
    {
        
    }
   });
  
 }

 function updateStatusTwo(statusValue){

$.ajax({
 url:"{{ route('admin.flexiload.api-two-status') }}",
 method:"POST",
 data: {statusValue:statusValue},
 
 success:function(data)
 {
     
 }
});

}
    
</script>

@endsection