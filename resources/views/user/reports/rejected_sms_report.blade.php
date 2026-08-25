@extends('user.master')

@section('reports_menu_class','open')
@section('rejected_sms_report_menu_class','active')
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
            Rejected SMS
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
                    <th>SenderID</th>
                    <th>Submitted</th>
                    <th>Charge</th>
                    <th>Refund Amount</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                @php($serial=1)
                @if($rejected_campaigns)
                    @foreach($rejected_campaigns as $rejected_campaign)
                        <tr>
                            <td>{{ $serial++ }}</td>
                            <td title="{{ $rejected_campaign->sci_campaign_id }}">{{ $rejected_campaign->sci_campaign_title }}</td>
                            <td>{{ $rejected_campaign->sci_targeted_time->format('H:i a, d-M-Y') }}</td>
                            <td>{{ $rejected_campaign->sender->sir_sender_id }}</td>
                            <td class="text-center">{{ $rejected_campaign->sci_total_submitted }}</td>
                            <td class="text-right">BDT {{ number_format($rejected_campaign->sci_total_cost, 2) }}</td>
                            <td class="text-right">BDT {{ number_format($rejected_campaign->sci_total_cost, 2) }}</td>
                            <td>
                                <label class="text-danger">Rejected</label>
                            </td>
                        </tr>
                    @endforeach
                @endif
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
    @include('user.ajax.view_todays_report')
    <script type="text/javascript">

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












