@extends('user.master')

@section('dynamic_reports_menu_class','open')
@section('pending_sms_report_menu_class','active')
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
            Pending's SMS
        </small>
    </h1>
@endsection


@section('main_content')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
@include('user.partials.session_messages')
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Campaign Title</th>
                    <th>Submit time</th>
                    <th>Submitted</th>
                    <th>Charge</th>
                    <th>Content</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @php($serial=1)
                @foreach($campaigns as $pending_campaign)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td title="{{ $pending_campaign->sci_campaign_id }}">{{ $pending_campaign->campaignId->sdci_campaign_title }}</td>
                        <td>
                            <form action="{{ route('user.reports.reschedule-campaign',$pending_campaign->campaign_id) }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control date-timepicker" name="target_time" data-date-format="YYYY-MM-DD h:mm A" value="{{ $pending_campaign->campaignId->sdci_targeted_time }}">
                                    </div>
                                    <div class="col-sm-4">
                                        <button type="submit" class="btn btn-success btn-xs">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">{{ $pending_campaign->campaignId->sdci_total_submitted }}</td>
                        <td class="text-center"><pre>{!! $pending_campaign->sdp_message !!}</pre></td>
                        <td class="text-right">BDT {{ number_format($pending_campaign->campaignId->sdci_total_cost, 2) }}</td>
                        <td class="text-center">
                            <a class="btn btn-danger btn-xs" href="{{ route('user.reports.reject-sms-campaign',$pending_campaign->campaign_id) }}" onclick="return confirm('Are You Sure?');">
                                <i class="fa fa-trash"></i>
                            </a>
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
<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datetimepicker.min.css"/>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function () {
            $('.date-timepicker').datetimepicker();
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












