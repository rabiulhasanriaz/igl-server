@extends('user.master')

@section('reports_menu_class','open')
@section('sms_bill_report_menu_class','active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('user.index') }}">Dashboard</a>
        </li>
        <li class="active">Reports SMS</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Reports & Statistics
        <i class="ace-icon fa fa-angle-double-right"></i>
        View DLR
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Archived SMS
        </small>
    </h1>
@endsection


@section('main_content')

<form action="" method="get">
		<input type="hidden" name="_token" value="" id="_token">
	   <div class="col-xs-3">
		<input type="text" name="start_date" data-date-format="yyyy-mm-dd" autocomplete="off" value="{{ $start_date }}" class="form-control" id="start" placeholder="Enter Start Date" >
	   </div>
		<div class="col-xs-3">
		<input type="text" name="end_date" data-date-format="yyyy-mm-dd" autocomplete="off" value="{{ $end_date }}" class="form-control" id="end" placeholder="Enter End Date" >
	   </div>
		  <div class="col-xs-3">
			<button type="submit" class="btn btn-info btn-sm" name="searchbtn">Search</button>
              <button type="button" onclick="downloadReport()" class="btn btn-danger btn-sm" name="searchbtn">Download</button>
		  </div>
    	  </form>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <table id="view_archived_report" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Campaign Type</th>
                    <th>Campaign Title</th>
                    <th>Submit time</th>
                    <th>SenderID</th>
                    <th>Submitted</th>
                    <th>Total sent</th>
                    <th>Charge</th>
                </tr>
                </thead>

                <tbody>
                @php($serial=1)

                @php($total=0)
                @php($total_sms=0)
                @php($total_load=0)
                @php($total_sub=0)
                @php($total_sub_sms=0)
                @php($total_sub_load=0)
                @php($debit1=0)
                @php($debit2=0)
                @php($credit=0)
                
                @if(!empty($transactions))
                <tr>
                    <td colspan="7" class="text-right">Balance BD</td>
                    <td class="text-right">{{ number_format($balancebd,2) }}</td>
                </tr>
                    @foreach($transactions as $transaction)
                    
                        <tr>
                            @if($transaction->asb_pay_mode == 4)
                                @php($smsCampaign = $transaction->smsCampaignId)
                                {{--@php($smsCampaign = \App\Model\AccSmsBalance::getSmsCampaignIdDetails($transaction->asb_pay_ref))--}}
                                <td>{{ $serial++ }}</td>
                                <td>SMS Campaign</td>
                                <td title="{{ @$smsCampaign->sci_campaign_id }}">
                                    {{ @$smsCampaign->sci_campaign_title ?? @$smsCampaign->sci_campaign_id }}
                                </td>
                                <td class="text-center">
                                    @if(!empty($transaction->asb_submit_time))
                                        {{ $transaction->asb_submit_time->format('H:i a, d-M-Y') }}
                                    @endif
                                </td>
                                <td>
                                    {{ @$smsCampaign->sender->sir_sender_id }}
                                </td>
                                <td class="text-center">
                                    {{ @$smsCampaign->sci_total_submitted }}
                                    @php($total_sub_sms = $total_sub_sms + @$smsCampaign->sci_total_submitted)
                                </td>
                                <td class="text-center">
                                    {{ @$smsCampaign->sci_total_submitted }}
                                    @php($total_sms = $total_sms + @$smsCampaign->sci_total_submitted)
                                </td>
                                <td class="text-right">
                                    -{{ number_format($transaction->asb_debit, 2) }}
                                    @php($debit1 = $debit1 + $transaction->asb_debit)
                                </td>
                            @elseif($transaction->asb_pay_mode == 5)
                                @php($loadCampaign = $transaction->loadCampaignId)
{{--                                @php($loadCampaign = \App\Model\AccSmsBalance::getLoadCampaignIdDetails($transaction->asb_pay_ref))--}}
                                <td>{{ $serial++ }}</td>
                                <td>Load Campaign</td>
                                <td title="{{ @$loadCampaign->campaign_id }}">
                                    {{ (@$loadCampaign->campaign_name != '')? @$loadCampaign->campaign_name:@$loadCampaign->campaign_id }}
                                </td>
                                <td class="text-center">
                                    @if(!empty($transaction->asb_submit_time))
                                        {{ $transaction->asb_submit_time->format('H:i a, d-M-Y') }}
                                    @endif
                                </td>
                                <td>N/A</td>
                                <td class="text-center">
                                    {{ @$loadCampaign->total_number }}
                                    @php($total_sub_load = $total_sub_load + @$loadCampaign->total_number)
                                </td>
                                <td class="text-center">
                                    {{ @$loadCampaign->total_number }}
                                    @php($total_load = $total_load + @$loadCampaign->total_number)
                                </td>
                                <td class="text-right">
                                    -{{ number_format($transaction->asb_debit, 2) }}
                                    @php($debit2 = $debit2 + $transaction->asb_debit)
                                </td>
                            @else
                                <td>{{ $serial++ }}</td>
                                <td>Deposit</td>
                                <td>
                                    Deposit
                                </td>
                                <td class="text-center">
                                    @if(!empty($transaction->asb_submit_time))
                                        {{ $transaction->asb_submit_time->format('H:i a, d-M-Y') }}
                                    @endif
                                </td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td class="text-right">
                                    {{ number_format($transaction->asb_credit, 2) }}
                                    @php($credit = $credit + $transaction->asb_credit)
                                </td>
                            @endif
                        </tr>

                    @endforeach
                
                @endif
                @php($sub_total = 0)
                @php($sub_total2 = 0)
                @php($amount = 0)
                @php($sub_total = $total_sms + $total_load)
                @php($sub_total2 = $total_sub_sms + $total_sub_load)
                @php($amount = ($credit + $balancebd) - ($debit1 + $debit2))

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right"><b>Total:</b></td>
                        <td class="text-center">
                            <b>{{ $sub_total2 }}</b>
                        </td>
                        <td class="text-center">
                            <b>
                            {{ $sub_total }}
                            </b>
                        </td>
                        <td class="text-right"><b>{{ number_format($amount,2) }}</b></td>
                    </tr>
                </tfoot>
            </table>
            {{-- {{ $balancebd }} --}}
            <!-- ------model view start-->
            <div id="my-modal" class="modal fade" tabindex="-1" style="display: none; z-index: 3000;">
                <div class="modal-dialog" style="width: 80%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 class="smaller lighter blue no-margin text-primary"> Today Report Details</h3>
                        </div>
                        <div class="modal-body">
                            <div id="SmsInformation"></div>
                        </div>
                    </div>
                </div><!-- /.modal-dialog -->
            </div>

            <div class="modal fade" id="api_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2000;">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Api Report Detail</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body" id="apiReportDetails">
                      
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>

        </div><!-- /.col -->
    </div><!-- /.row -->

@endsection
@section('custom_style')
<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datepicker3.min.css"/>
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
    @include('user.ajax.view_archived_report')
    <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        /*var table = $('#view_archived_report').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 2 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 1 },
                    { responsivePriority: 5, targets: 3 },
            ]
        } );*/
    } );
    </script>
    <script src="{{ asset('assets') }}/js/bootstrap-datepicker.min.js"></script>

    <script>
        // $('#view_archived_report').DataTable();
        $(document).ready(function () {
            $('#start').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            $('#end').datepicker({
                autoclose: true,
                todayHighlight: true
            });
        });

        function downloadReport() {
            let startDate = $("#start").val();
            let endDate = $("#end").val();
            let route = "{!! route('user.reports.bill-report-download') !!}?start_date="+startDate+"&end_date="+endDate;
            window.open(route, '_blank');
        }
    </script>

@endsection