@extends('user.master')

@section('reports_menu_class','open')
@section('view_dlr_menu_class','open')
@section('todays_sms_report_menu_class','active')
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
            Today's SMS
        </small>
    </h1>
@endsection


@section('main_content')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div style="margin-bottom: 12px;">
                <a href="{{ route('user.reports.today-report-csv') }}" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel-o"></i> Download Today CSV
                </a>
            </div>

            <table id="today_sms_report_table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Campaign Title</th>
                    <th>Submit time</th>
                    <th>SenderID</th>
                    <th>SMS Count</th>
                    <th>Total sent</th>
                    <th>Charge</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @php($serial=1)
                @foreach($todays_campaigns_by_api as $api_campaign)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td title="{{ $api_campaign->sci_campaign_id }}">API - {{ $api_campaign->sci_campaign_title ?? $api_campaign->sci_campaign_id }}</td>
                        <td>{{ $api_campaign->sci_targeted_time->format('H:i a, d-M-Y') }}</td>
                        <td>{{ optional($api_campaign->sender)->sir_sender_id }}</td>
                        <td class="text-center">{{ number_format($api_campaign->report_sms_count) }}</td>
                        <td class="text-center">{{ number_format($api_campaign->report_recipient_count) }}</td>
                        <td class="text-right">BDT {{ number_format($api_campaign->sci_total_cost, 2) }}</td>
                        <td>
                            @if($api_campaign->sci_campaign_status == 1)
                                <span class="label label-success">Completed</span>
                            @elseif($api_campaign->sci_campaign_status == 2)
                                <span class="label label-danger">Rejected</span>
                            @elseif($api_campaign->sci_campaign_status == 0)
                                <span class="label label-warning">Pending</span>
                            @elseif($api_campaign->sci_campaign_status == 3)
                                <span class="label label-info">Processing</span>
                            @else
                                <span class="label label-default">Unknown</span>
                            @endif
                        </td>
                        <td>
                            <label>
                                <a href="#my-modal" onclick="show_today_details('{{ $api_campaign->id }}')"
                                   role="button" data-toggle="modal" class="btn-none-edit CampaignId_one"> View </a>
                            </label>
                            |
                            <label>
                                <a href="{{ route('user.reports.download_todays_report', $api_campaign->id) }}" target="_blank" class="btn-none-download"> Download </a>
                            </label>
                        </td>
                    </tr>
                @endforeach
                @foreach($todays_campaigns as $todays_campaign)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td title="{{ $todays_campaign->sci_campaign_id }}">{{ $todays_campaign->sci_campaign_title }}</td>
                        <td>{{ $todays_campaign->sci_targeted_time->format('H:i a, d-M-Y') }}</td>
                        <td>{{ optional($todays_campaign->sender)->sir_sender_id }}</td>
                        <td class="text-center">{{ number_format($todays_campaign->report_sms_count) }}</td>
                        <td class="text-center">{{ number_format($todays_campaign->report_recipient_count) }}</td>
                        <td class="text-right">BDT {{ number_format($todays_campaign->sci_total_cost, 2) }}</td>
                        <td>
                            @if($todays_campaign->sci_campaign_status == 1)
                                <span class="label label-success">Completed</span>
                            @elseif($todays_campaign->sci_campaign_status == 2)
                                <span class="label label-danger">Rejected</span>
                            @elseif($todays_campaign->sci_campaign_status == 0)
                                <span class="label label-warning">Pending</span>
                            @elseif($todays_campaign->sci_campaign_status == 3)
                                <span class="label label-info">Processing</span>
                            @else
                                <span class="label label-default">Unknown</span>
                            @endif
                        </td>
                        <td>
                            <label>
                                <a href="#my-modal" onclick="show_today_details('{{$todays_campaign->id}}')"
                                   role="button" data-toggle="modal"
                                   class="btn-none-edit CampaignId_one"> View </a>
                            </label>
                            |
                            <label>
                                <a href="{{ route('user.reports.download_todays_report', $todays_campaign->id) }}" target="_blank" class="btn-none-download"> Download </a>
                            </label>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- ------model view start-->
            <div id="my-modal" class="modal fade" tabindex="-1" style="display: none; z-index: 2001;">
                <div class="modal-dialog" style="width: 80%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 class="smaller lighter blue no-margin text-primary"> Today Report Details</h3>
                        </div>
                        <div class="modal-body">
                            <div id="SmsInformation"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div><!-- /.modal-dialog -->
            </div>

            <div class="modal fade" id="today_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2000;">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Api Report Detail</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body" id="todayReportDetails">
                          
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
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css">
    <style>
        .label {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px;
            text-align: center;
        }
        .label-success {
            background-color: #27ae60;
            color: #fff;
        }
        .label-danger {
            background-color: #e74c3c;
            color: #fff;
        }
        .label-warning {
            background-color: #f39c12;
            color: #fff;
        }
        .label-info {
            background-color: #3498db;
            color: #fff;
        }
        .label-default {
            background-color: #95a5a6;
            color: #fff;
        }
    </style>
@endsection


@section('custom_script')
    @include('user.ajax.view_todays_report')
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#today_sms_report_table')) {
                $('#today_sms_report_table').DataTable({
                    responsive: true,
                    deferRender: true,
                    pageLength: 50,
                    order: [[2, 'desc']]
                });
            }
        });

        function todays_reports() {
      
          let url = "{{ route('user.reports.today-report-ajax') }}";
          var _token=$("#_token").val();
          $.ajax({  
            type: "GET",
            url: url,
            data: { _token:_token},
            success: function (result) {
             $("#todayReportDetails").html(result);
             $("#today_modal").modal("show");
            }
          });
        }
      </script>
@endsection