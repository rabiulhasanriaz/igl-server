@extends('admin.master')

@section('dashboard_menu_class','active')

@section('page_location')
<div style="margin-top: -10px; margin-bottom: 15px;">
    <ul class="breadcrumb" style="background: transparent; padding-left: 0; margin-bottom: 10px;">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}" style="color: #438EB9;">Home</a>
        </li>
        <li class="active">Dashboard Overview</li>
    </ul>
</div>
@endsection

@section('main_content')
<style>
    /* Global Layout & Spacing */
    .row { margin-left: -8px; margin-right: -8px; }
    .col-md-3, .col-md-4, .col-md-6, .col-md-8, .col-sm-6 { padding-left: 8px; padding-right: 8px; }
    
    /* 1. TOP PENDING CARDS */
    .pending-container {
        background: #fff; border: 1px solid #e6e9ed; border-radius: 8px; padding: 18px 22px;
        margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between;
        position: relative; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .pending-container::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; }
    .line-red::before { background: #d15b47; }
    .line-orange::before { background: #f0ad4e; }
    
    .pending-label { font-size: 11px; text-transform: uppercase; color: #777; font-weight: 700; letter-spacing: 1px; }
    .pending-value { font-size: 32px; font-weight: 800; margin-top: 2px; letter-spacing: -1px; }
    .pending-sub { font-size: 12px; color: #999; margin-top: 5px; }
    
    .immediate-value { color: #d15b47 !important; }
    .scheduled-value { color: #f0ad4e !important; }
    
    #immediateCount, #immediateCost { color: #d15b47 !important; font-weight: 800; }
    #scheduledCount, #scheduledCost { color: #f0ad4e !important; font-weight: 800; }

    /* 2. STAT BOXES */
    .stat-box { background: #fff; border: 1px solid #dce4ec; border-radius: 4px; padding: 12px 15px; margin-bottom: 15px; display: flex; align-items: center; }
    .stat-icon { width: 40px; height: 40px; border-radius: 4px; background: #f8f9fb; color: #34495e; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px; }
    .stat-info small { color: #7f8c8d; text-transform: uppercase; font-size: 10px; font-weight: bold; display: block; }
    .stat-info h2 { margin: 0; font-size: 18px; font-weight: 700; color: #2c3e50; }

    /* 3. HIGHLIGHTED SECTION */
    .highlight-card { border: 2px solid #438EB9 !important; box-shadow: 0 8px 15px rgba(67, 142, 185, 0.15); background: #fff; }
    .highlight-card .card-header { background: #438EB9 !important; color: #fff !important; border-bottom: none; font-weight: 700; padding: 12px; }
    .table-premium { margin-bottom: 0; width: 100%; }
    .table-premium thead th { background: #f4f7f6; color: #438EB9; font-size: 10px; text-transform: uppercase; padding: 10px !important; border-bottom: 1px solid #dce4ec !important; }
    .table-premium td { padding: 10px !important; border-top: 1px solid #f1f1f1 !important; vertical-align: middle; }

    /* 4. GATEWAY HEALTH */
    .op-icon { width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: #f0f3f5; margin-right: 10px; font-size: 12px; }
    .op-gp { color: #00a9e0; border: 1px solid #00a9e0; } 
    .op-robi { color: #e31e24; border: 1px solid #e31e24; } 
    .op-bl { color: #ff6700; border: 1px solid #ff6700; } 

    /* 5. TOOLTIP STYLE */
    #flot-tooltip { position: absolute; display: none; padding: 8px 12px; background: #2c3e50; color: #fff; font-size: 12px; border-radius: 4px; z-index: 1000; box-shadow: 0 2px 8px rgba(0,0,0,0.3); line-height: 1.4; }

    .main-card { background: #fff; border: 1px solid #dce4ec; border-radius: 4px; margin-bottom: 15px; overflow: hidden; }
    .card-header-std { padding: 10px 15px; background: #fcfdfe; border-bottom: 1px solid #dce4ec; font-weight: 700; color: #34495e; display: flex; justify-content: space-between; align-items: center; }
</style>

{{-- Queue Statistics - 4 Cards for Immediate and Scheduled --}}
<div class="row">
    {{-- Immediate Messages Count --}}
    <div class="col-md-3 col-sm-6">
        <div class="pending-container line-red">
            <div>
                <div class="pending-label"><i class="fa fa-bolt"></i> IMMEDIATE QUEUE</div>
                <div class="pending-value immediate-value"><span id="immediateCount">0</span></div>
                <div class="pending-sub">Ready to send now</div>
            </div>
            <i class="fa fa-send" style="font-size:28px; color:#f1f3f5;"></i>
        </div>
    </div>
    
    {{-- Immediate Estimated Cost --}}
    <div class="col-md-3 col-sm-6">
        <div class="pending-container line-red">
            <div>
                <div class="pending-label"><i class="fa fa-money"></i> IMMEDIATE COST</div>
                <div class="pending-value immediate-value"><span id="immediateCost">0.00</span></div>
                <div class="pending-sub">Estimated</div>
            </div>
            <i class="fa fa-calculator" style="font-size:28px; color:#f1f3f5;"></i>
        </div>
    </div>
    
    {{-- Scheduled Messages Count --}}
    <div class="col-md-3 col-sm-6">
        <div class="pending-container line-orange">
            <div>
                <div class="pending-label"><i class="fa fa-calendar"></i> SCHEDULED QUEUE</div>
                <div class="pending-value scheduled-value"><span id="scheduledCount">0</span></div>
                <div class="pending-sub">Future delivery</div>
            </div>
            <i class="fa fa-clock-o" style="font-size:28px; color:#f1f3f5;"></i>
        </div>
    </div>
    
    {{-- Scheduled Estimated Cost --}}
    <div class="col-md-3 col-sm-6">
        <div class="pending-container line-orange">
            <div>
                <div class="pending-label"><i class="fa fa-credit-card"></i> SCHEDULED COST</div>
                <div class="pending-value scheduled-value"><span id="scheduledCost">0.00</span></div>
                <div class="pending-sub">Estimated</div>
            </div>
            <i class="fa fa-calculator" style="font-size:28px; color:#f1f3f5;"></i>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="stat-box">
            <div class="stat-icon"><i class="fa fa-users" style="color:#438EB9"></i></div>
            <div class="stat-info"><small>Active Users</small><h2>{{ $data['active_user'] }}</h2></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box">
            <div class="stat-icon" style="color: #e74c3c;"><i class="fa fa-lock"></i></div>
            <div class="stat-info"><small>Suspended</small><h2>{{ $data['suspend_user'] }}</h2></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box">
            <div class="stat-icon"><i class="fa fa-paper-plane-o" style="color:#438EB9"></i></div>
            <div class="stat-info"><small>{{ \Carbon\Carbon::now()->format('M') }} Volume</small><h2>{{ $data['last_month_sms'] }}</h2></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box" style="border-bottom: 2px solid #2c3e50;">
            <div class="stat-icon" style="background: #2c3e50; color: #fff;"><i class="fa fa-database"></i></div>
            <div class="stat-info"><small>Lifetime Total</small><h2>{{ $data['total_sms'] }}</h2></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="main-card">
            <div class="card-header-std">
                <span><i class="fa fa-bar-chart"></i> Monthly Traffic Trend</span>
                <span id="total-sms-count" class="badge" style="background:#438EB9">0</span>
            </div>
            <div style="padding: 15px;"><div id="monthly-sms-chart" style="height: 280px;"></div></div>
        </div>
        <div class="row">
            <div class="col-sm-6"><div class="main-card"><div class="card-header-std">Daily Frequency</div><div style="padding: 10px;"><div id="daily-sms-chart" style="height: 180px;"></div></div></div></div>
            <div class="col-sm-6"><div class="main-card"><div class="card-header-std">Segment Analysis</div><div style="padding: 10px;"><div id="today-sms-barchart" style="height: 180px;"></div></div></div></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="main-card highlight-card">
            <div class="card-header">
                <span><i class="fa fa-history"></i> YEAR-TO-DATE LOG</span>
            </div>
            <table class="table table-premium">
                <thead><tr><th>Month-Year</th><th class="text-right">Volume</th></tr></thead>
                <tbody>
                    @foreach($data['monthly_sms'] as $monthly_sms)
                    <tr>
                        <td style="font-weight: 600; color: #34495e;"><i class="fa fa-calendar-o" style="color:#438EB9"></i> {{ date("M Y", mktime(0,0,0,$monthly_sms->month,1,$monthly_sms->year)) }}</td>
                        <td class="text-right"><span class="badge" style="background:#438EB9">{{ number_format($monthly_sms->total_sms) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="main-card" style="margin-top: 15px;">
            <div class="card-header-std"><i class="fa fa-signal"></i> Gateway Health</div>
            <table class="table table-condensed mb-0" style="font-size: 13px;">
                <tbody>
                    @foreach($data['ans_balances'] as $opName => $balance)
                    @php 
                        $name = strtolower($opName);
                        $iconClass = 'fa-globe'; $colorClass = '';
                        if(strpos($name, 'gp') !== false) { $iconClass = 'fa-mobile'; $colorClass = 'op-gp'; }
                        elseif(strpos($name, 'robi') !== false) { $iconClass = 'fa-cube'; $colorClass = 'op-robi'; }
                        elseif(strpos($name, 'banglalink') !== false) { $iconClass = 'fa-bolt'; $colorClass = 'op-bl'; }
                    @endphp
                    <tr>
                        <td style="padding: 10px 12px;">
                            <span class="op-icon {{ $colorClass }}"><i class="fa {{ $iconClass }}"></i></span>
                            <strong>{{ $opName }}</strong>
                        </td>
                        <td class="text-right font-weight-bold" style="color:#438EB9">{{ $balance['availableBalance'] ?? '0.00' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('custom_script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.time.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.resize.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.stack.min.js"></script>

<script>
$(document).ready(function() {
    const brandBlue = "#438EB9", slate = "#2c3e50";
    let monthlyRawData = []; // To store full data for hover tooltips

    // Initialize Tooltip
    $("<div id='flot-tooltip'></div>").appendTo("body");
    function showTooltip(x, y, contents) {
        $("#flot-tooltip").html(contents).css({top: y + 5, left: x + 5}).fadeIn(200);
    }

    // Combined Hover Event Listener
    $(".row div[id$='chart']").bind("plothover", function (event, pos, item) {
        if (item) {
            let label = item.series.label || "Count";
            let val = item.datapoint[1].toFixed(0);
            let content = "";

            // Check if it's the monthly chart to show the date
            if ($(this).attr('id') === "monthly-sms-chart" && monthlyRawData[item.dataIndex]) {
                let dayData = monthlyRawData[item.dataIndex];
                content = `<strong>${dayData.date}</strong><br>` +
                          `<span style="color:#438EB9">Non-Masking: ${dayData.non_masking}</span><br>` +
                          `<span style="color:#888">Masking: ${dayData.masking}</span><br>` +
                          `<strong>Total: ${dayData.total}</strong>`;
            } else {
                content = `<strong>${label}</strong>: ${val}`;
            }

            showTooltip(item.pageX, item.pageY, content);
        } else { 
            $("#flot-tooltip").hide(); 
        }
    });

    function initCharts() {
        // Monthly Chart
        $.get("{{ route('admin.monthly-sms-count') }}", function(res) {
            if (res.success) {
                monthlyRawData = res.daily_data;
                const nm = res.daily_data.map((d, i) => [i, d.non_masking]);
                const m = res.daily_data.map((d, i) => [i, d.masking]);
                const ticks = res.daily_data.map((d, i) => [i, d.date.split('-')[2]]); // Shows day of month

                $.plot("#monthly-sms-chart", [
                    { data: nm, label: "Non-Masking", color: brandBlue, bars: { show: true, barWidth: 0.5, fill: 0.8 } },
                    { data: m, label: "Masking", color: "#dcdde1", bars: { show: true, barWidth: 0.5, fill: 1 } }
                ], { 
                    series: { stack: true }, 
                    xaxis: { ticks: ticks, tickLength: 0 }, 
                    grid: { borderWidth: 0, hoverable: true }, 
                    legend: { position: "nw", noColumns: 2 } 
                });
                $('#total-sms-count').text(res.total_count.toLocaleString());
            }
        });

        // Daily Chart
        $.get("{{ route('admin.daily-sms-data') }}", function(data) {
            var d1 = data.map(item => {
                var p = item.date.split('-');
                return [new Date(p[0], p[1]-1, p[2]).getTime(), parseInt(item.sms_count)];
            });
            $.plot("#daily-sms-chart", [{ data: d1, label: "Total SMS", color: brandBlue, lines: { show: true, fill: true, fillColor: "rgba(67, 142, 185, 0.05)" }}], { xaxis: { mode: "time", tickLength: 0 }, grid: { borderWidth: 0, hoverable: true } });
        });

        // Segment Chart
        $.get("{{ route('admin.today-sms-counts') }}", function(res) {
            var d2 = res.data.map((item, idx) => [idx, item.message_count]);
            $.plot("#today-sms-barchart", [{ data: d2, label: "Segments", color: slate, bars: { show: true, barWidth: 0.4, align: "center", fill: 0.9 }}], { xaxis: { ticks: res.data.map((item, idx) => [idx, item.sms_segments]), tickLength: 0 }, grid: { borderWidth: 0, hoverable: true } });
        });
    }

    initCharts();

    // Live Data Updates - Separate for Immediate and Scheduled
    function refreshLiveStats() {
        // Immediate Queue Count & Cost (using existing SmsCamPending)
        $.ajax({ url: "{{ route('admin.data-count') }}", success: d => $('#immediateCount').html(d) });
        $.ajax({ url: "{{ route('admin.data-cost') }}", success: d => $('#immediateCost').html(d) });
        
        // Scheduled Queue Count & Cost (need to add these routes)
        $.ajax({ url: "{{ route('admin.scheduled-count') }}", success: d => $('#scheduledCount').html(d) });
        $.ajax({ url: "{{ route('admin.scheduled-cost') }}", success: d => $('#scheduledCost').html(d) });
    }
    
    setInterval(refreshLiveStats, 10000);
    refreshLiveStats();
});
</script>
@endsection
