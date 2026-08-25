@extends('reseller.master')

@section('sms_activity_menu_monitoring', 'active')
@section('user_menu_class', 'open')

@section('page_location')
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="{{ route('reseller.index') }}">Dashboard</a>
    </li>
    <li class="active">SMS Monitoring Dashboard</li>
</ul>
@endsection

@section('page_header')
<h1>
    <i class="fa fa-calendar"></i>
    SMS Monitoring Dashboard
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Daily SMS Activity & Analytics
    </small>
</h1>
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-12">
        <!-- Calendar Navigation -->
        <div class="widget-box">
            <div class="widget-header widget-header-flat">
                <h4 class="widget-title lighter">
                    <i class="ace-icon fa fa-calendar"></i>
                    Calendar Navigation - {{ $currentMonth }}
                </h4>
                <div class="widget-toolbar">
                    <a href="{{ route('reseller.user.smsMonitoringDashboard', ['date' => $prevMonth]) }}" class="btn btn-sm btn-default">
                        <i class="ace-icon fa fa-chevron-left"></i> Prev Month
                    </a>
                    <a href="{{ route('reseller.user.smsMonitoringDashboard', ['date' => $nextMonth]) }}" class="btn btn-sm btn-default">
                        Next Month <i class="ace-icon fa fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            <div class="widget-body">
                <div class="widget-main">
                    <!-- Calendar Days -->
                    <div class="row" style="margin: -2px;">
                        @foreach($daysInMonth as $day)
                        <div class="col-xs-6 col-sm-4 col-md-2 col-lg-1" style="padding: 2px; margin-bottom: 5px;">
                            <a href="{{ route('reseller.user.smsMonitoringDashboard', ['date' => $day['date']]) }}" 
                               class="btn btn-app btn-sm btn-no-radius 
                                      {{ $day['is_selected'] ? 'btn-pink' : ($day['total_sms'] > 0 ? 'btn-success' : 'btn-default') }} 
                                      {{ $day['is_today'] ? 'today-highlight' : '' }}"
                               style="display: block; min-width: auto; padding: 3px; height: 65px; text-align: center;">
                                <span style="display: block; font-size: 14px; font-weight: bold;">{{ $day['day'] }}</span>
                                @if($day['total_sms'] > 0)
                                <span style="display: block; background: #d15b47; color: white; border-radius: 2px; padding: 1px 2px; font-size: 9px; margin: 1px 0;">
                                    {{ $day['total_sms'] }} SMS
                                </span>
                                <span style="display: block; background: #4b6ea8; color: white; border-radius: 2px; padding: 1px 2px; font-size: 9px; margin: 1px 0;">
                                    {{ $day['active_users'] }} Users
                                </span>
                                @else
                                <span style="display: block; color: #999; font-size: 9px; margin-top: 5px;">
                                    No SMS
                                </span>
                                @endif
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats for Selected Date -->
        <div class="row">
            <div class="col-sm-3">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title smaller">Total SMS</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="clearfix">
                                <span class="pull-left" style="font-size: 18px; font-weight: bold;">{{ number_format($totalSms) }}</span>
                                <span class="pull-right">
                                    <i class="ace-icon fa fa-comments fa-2x red"></i>
                                </span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar progress-bar-danger" style="width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title smaller">Active Users</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="clearfix">
                                <span class="pull-left" style="font-size: 18px; font-weight: bold;">{{ $activeUsers }} / {{ $allUsers }}</span>
                                <span class="pull-right">
                                    <i class="ace-icon fa fa-users fa-2x green"></i>
                                </span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar progress-bar-success" style="width: {{ $allUsers > 0 ? ($activeUsers/$allUsers)*100 : 0 }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title smaller">Inactive Users</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="clearfix">
                                <span class="pull-left" style="font-size: 18px; font-weight: bold;">{{ $inactiveUsers }} / {{ $allUsers }}</span>
                                <span class="pull-right">
                                    <i class="ace-icon fa fa-user-times fa-2x orange"></i>
                                </span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar progress-bar-warning" style="width: {{ $allUsers > 0 ? ($inactiveUsers/$allUsers)*100 : 0 }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title smaller">Activity Rate</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="clearfix">
                                <span class="pull-left" style="font-size: 18px; font-weight: bold;">{{ $allUsers > 0 ? number_format(($activeUsers/$allUsers)*100, 1) : 0 }}%</span>
                                <span class="pull-right">
                                    <i class="ace-icon fa fa-percent fa-2x blue"></i>
                                </span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar progress-bar-info" style="width: {{ $allUsers > 0 ? ($activeUsers/$allUsers)*100 : 0 }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Top Users -->
        <div class="row">
            

            <div class="col-md-12">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title lighter">Top Users on {{ date('M j, Y', strtotime($selectedDate)) }}</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            @if($topUsers->count() > 0)
                            <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th style="width:2%;">SL</th>
            <th style="width:15%;">User</th>
          
            <th style="width:8%;">Phone</th>
              <th style="width:5%;">Count</th>
            <th style="width:5%;">%</th>
            <th style="width:65%;">Ratios</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topUsers as $index => $user)
        <tr>
            <td>{{ $index + 1 }}</td> <!-- Serial number -->

            <td>{{ $user->user->company_name ?? 'Unknown User' }}</td>
            
            @php
                // Percentage for progress bar
                $percentage = $totalSms > 0 ? ($user->sms_count / $totalSms) * 100 : 0;

                // Set progress bar color based on percentage ranges
                if ($percentage >= 70) {
                    $barColor = '#28a745'; // Green
                } elseif ($percentage >= 50) {
                    $barColor = '#28a745'; // Yellow
                } elseif ($percentage >= 20) {
                    $barColor = '#fd7e14'; // Orange
                } else {
                    $barColor = '#dc3545'; // Red
                }

                // Badge color for count
                $badgeColor = $user->sms_count >= 10000 ? 'success' : 'danger';
            @endphp
<td>{{ $user->user->cellphone }}</td>
            <td>
                <span class="badge bg-{{ $badgeColor }}">
                    {{ number_format($user->sms_count) }}
                </span>
            </td>

            
            <td>{{ number_format($percentage, 1) }}%</td>
            <td>
                <div class="progress progress-mini" style="height:19px;">
                    <div class="progress-bar" 
                         style="width: {{ $percentage }}%; 
                                line-height: 15px;
                                background-color: {{ $barColor }};
                                color: #fff;
                                font-weight: bold;">
                        {{ number_format($percentage, 1) }}%
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>



                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="ace-icon fa fa-info-circle"></i>
                                No SMS activity found for this date.
                            </div>
                            @endif
                        </div>
                    </div>
                 
                </div>
                 
            </div>
              <div class="col-md-12">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title lighter">SMS Activity (Last 15 Days)</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <canvas id="smsChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('custom_script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    var ctx = document.getElementById('smsChart').getContext('2d');

    // Calculate maximum SMS for percentage-based coloring
    var smsCounts = [@foreach($chartData as $data) {{ $data['sms_count'] }}, @endforeach];
    var maxSms = Math.max(...smsCounts);

    var smsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [@foreach($chartData as $data) '{{ $data['date'] }}', @endforeach],
            datasets: [{
                label: 'SMS Sent',
                data: smsCounts,
                backgroundColor: smsCounts.map(count => {
                    return count >= (maxSms * 0.5) ? 'rgba(0, 128, 0, 0.7)' : 'rgba(75, 192, 192, 0.7)';
                }),
                borderColor: smsCounts.map(count => {
                    return count >= (maxSms * 0.5) ? 'rgba(0, 128, 0, 1)' : 'rgba(75, 192, 192, 1)';
                }),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Number of SMS' } },
                x: { title: { display: true, text: 'Date' } }
            }
        }
    });

    // Highlight today's date (optional)
    $('.today-highlight').addClass('btn-danger').removeClass('btn-default');
});
</script>

<style>
.today-highlight { border: 2px solid #d15b47 !important; font-weight: bold; }
.progress-mini { height: 10px; margin-bottom: 0; }
.widget-box { margin-bottom: 15px; }
</style>
@endsection

