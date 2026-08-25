@extends('admin.master')

@section('operator_class','active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('reseller.index') }}">Dashboard</a>
        </li>
        <li class="active">30 Days Reports</li>
    </ul><!-- /.breadcrumb -->
@endsection




@section('main_content')

    <div class="row" id="app">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <ul class="list-group">
                    <li class="list-group-item active text-center">Last 30 Days SMS Reports</li>
                </ul>
                <table id="sms" class="display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Operator</th>
                            <th>Total SMS</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($sl=0)
                        @php($total_op_sms=0)
                        @php($total_op_cost=0)
                        @foreach ($sms_report as $sms)
                        @php($total_op_sms += $sms->total)
                        @php($total_op_cost += $sms->total_cost)
                        <tr>
                            <td>{{ ++$sl }}</td>
                            <td title="">{{ $sms->operator->ope_operator_name }}</td>
                            <td class="text-center">{{ $sms->total }}</td>
                            <td class="text-right">{{ number_format($sms->total_cost,2) }}</td>
                        </tr>  
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total</th>
                            <th class="text-center">{{ $total_op_sms }}</th>
                            <th class="text-right">{{ number_format($total_op_cost,2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <ul class="list-group">
                    <li class="list-group-item active text-center">Last 30 Days Flexi Reports</li>
                </ul>
                <table id="flexi" class="display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>CellPhone</th>
                            <th>Total Number</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                       @php($sl=0)
                       @php($total_op_flexi=0)
                       @php($total_op_cost=0)
                       @foreach ($flexi_report as $flexi)
                       @php($total_op_flexi += $flexi->total)
                       @php($total_op_cost += $flexi->total_cost)
                       <tr>
                            <td>{{ ++$sl }}</td>
                            <td title="">
                                @if ($flexi->operator_id == 'airtel')
                                    Airtel
                                @elseif($flexi->operator_id == 'blink')
                                    Banglalink
                                @elseif($flexi->operator_id == 'gp')
                                    GrameenPhone
                                @elseif($flexi->operator_id == 'robi')
                                    Robi
                                @elseif($flexi->operator_id == 'teletalk')
                                    Teletalk 
                                @endif
                            </td>
                            <td class="text-center">{{ $flexi->total }}</td>
                            <td class="text-right">{{ $flexi->total_cost }}</td>
                        </tr>
                       @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total</th>
                            <th class="text-center">{{ $total_op_flexi }}</th>
                            <th class="text-right">{{ $total_op_cost }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->


@endsection




@section('custom_style')
    <link href="{{ asset('assets') }}/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css" rel="stylesheet" />
@endsection


@section('custom_script')
    {{-- <script src="{{ asset('assets') }}/js//bootstrap-datetimepicker.min.js"></script> --}}
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#sms').DataTable( {
                
                responsive: true
            } );
        } );

        $(document).ready(function() {
            var table = $('#flexi').DataTable( {
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true
            } );
        } );

       
    </script>
    

    {{-- @include('admin.ajax.check_customer_available_balance') --}}
@endsection
