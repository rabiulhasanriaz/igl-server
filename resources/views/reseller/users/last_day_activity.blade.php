@extends('reseller.master')

@section('sms_activity_menu', 'active')
@section('user_menu_class', 'open')

@section('page_location')
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="{{ route('reseller.index') }}">Dashboard</a>
    </li>
    <li class="active">Daily SMS Activity</li>
</ul>
@endsection

@section('page_header')
<h1>
    <i class="fa fa-comments"></i>
    Daily SMS Activity
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Users who sent/didn't send SMS today
    </small>
</h1>
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-12">
        <div class="widget-box">
            <div class="widget-header">
                <h4 class="widget-title">
                    <i class="fa fa-filter"></i> Filter Users
                </h4>
                <div class="widget-toolbar">
                    <div class="form-inline">
                        <label>Show</label>
                        <select class="form-control input-sm" id="entries-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label>entries</label>
                    </div>
                </div>
            </div>
            <div class="widget-body">
                <div class="widget-main">
                    <div class="btn-group btn-group-justified" id="smsFilterTabs">
                        <a href="#withSms" data-toggle="tab" class="btn btn-sm btn-success active">
                            <i class="fa fa-check-circle"></i>
                            Sent SMS Today ({{ $withSmsCount }})
                        </a>
                        <a href="#withoutSms" data-toggle="tab" class="btn btn-sm btn-danger">
                            <i class="fa fa-exclamation-circle"></i>
                            No SMS Today ({{ $withoutSmsCount }})
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content no-border">
            <!-- Users With SMS Tab -->
            <div class="tab-pane active" id="withSms">
                <div class="table-responsive">
                    <table id="withSmsTable" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company</th>
                                <th>Mobile</th>
                                <th>Balance</th>
                                <th>Last SMS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usersWithSms as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->company_name }}</td>
                                <td>{{ $user->cellphone }}</td>
                                <td class="text-right">{{ number_format($user->balance, 3) }}</td>
                                <td>{{ $user->last_sms_date ? $user->last_sms_date->format('Y-m-d H:i') : 'Never' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No users sent SMS today</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Users Without SMS Tab -->
            <div class="tab-pane" id="withoutSms">
                <div class="table-responsive">
                    <table id="withoutSmsTable" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company</th>
                                <th>Mobile</th>
                                <th>Balance</th>
                                <th>Last SMS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usersWithoutSms as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->company_name }}</td>
                                <td>{{ $user->cellphone }}</td>
                                <td class="text-right">{{ number_format($user->balance, 3) }}</td>
                                <td>{{ $user->last_sms_date ? $user->last_sms_date->format('Y-m-d H:i') : 'Never' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">All users sent SMS today</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            	<div class="row">
		    <div class="col-sm-12">
			<div class="widget-box">
			    <div class="widget-header">
				<h4 class="widget-title">
				    Monthly SMS Count - <span id="current-month">{{ \Carbon\Carbon::now()->format('F Y') }}</span>
				    <small class="pull-right">
				        Total: <span id="total-sms-count" class="badge badge-primary">0</span>
				    </small>
				    <div class="widget-toolbar">
				        <a href="#" class="refresh-monthly-sms" title="Refresh">
				            <i class="ace-icon fa fa-refresh"></i>
				        </a>
				    </div>
				</h4>
			    </div>
			    <div class="widget-body">
				<div class="widget-main no-padding">
				    <div id="monthly-sms-chart" style="height: 350px; padding: 10px;"></div>
				</div>
			    </div>
			</div>
		    </div>
		</div>
        </div>
    </div>
</div>
@endsection

@section('custom_style')
<link href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/datatable/rowReorder.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<style>
    /* Consistent table styling for both tabs */
    .table {
        width: 100% !important;
        margin-bottom: 0;
    }
    
    .table thead th {
        background-color: #f5f5f5;
        border-bottom: 2px solid #ddd;
        font-weight: bold;
        vertical-align: middle;
    }
    
    .table tbody tr td {
        vertical-align: middle;
    }
    
    .table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    .table tbody tr:hover {
        background-color: #f1f1f1;
    }
    
    /* Tab button styling */
    #smsFilterTabs .btn {
        border-radius: 0;
        padding: 8px 12px;
    }
    
    #smsFilterTabs .btn.active {
        position: relative;
        z-index: 1;
        box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
        font-weight: bold;
    }
    
    /* Widget box styling */
    .widget-box {
        border: 1px solid #dce4ec;
        box-shadow: 0 1px 1px rgba(0,0,0,0.05);
    }
    
    .widget-header {
        background-color: #f5f5f5;
        border-bottom: 1px solid #dce4ec;
        padding: 10px 15px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 767px) {
        .widget-toolbar {
            margin-top: 10px;
            float: none !important;
        }
        
        #smsFilterTabs .btn {
            padding: 6px 8px;
            font-size: 12px;
        }
    }
