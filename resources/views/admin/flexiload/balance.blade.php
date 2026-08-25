@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_balance_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Flexiload Balance Enquiry</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Flexiload
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Available Balance
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            @include('admin.partials.session_messages')
            @include('admin.partials.all_error_messages')
        <div class="col-sm-6">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Airtel</span>
                  <span class="badge badge-primary badge-pill">
                      @if ($latestbal->airtel == NULL)
                      <span style="font-size: 20px;">0.00</span>
                      @else
                      <span style="font-size: 20px;">{{ $latestbal->airtel }}</span>
                      @endif
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Banglalink</span>
                  <span class="badge badge-primary badge-pill">
                      @if ($latestbal->blink == NULL)
                      <span style="font-size: 20px;">0.00</span>
                      @else
                      <span style="font-size: 20px;">{{ $latestbal->blink }}</span>
                      @endif
                  </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span style="font-size: 20px;">GrameenPhone</span>
                  <span class="badge badge-primary badge-pill">
                      @if ($latestbal->gp == NULL)
                      <span style="font-size: 20px;">0.00</span>
                      @else
                      <span style="font-size: 20px;">{{ $latestbal->gp }}</span>
                      @endif
                  </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Robi</span>
                    <span class="badge badge-primary badge-pill">
                        @if ($latestbal->robi == NULL)
                        <span style="font-size: 20px;">0.00</span>
                        @else
                        <span style="font-size: 20px;">{{ $latestbal->robi }}</span>
                        @endif
                    </span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Teletalk</span>
                    <span class="badge badge-primary badge-pill">
                        @if ($latestbal->teletalk == NULL)
                        <span style="font-size: 20px;">0.00</span>
                        @else
                        <span style="font-size: 20px;">{{ $latestbal->teletalk }}</span>
                        @endif
                    </span>
                  </li>
                  @php
                      $total = $latestbal->airtel + $latestbal->gp + $latestbal->blink + $latestbal->robi + $latestbal->teletalk;
                  @endphp
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;" class="text-danger">Total</span>
                    <span class="badge badge-danger badge-pill">
                        <span style="font-size: 20px;">{{ $total }}</span>
                    </span>
                  </li>
              </ul>

              <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px; margin-left: 100px;" class="text-warning">Pending Flexiload Balance</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Prepaid</span>
                  <span class="badge badge-primary badge-pill">
                      @if ($pending_bal_pre == 0)
                      <span style="font-size: 20px;">0.00</span>
                      @else
                      <span style="font-size: 20px;">{{ $pending_bal_pre }}</span>
                      @endif
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Postpaid</span>
                  <span class="badge badge-primary badge-pill">
                      @if ($pending_bal_post == 0)
                      <span style="font-size: 20px;">0.00</span>
                      @else
                      <span style="font-size: 20px;">{{ $pending_bal_post }}</span>
                      @endif
                  </span>
                </li>
                  @php
                      $total = $pending_bal_pre + $pending_bal_post;
                  @endphp
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;" class="text-danger">Total</span>
                    <span class="badge badge-danger badge-pill">
                        <span style="font-size: 20px;">{{ number_format($total,2) }}</span>
                    </span>
                  </li>
              </ul>
        </div>

        <div class="col-sm-6">
            <div class="widget-box transparent">
                <div class="widget-header widget-header-flat">
                    <h4 class="widget-title lighter">
                        <span style="font-size: 20px; margin-left: 100px;" class="text-success">Flexiload Transaction (Last 7 Days)</span>
                    </h4>

                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <table class="table table-striped table-bordered table-hover" id="flexi_list">
                            <thead>
                              <tr>
                                <th rowspan="3">SL</th>
                                <th rowspan="3">Company Name</th>
                                <th colspan="2">Bill Amount</th>
                                <th colspan="2">Submission Summary</th>
                              </tr>
                              <tr>
                                <th colspan="1">Success</th>
                                <th colspan="1">Pending</th>
                                <th colspan="1">Success</th>
                                <th colspan="1">Pending</th>
                              </tr>
                            </thead>
                            <tbody>

                                @php
                                function formatNumber($number) {
                                    if ($number >= 1000) {
                                        return number_format($number);
                                    }
                                    return $number;
                                }
                                @endphp
                                
                                @php($sl = 0)
                                @php($totalAmountSum = 0)
                                @php($totalNumberSum = 0)
                                @php($totalPenSum = 0)
                                @php($penAmount = 0)
                        
                                @foreach ($campaignPriceSum as $userId => $priceSum)
                                    @php($totalAmountSum += $priceSum)
                                    @php($totalNumberSum += $numberSum->sum())
                                    @php($totalPenSum += $numPendSum->sum())
                                    @php($penAmount += $numPendAmount->sum())
                        
                                    <tr>
                                        <td>{{ ++$sl }}</td>
                                        <td style="text-transform: uppercase;">{{ $userNames->get($userId) }}</td>
                                        <td class="hidden-480 text-right">{{ formatNumber($priceSum) }}</td>
                                        <td class="hidden-480 text-right">{{ formatNumber($numPendAmount->get($userId, 0)) }}</td>
                                        <td class="hidden-480 text-right">{{ formatNumber($numberSum->get($userId, 0)) }}</td>
                                        <td class="hidden-480 text-right">{{ formatNumber($numPendSum->get($userId, 0)) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td class="text-right"><strong>{{ formatNumber($totalAmountSum) }}</strong></td>
                                    <td class="text-right">{{ formatNumber($penAmount) }}</td>
                                    <td class="text-right">{{ formatNumber($totalNumberSum) }}</td>
                                    <td class="text-right">{{ formatNumber($totalPenSum) }}</td>
                                </tr>
                            </tbody>
                          </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

@endsection


@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#flexi_list').DataTable();
        });
        
    </script>

@endsection