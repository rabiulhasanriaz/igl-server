@extends('admin.master')

@section('route2_class','active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Route 2 Total Send</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Route 2
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Details
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

          
            
            
                
            <table class="table table-striped table-bordered table-hover" id="reseller_list">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>User ID</th>
                    <th>Company Name</th>
                    <th>Cellphone</th>
                    <th>Email</th>
                    <th>Total Submitted</th>
                    <th>Total Cost</th>
                    <th>Details</th>
                </tr>
                </thead>
                <tbody>
                @php
                $sl=0;
                $total_submitted = 0;
                $total_cost = 0;
                @endphp
                @foreach ($sms_report as $report)
                <tr>
                    <td>{{ ++$sl }}</td>
                    <td class="text-center">{{ $report->user_id }}</td>
                    <td>{{ $report->user->company_name }}</td>
                    <td>{{ $report->user->cellphone }}</td>
                    <td>{{ $report->user->email }}</td>
                    <td class="text-right">{{ number_format($report->total_submit,2) }}</td>
                    <td class="text-right">{{ number_format($report->total_cost,2) }}</td>
                    <td>
                        <a href="javascript:void(0);" onclick="sell_reports('{{ $report->user_id }}')">Details</a>
                    </td>
                </tr>
                @php($total_submitted = $total_submitted + $report->total_submit)
                @php($total_cost = $total_cost + $report->total_cost)
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">Total</td>
                        <td class="text-right">{{ number_format($total_submitted,2) }}</td>
                        <td class="text-right">{{ number_format($total_cost,2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

        </div><!-- /.col -->
    </div><!-- /.row -->

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Details</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="smsDetails">
              
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
@endsection


@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#reseller_list').DataTable();
        function sell_reports(user) {

            let url = "{{ route('admin.route-2-report-ajax') }}";
            var _token=$("#_token").val();
            $.ajax({  
            type: "GET",
            url: url,
            data: { user: user,_token:_token},
            success: function (result) {
            $("#smsDetails").html(result);
            $("#exampleModal").modal("show");
            }
        });
        }
        
    </script>

@endsection