</style>
@endsection

@section('custom_script')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
   <!-- Flot Charts from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.time.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.resize.min.js"></script>
  
<script>

$(document).ready(function() {
    // Initialize chart
    function loadMonthlyData() {
        $('#monthly-sms-chart').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading data...</div>');
        
        $.get("{{ route('reseller.monthly-sms-count-reseller') }}", function(response) {
            if (response.success) {
                renderChart(response.daily_data, response.total_count);
            } else {
                showError(response.message || 'Failed to load data');
            }
        }).fail(function() {
            showError('Server error');
        });
    }

    // Render Flot chart
    function renderChart(dailyData, totalCount) {
        const chartData = dailyData.map((day, index) => [index, day.count]);
        const ticks = dailyData.map((day, index) => [index, day.date]);
        
        $.plot("#monthly-sms-chart", [{
            data: chartData,
            color: "#4E9CB5",
            bars: {
                show: true,
                barWidth: 0.6,
                align: "center",
                fill: 0.8
            }
        }], {
            xaxis: {
                ticks: ticks,
                tickLength: 0,
                rotateTicks: 45
            },
            yaxis: {
                min: 0,
                tickFormatter: function(val) {
                    return val.toLocaleString();
                }
            },
            grid: {
                hoverable: true,
                borderWidth: 0,
                backgroundColor: "#FFF"
            }
        });

        // Update total count
        $('#total-sms-count').text(totalCount.toLocaleString());
        
        // Tooltip
        $("#monthly-sms-chart").bind("plothover", function(event, pos, item) {
            if (item) {
                const day = dailyData[item.dataIndex];
                showTooltip(item.pageX, item.pageY, 
                    `<strong>${day.date}</strong><br>${day.count.toLocaleString()} SMS`);
            } else {
                $("#tooltip").remove();
            }
        });
    }

    function showTooltip(x, y, content) {
        $("#tooltip").remove();
        $('<div id="tooltip">' + content + '</div>').css({
            position: 'absolute',
            top: y + 10,
            left: x + 10,
            padding: '8px',
            background: 'rgba(255,255,255,0.95)',
            border: '1px solid #ddd',
            'border-radius': '4px',
            'box-shadow': '0 2px 8px rgba(0,0,0,0.1)'
        }).appendTo("body").fadeIn(200);
    }

    function showError(message) {
        $('#monthly-sms-chart').html(
            `<div class="text-center py-4 text-danger">
                <i class="fa fa-exclamation-triangle"></i> ${message}
                <button class="btn btn-sm btn-light mt-2" onclick="loadMonthlyData()">
                    <i class="fa fa-refresh"></i> Retry
                </button>
            </div>`
        );
    }

    // Initial load
    loadMonthlyData();
    
    // Refresh button
    $('.refresh-monthly-sms').click(function(e) {
        e.preventDefault();
        loadMonthlyData();
    });

    // Make function available for retry button
    window.loadMonthlyData = loadMonthlyData;
});
</script>


@endsection
