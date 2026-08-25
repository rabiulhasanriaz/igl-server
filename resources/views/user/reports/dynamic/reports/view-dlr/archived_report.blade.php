@extends('user.master')

@section('dynamic_reports_menu_class','open')
@section('dynamic_view_dlr_menu_class','open')
@section('dynamic_archived_report_menu_class','active')
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
            <button type="button" onclick="downloadReport()" class="btn btn-danger btn-sm">Download</button>
          </div>
          </form>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <table id="view_archived_report" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Campaign Title</th>
                    <th class="hidden-600">Submit time</th>
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
               @empty(!$archived_campaigns_by_api)
               
                <tr>
                    <td>{{ $serial++ }}</td>
                    <td> API </td>
                    <td></td>
                    <td class="text-center">{{ $archived_campaigns_by_api->sub_sum }}</td>
                    <td class="text-center">{{ $archived_campaigns_by_api->sub_sum }}</td>
                    <td class="text-right">BDT {{ number_format($archived_campaigns_by_api->sum, 2) }}</td>
                    <td>
                        <label>
                           <a href="" data-toggle="modal" data-target="#api_modal" onclick="api_reports('{{ $start_date }}', '{{ $end_date }}')">
                               View
                            </a>
                        </label>
                        |
                        <label>

                            <label>
                                <a href="" target="_blank" class="btn-none-download"> Download </a>
                            </label>
                        </label>
                    </td>
                </tr>
                @endempty
                @foreach($archived_campaigns as $archived_campaign)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td title="{{ $archived_campaign->sdci_campaign_id }}">{{ $archived_campaign->sdci_campaign_title }}</td>
                        <td>
                            @if(!empty($archived_campaign->sdci_targeted_time))
                                {{ $archived_campaign->sdci_targeted_time->format('H:i a, d-M-Y') }}
                            @endif
                        </td>
                        <td class="text-center">{{ $archived_campaign->sdci_total_submitted }}</td>
                        <td class="text-center">{{ $archived_campaign->sdci_total_submitted }}</td>
                        <td class="text-right">BDT {{ number_format($archived_campaign->sdci_total_cost, 2) }}
                            @foreach ($cost as $sms)
                            @if ($archived_campaign->id == $sms->campaign_id)
                            ({{ $sms->sd_sms_cost }})
                            @endif
                            @endforeach

                        </td>
                        <td>
                            <label>
                                <a href="#my-modal" onclick="show_archived_details('{{$archived_campaign->id}}')"
                                   role="button" data-toggle="modal"
                                   class="btn-none-edit CampaignId_one"> View </a>
                            </label>
                            |
                            <label>

                                <label>
                                    <a href="{{ route('user.reports.download_dynamic_archived_report', $archived_campaign->id) }}" target="_blank" class="btn-none-download"> Download </a>
                                </label>
                            </label>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>

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
    @include('user.ajax.dynamic.ajax.view_archived_report')
    {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script> --}}
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script> -->
    {{--<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>--}}
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        var table = $('#view_archived_report').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 2 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 1 },
                    { responsivePriority: 5, targets: 3 },
            ]
        } );
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
    </script>
    <script type="text/javascript">

        function api_reports(start_date, end_date) {
      
          let url = "{{ route('user.reports.api-report-ajax-dynamic') }}";
          var _token=$("#_token").val();
          $.ajax({  
            type: "GET",
            url: url,
            data: { _token:_token, start_date:start_date, end_date:end_date},
            success: function (result) {
             $("#apiReportDetails").html(result);
             $("#api_modal").modal("show");
            }
          });
        }

        function downloadReport() {
            let startDate = $("#start").val();
            let endDate = $("#end").val();
            let route = "{!! route('user.reports.total-report-download') !!}?start_date="+startDate+"&end_date="+endDate;
            window.open(route, '_blank');
        }
      </script>
@endsection