@extends('user.master')

@section('dynamic_reports_menu_class','open')
@section('dynamic_view_dlr_menu_class','open')
@section('dynamic_todays_sms_report_menu_class','active')
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

            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Campaign Title</th>
                    <th>Submit time</th>
                    <th>Submitted</th>
                    <th>Total sent</th>
                    <th>Charge</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @php
                ($serial=1)
                @endphp
                @empty(!$todays_campaigns_by_api)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>API</td>
                        
                        <td></td>
                        <td class="text-center">{{ $todays_campaigns_by_api->sub_sum }}</td>
                        <td class="text-center">{{ $todays_campaigns_by_api->sub_sum }}</td>
                        <td class="text-right">BDT {{ number_format($todays_campaigns_by_api->sum, 2) }}</td>
                        <td>
                            <label>
                                <a href="" data-toggle="modal" data-target="#today_modal" onclick="todays_reports()">
                                    View
                                </a>
                            </label>
                            |
                            <label>
                                <a href="{{ route('user.reports.download_dynamic_todays_report', $todays_campaigns_by_api->id) }}" target="_blank" class="btn-none-download"> Download </a>
                            </label>
                        </td>
                    </tr>
                @endempty
                @foreach($todays_campaigns as $todays_campaign)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td title="{{ $todays_campaign->sdci_campaign_id }}">{{ $todays_campaign->sdci_campaign_title }}</td>
                        <td>{{ $todays_campaign->sdci_targeted_time->format('H:i a, d-M-Y') }}</td>
                        <td class="text-center">{{ $todays_campaign->sdci_total_submitted }}</td>
                        <td class="text-center">{{ $todays_campaign->sdci_total_submitted }}</td>
                        <td class="text-right">BDT {{ number_format($todays_campaign->sdci_total_cost, 2) }}</td>
                        <td>
                            <label>
                                <a href="#my-modal" onclick="show_today_details('{{$todays_campaign->id}}')"
                                   role="button" data-toggle="modal"
                                   class="btn-none-edit CampaignId_one"> View </a>
                            </label>
                            |
                            <label>
                                <a href="{{ route('user.reports.download_dynamic_todays_report', $todays_campaign->id) }}" target="_blank" class="btn-none-download"> Download </a>
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


@section('custom_script')
    @include('user.ajax.dynamic.ajax.view_todays_report')
    <script type="text/javascript">

        function todays_reports() {
      
          let url = "{{ route('user.reports.today-report-ajax-dynamic') }}";
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












