@extends('user.master')

@section('balance_menu','active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('user.index') }}">Dashboard</a>
        </li>
        <li class="active">Statements</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        {{ Auth::user()->company_name }}
        <i class="ace-icon fa fa-angle-double-right"></i>
        Balance
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Statements
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

            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Seq.</th>
                        <th>Pay. Reference</th>
                        <th>Transaction Date</th>
                        <th>Pay. Mode</th>
                        <th>Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                    @endphp
                    @foreach ($stat as $balance)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $balance->asb_pay_ref }}</td>
                        <td>{{ $balance->created_at }}</td>
                        <td class="text-center">
                            @if ($balance->asb_pay_mode == 1)
                                Cash
                            @elseif($balance->asb_pay_mode == 2)
                                Bank
                            @elseif($balance->asb_pay_mode == 3)
                                Check
                            @else
                                Others
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($balance->asb_credit,2) }}</td>
                    </tr>
                    @php
                        $total = $total + $balance->asb_credit;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><b>In Word:- {{ \OtherHelpers::number_to_text($total) }}</b></td>
                        <td class="text-right"><b>Total</b></td>
                        <td colspan="1" class="text-right"><b>{{ number_format($total,2) }}</b></td>
                    </tr>
                </tfoot>
              
            </table>

            <!-- ------model view start-->

        </div><!-- /.col -->
    </div><!-- /.row -->

@endsection
@section('custom_style')
<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datepicker3.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"/>
    <link href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/responsive/2.2.8/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css">
@endsection

@section('custom_script')
    {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> --}}
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.8/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
            var table = $('#example').DataTable( {
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true
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
      
          let url = "{{ route('user.reports.api-report-ajax') }}";
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
            let route = "{!! route('user.user-balance-statements.balance-pdf') !!}?start_date="+startDate+"&end_date="+endDate;
            window.open(route, '_blank');
        }
      </script>
@endsection