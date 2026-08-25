@extends('reseller.master')

@section('sms_flexi_report_class','open')
@section('sms_flexi_class','active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('reseller.index') }}">Dashboard</a>
        </li>
        <li class="active">24 Hour Reports</li>
    </ul><!-- /.breadcrumb -->
@endsection




@section('main_content')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <ul class="list-group">
                    <li class="list-group-item active text-center">Last 24hs SMS Reports</li>
                </ul>
                <table id="sms" class="display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Cellphone</th>
                            <th>Total SMS</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($sl=0)
                        @php($total_sms=0)
                        @php($total_sms_cost=0)

                        @foreach ($sms_user as $sms)
                        @php($total_sms += OtherHelpers::total_sms($sms->user_id))
                        @php($total_sms_cost += BalanceHelper::sms_cost($sms->user_id))
                        <tr>
                            <td>{{ ++$sl }}</td>
                            <td title="{{ $sms->user['company_name'] }}">{{ $sms->user['cellphone'] }}</td>
                            <td class="text-center">{{ OtherHelpers::total_sms($sms->user_id) }}</td>
                            <td class="text-right">{{ number_format(BalanceHelper::sms_cost($sms->user_id),2) }}</td>
                        </tr>
                        
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total</th>
                            <th class="text-center">{{ $total_sms }}</th>
                            <th class="text-right">{{ number_format($total_sms_cost,2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <ul class="list-group">
                    <li class="list-group-item active text-center">Last 24hs Flexi Reports</li>
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
                        @php($total_flexi=0)
                        @php($total_flexi_cost=0)
                        @foreach ($flexi_user as $flexi)
                        @php($total_flexi += OtherHelpers::total_flexi($flexi->user_id))
                        @php($total_flexi_cost += BalanceHelper::flexi_cost($flexi->user_id))
                        <tr>
                            <td>{{ ++$sl }}</td>
                            <td title="{{ $flexi->trx_user['company_name'] }}">{{ $flexi->trx_user['cellphone'] }}</td>
                            <td class="text-center">{{ OtherHelpers::total_flexi($flexi->user_id) }}</td>
                            <td class="text-right">{{ number_format(BalanceHelper::flexi_cost($flexi->user_id),2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total</th>
                            <th class="text-center">{{ $total_flexi }}</th>
                            <th class="text-right">{{ number_format($total_flexi_cost,2) }}</th>
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
    <script src="{{ asset('assets') }}/js//moment.min.js"></script>
    <script src="{{ asset('assets') }}/js//bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
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

        Vue.filter('formatAmount', function(value) {
        if (value) {
            return new Intl.NumberFormat().format(value);
            }
        });
        let app = new Vue({
            el: '#app',
            
        })
    </script>
    

    {{-- @include('admin.ajax.check_customer_available_balance') --}}
@endsection
