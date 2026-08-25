

<?php $__env->startSection('sms_monitoring', 'active'); ?>
<?php $__env->startSection('user_menu_class', 'open'); ?>

<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="">Dashboard</a>
    </li>
    <li class="active">SMS Monitoring Dashboard</li>
</ul>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_header'); ?>
<h1>
    <i class="fa fa-calendar"></i>
    SMS Monitoring Dashboard
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Real-time SMS Activity & Analytics
    </small>
</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
<div class="row">
    <div class="col-md-12">
        <!-- Real-time Status Bar -->
        <div class="widget-box">
            <div class="widget-body">
                <div class="widget-main" style="padding: 10px 15px; background: #f8f9fa; border-radius: 4px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                        <div>
                            <span class="live-dot"></span>
                            <span style="font-weight: bold; color: #28a745;">LIVE</span>
                            <span style="color: #888; font-size: 12px; margin-left: 10px;">
                                Real-time updates via Server-Sent Events
                            </span>
                        </div>
                        <div style="font-size: 12px; color: #888;">
                            <span>Last update: <span id="last-update-time">Just now</span></span>
                            <span style="margin-left: 15px;">
                                <i class="fa fa-signal" id="status-icon" style="color: #28a745;"></i>
                                <span id="connection-status" style="color: #28a745;">● Connected</span>
                            </span>
                            <span style="margin-left: 15px;">
                                Updates: <span id="update-count">0</span>
                            </span>
                            <button onclick="reconnectSSE()" class="btn btn-xs btn-info" style="margin-left: 10px;">
                                <i class="fa fa-refresh"></i> Reconnect
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Navigation -->
        <div class="widget-box">
            <div class="widget-header widget-header-flat">
                <h4 class="widget-title lighter">
                    <i class="ace-icon fa fa-calendar"></i>
                    Calendar Navigation - <?php echo e($currentMonth); ?>

                </h4>
                <div class="widget-toolbar">
                    <a href="<?php echo e(route('admin.reseller.sms_monitoring', ['date' => $prevMonth])); ?>" class="btn btn-sm btn-default">
                        <i class="ace-icon fa fa-chevron-left"></i> Prev Month
                    </a>
                    <a href="<?php echo e(route('admin.reseller.sms_monitoring', ['date' => $nextMonth])); ?>" class="btn btn-sm btn-default">
                        Next Month <i class="ace-icon fa fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            <div class="widget-body">
                <div class="widget-main">
                    <!-- Calendar Days -->
                    <div class="row" style="margin: -2px;">
                        <?php $__currentLoopData = $daysInMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-xs-6 col-sm-4 col-md-2 col-lg-1" style="padding: 2px; margin-bottom: 5px;">
                            <a href="<?php echo e(route('admin.reseller.sms_monitoring', ['date' => $day['date']])); ?>" 
                               class="btn btn-app btn-sm btn-no-radius 
                                      <?php echo e($day['is_selected'] ? 'btn-pink' : ($day['total_sms'] > 0 ? 'btn-success' : 'btn-default')); ?> 
                                      <?php echo e($day['is_today'] ? 'today-highlight' : ''); ?>"
                               style="display: block; min-width: auto; padding: 3px; height: 65px; text-align: center;">
                                <span style="display: block; font-size: 14px; font-weight: bold;"><?php echo e($day['day']); ?></span>
                                <?php if($day['total_sms'] > 0): ?>
                                <span style="display: block; background: #d15b47; color: white; border-radius: 2px; padding: 1px 2px; font-size: 9px; margin: 1px 0;">
                                    <?php echo e($day['total_sms']); ?> SMS
                                </span>
                                <span style="display: block; background: #4b6ea8; color: white; border-radius: 2px; padding: 1px 2px; font-size: 9px; margin: 1px 0;">
                                    <?php echo e($day['active_users']); ?> Users
                                </span>
                                <?php else: ?>
                                <span style="display: block; color: #999; font-size: 9px; margin-top: 5px;">
                                    No SMS
                                </span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats for Selected Date -->
        <div class="row">
            <!-- Total SMS -->
            <div class="col-sm-2">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title smaller">Total SMS</h4>
                        <span class="trend-indicator">
                            <span id="total-sms-trend-text" style="font-size: 11px; color: #888;"></span>
                        </span>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="clearfix">
                                <span class="pull-left" style="font-size: 18px; font-weight: bold;" id="total-sms"><?php echo e(number_format($totalSms)); ?></span>
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

            <!-- Masking SMS -->
            <div class="col-sm-2">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title smaller">Masking SMS</h4>
                        <span class="trend-indicator">
                            <span id="masking-trend-text" style="font-size: 11px; color: #888;"></span>
                        </span>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="clearfix">
                                <span class="pull-left" style="font-size: 18px; font-weight: bold; color: #2c9c5c;" id="masking-count"><?php echo e(number_format($maskingCount)); ?></span>
                                <span class="pull-right">
                                    <i class="ace-icon fa fa-shield fa-2x" style="color: #2c9c5c;"></i>
                                </span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar" style="width: <?php echo e($totalSms > 0 ? ($maskingCount/$totalSms)*100 : 0); ?>%; background-color: #2c9c5c;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Non-Masking SMS -->
            <div class="col-sm-2">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title smaller">Non-Masking SMS</h4>
                        <span class="trend-indicator">
                            <span id="non-masking-trend-text" style="font-size: 11px; color: #888;"></span>
                        </span>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="clearfix">
                                <span class="pull-left" style="font-size: 18px; font-weight: bold; color: #ff8c42;" id="non-masking-count"><?php echo e(number_format($nonMaskingCount)); ?></span>
                                <span class="pull-right">
                                    <i class="ace-icon fa fa-eye-slash fa-2x" style="color: #ff8c42;"></i>
                                </span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar" style="width: <?php echo e($totalSms > 0 ? ($nonMaskingCount/$totalSms)*100 : 0); ?>%; background-color: #ff8c42;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="col-sm-2">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title smaller">Active Users</h4>
                        <span class="trend-indicator">
                            <span id="active-users-trend-text" style="font-size: 11px; color: #888;"></span>
                        </span>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="clearfix">
                                <span class="pull-left" style="font-size: 18px; font-weight: bold;" id="active-users"><?php echo e($activeUsers); ?> / <?php echo e($allUsers); ?></span>
                                <span class="pull-right">
                                    <i class="ace-icon fa fa-users fa-2x green"></i>
                                </span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar progress-bar-success" id="active-users-progress" style="width: <?php echo e($allUsers > 0 ? ($activeUsers/$allUsers)*100 : 0); ?>%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inactive Users -->
            <div class="col-sm-2">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title smaller">Inactive Users</h4>
                        <span class="trend-indicator">
                            <span id="inactive-users-trend-text" style="font-size: 11px; color: #888;"></span>
                        </span>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="clearfix">
                                <span class="pull-left" style="font-size: 18px; font-weight: bold;" id="inactive-users"><?php echo e($inactiveUsers); ?> / <?php echo e($allUsers); ?></span>
                                <span class="pull-right">
                                    <i class="ace-icon fa fa-user-times fa-2x orange"></i>
                                </span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar progress-bar-warning" id="inactive-users-progress" style="width: <?php echo e($allUsers > 0 ? ($inactiveUsers/$allUsers)*100 : 0); ?>%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Rate -->
            <div class="col-sm-2">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title smaller">Activity Rate</h4>
                        <span class="trend-indicator">
                            <span id="activity-rate-trend-text" style="font-size: 11px; color: #888;"></span>
                        </span>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="clearfix">
                                <span class="pull-left" style="font-size: 18px; font-weight: bold;" id="activity-rate"><?php echo e($allUsers > 0 ? number_format(($activeUsers/$allUsers)*100, 1) : 0); ?>%</span>
                                <span class="pull-right">
                                    <i class="ace-icon fa fa-percent fa-2x blue"></i>
                                </span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar progress-bar-info" id="activity-rate-progress" style="width: <?php echo e($allUsers > 0 ? ($activeUsers/$allUsers)*100 : 0); ?>%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Users -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title lighter">
                            Top Users on <?php echo e(date('M j, Y', strtotime($selectedDate))); ?>

                            <small class="text-muted" style="font-size: 12px; margin-left: 10px;">
                                <span id="live-indicator" class="badge" style="background-color: #28a745; color: white;">
                                    <i class="ace-icon fa fa-circle" style="font-size: 8px; animation: pulse 2s infinite;"></i>
                                    Live
                                </span>
                                <span style="font-size: 11px; color: #888;" id="last-update">Updated just now</span>
                            </small>
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div id="loading-users" class="text-center" style="display: none;">
                                <i class="ace-icon fa fa-spinner fa-spin blue fa-2x"></i>
                                <p>Loading user data...</p>
                            </div>
                            <div id="top-users-container">
                                <?php if($topUsers->count() > 0): ?>
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
                                        <tbody id="top-users-body">
                                            <?php $__currentLoopData = $topUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td><?php echo e($user->user->company_name ?? 'Unknown User'); ?></td>
                                                <?php
                                                $percentage = $totalSms > 0 ? ($user->sms_count / $totalSms) * 100 : 0;

                                                if ($percentage >= 10) {
                                                    $barColor = '#28a745';
                                                } elseif ($percentage >= 5) {
                                                    $barColor = '#ffc107';
                                                } elseif ($percentage >= 2) {
                                                    $barColor = '#fd7e14';
                                                } else {
                                                    $barColor = '#d14747';
                                                }

                                                if ($user->sms_count >= 10000) {
                                                    $badgeColor = '#28a745';
                                                } elseif ($user->sms_count >= 5000) {
                                                    $badgeColor = '#ffc107';
                                                } elseif ($user->sms_count >= 1000) {
                                                    $badgeColor = '#fd7e14';
                                                } else {
                                                    $badgeColor = '#d14747';
                                                }
                                                ?>
                                                <td><?php echo e($user->user->cellphone); ?></td>
                                                <td>
                                                    <span class="badge" style="background-color: <?php echo e($badgeColor); ?>; color: #fff;">
                                                        <?php echo e(number_format($user->sms_count)); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <span style="color: <?php echo e($percentage >= 10 ? '#28a745' : ($percentage >= 5 ? '#ffc107' : ($percentage >= 2 ? '#fd7e14' : '#d14747'))); ?>; font-weight: bold;">
                                                        <?php echo e(number_format($percentage, 1)); ?>%
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="progress progress-mini" style="height:19px;">
                                                        <div class="progress-bar" 
                                                             style="width: <?php echo e($percentage); ?>%; 
                                                                    line-height: 15px;
                                                                    background-color: <?php echo e($barColor); ?>;
                                                                    color: #fff;
                                                                    font-weight: bold;">
                                                            <?php echo e(number_format($percentage, 1)); ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="ace-icon fa fa-info-circle"></i>
                                    No SMS activity found for this date.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title lighter">SMS Activity (Last 15 Days)</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div id="loading-chart" class="text-center" style="display: none;">
                                <i class="ace-icon fa fa-spinner fa-spin blue fa-2x"></i>
                                <p>Loading chart data...</p>
                            </div>
                            <div id="chart-container">
                                <canvas id="smsChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function () {
    // Initialize variables
    let chart = null;
    let updateCount = 0;
    let previousStats = {};
    let eventSource = null;
    let reconnectAttempts = 0;
    const maxReconnectAttempts = 10;
    let reconnectTimeout = null;

    // Function to load chart data
    function loadChartData() {
        $('#loading-chart').show();
        
        $.ajax({
            url: "<?php echo e(route('admin.reseller.sms_monitoring.chart_data')); ?>",
            type: "GET",
            success: function (chartData) {
                $('#loading-chart').hide();
                
                const ctx = document.getElementById('smsChart').getContext('2d');
                const smsCounts = chartData.map(item => item.sms_count);
                const maxSms = Math.max(...smsCounts);
                
                if (chart) {
                    chart.destroy();
                }
                
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.map(item => item.date),
                        datasets: [{
                            label: 'SMS Sent',
                            data: smsCounts,
                            backgroundColor: smsCounts.map(count =>
                                count >= (maxSms * 0.5) ? 'rgba(0, 128, 0, 0.7)' : 'rgba(75, 192, 192, 0.7)'
                            ),
                            borderColor: smsCounts.map(count =>
                                count >= (maxSms * 0.5) ? 'rgba(0, 128, 0, 1)' : 'rgba(75, 192, 192, 1)'
                            ),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                title: { 
                                    display: true, 
                                    text: 'Number of SMS' 
                                } 
                            },
                            x: { 
                                title: { 
                                    display: true, 
                                    text: 'Date' 
                                } 
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            },
            error: function () {
                $('#loading-chart').hide();
                console.error('Failed to load chart data');
            }
        });
    }

    // Function to update stats with animation
    function updateStatsWithAnimation(stats) {
        // Animate number changes
        animateNumber('#total-sms', stats.totalSms);
        animateNumber('#masking-count', stats.maskingCount);
        animateNumber('#non-masking-count', stats.nonMaskingCount);
        
        const activeUsersText = stats.activeUsers + ' / ' + stats.allUsers;
        animateNumber('#active-users', activeUsersText);
        
        const inactiveUsersText = stats.inactiveUsers + ' / ' + stats.allUsers;
        animateNumber('#inactive-users', inactiveUsersText);
        
        animateNumber('#activity-rate', stats.activityRate + '%');
        
        // Update progress bars
        $('#active-users-progress').css('width', stats.activeProgress + '%');
        $('#inactive-users-progress').css('width', stats.inactiveProgress + '%');
        $('#activity-rate-progress').css('width', stats.activityRate + '%');
        
        // Show trend indicators
        if (Object.keys(previousStats).length > 0) {
            showTrendIndicator('#total-sms', stats.totalSms, previousStats.totalSms);
            showTrendIndicator('#masking-count', stats.maskingCount, previousStats.maskingCount);
            showTrendIndicator('#non-masking-count', stats.nonMaskingCount, previousStats.nonMaskingCount);
            showTrendIndicator('#active-users', activeUsersText, previousStats.activeUsers + ' / ' + previousStats.allUsers);
            showTrendIndicator('#inactive-users', inactiveUsersText, previousStats.inactiveUsers + ' / ' + previousStats.allUsers);
            showTrendIndicator('#activity-rate', stats.activityRate, previousStats.activityRate, true);
        }
        
        previousStats = stats;
    }

    function animateNumber(elementId, newValue) {
        const $element = $(elementId);
        const currentValue = $element.text();
        
        if (typeof newValue === 'string' && (newValue.includes('/') || newValue.includes('%'))) {
            $element.text(newValue);
            return;
        }
        
        const currentNum = parseInt(currentValue.replace(/,/g, '')) || 0;
        const newNum = parseInt(newValue) || 0;
        
        if (currentNum === newNum) return;
        
        const duration = 500;
        const startTime = Date.now();
        const startValue = currentNum;
        const endValue = newNum;
        
        function updateNumber() {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const currentValue = Math.round(startValue + (endValue - startValue) * eased);
            
            $element.text(currentValue.toLocaleString());
            
            if (progress < 1) {
                requestAnimationFrame(updateNumber);
            } else {
                $element.text(endValue.toLocaleString());
            }
        }
        
        updateNumber();
    }

    function showTrendIndicator(elementId, currentValue, previousValue, isPercentage = false) {
        const $trendText = $(elementId + '-text');
        
        if (previousValue === undefined || previousValue === null || previousValue === 0) {
            $trendText.text('New data');
            $trendText.css('color', '#28a745');
            $trendText.css('font-weight', 'bold');
            return;
        }
        
        let change = 0;
        let currentNum, prevNum;
        
        if (isPercentage) {
            currentNum = parseFloat(currentValue);
            prevNum = parseFloat(previousValue);
            if (prevNum > 0) {
                change = ((currentNum - prevNum) / prevNum) * 100;
            }
        } else {
            let currentStr = String(currentValue).replace(/,/g, '');
            let prevStr = String(previousValue).replace(/,/g, '');
            
            if (currentStr.includes('/')) {
                currentStr = currentStr.split('/')[0].trim();
            }
            if (prevStr.includes('/')) {
                prevStr = prevStr.split('/')[0].trim();
            }
            
            currentNum = parseFloat(currentStr) || 0;
            prevNum = parseFloat(prevStr) || 0;
            
            if (prevNum > 0) {
                change = ((currentNum - prevNum) / prevNum) * 100;
            }
        }
        
        const $indicator = $trendText;
        const $widgetHeader = $indicator.closest('.widget-box').find('.widget-header');
        
        $indicator.removeClass('trend-up-green trend-up-yellow trend-down-red trend-neutral');
        
        if (change >= 10) {
            $indicator.html('↑ ' + change.toFixed(1) + '%');
            $indicator.css('color', '#28a745');
            $indicator.css('font-weight', 'bold');
            $indicator.addClass('trend-up-green');
            
            $widgetHeader.css('background-color', '#d4edda');
            setTimeout(() => {
                $widgetHeader.css('background-color', '');
            }, 2000);
        } else if (change >= 0 && change < 10) {
            $indicator.html('↑ ' + change.toFixed(1) + '%');
            $indicator.css('color', '#ffc107');
            $indicator.css('font-weight', 'normal');
            $indicator.addClass('trend-up-yellow');
        } else if (change < 0) {
            $indicator.html('↓ ' + Math.abs(change).toFixed(1) + '%');
            $indicator.css('color', '#dc3545');
            $indicator.css('font-weight', 'normal');
            $indicator.addClass('trend-down-red');
        } else {
            $indicator.text('No change');
            $indicator.css('color', '#888');
            $indicator.css('font-weight', 'normal');
            $indicator.addClass('trend-neutral');
        }
    }

    // SSE Connection for Real-time Updates - FIXED ROUTE NAME
    function connectSSE() {
        if (eventSource) {
            eventSource.close();
            eventSource = null;
        }

        if (reconnectTimeout) {
            clearTimeout(reconnectTimeout);
            reconnectTimeout = null;
        }

        $('#connection-status').html('● Connecting...').css('color', '#ffc107');
        $('#status-icon').css('color', '#ffc107');

        try {
            // USE THE CORRECT ROUTE NAME WITH admin.reseller. PREFIX
            eventSource = new EventSource("<?php echo e(route('admin.reseller.sms_monitoring.stream')); ?>");

            // SMS Update event
            eventSource.addEventListener('sms_update', function(e) {
                try {
                    const data = JSON.parse(e.data);
                    updateStatsWithAnimation(data);
                    
                    // Update top users table
                    if (data.topUsers) {
                        $('#top-users-container').html(data.topUsers);
                    }
                    
                    // Update timestamp
                    const now = new Date();
                    $('#last-update').text('Updated ' + now.toLocaleTimeString());
                    $('#last-update-time').text(now.toLocaleTimeString());
                    
                    updateCount++;
                    $('#update-count').text(updateCount);
                    
                    reconnectAttempts = 0;
                    
                    $('#connection-status').html('● Connected').css('color', '#28a745');
                    $('#status-icon').css('color', '#28a745');
                } catch (err) {
                    console.error('Error parsing SMS update:', err);
                }
            });

            // Heartbeat ping
            eventSource.addEventListener('ping', function(e) {
                try {
                    $('#connection-status').html('● Connected').css('color', '#28a745');
                    $('#status-icon').css('color', '#28a745');
                } catch (err) {
                    console.error('Error parsing ping:', err);
                }
            });

            // Open event
            eventSource.onopen = function() {
                reconnectAttempts = 0;
                $('#connection-status').html('● Connected').css('color', '#28a745');
                $('#status-icon').css('color', '#28a745');
                console.log('SSE connection opened');
            };

            // Error handling
            eventSource.onerror = function(e) {
                console.error('SSE Error:', e);
                
                if (eventSource.readyState === EventSource.CLOSED) {
                    $('#connection-status').html('● Disconnected').css('color', '#dc3545');
                    $('#status-icon').css('color', '#dc3545');
                    
                    reconnectAttempts++;
                    if (reconnectAttempts <= maxReconnectAttempts) {
                        const delay = Math.min(1000 * Math.pow(1.5, reconnectAttempts), 30000);
                        $('#connection-status').html('● Reconnecting in ' + (delay/1000).toFixed(1) + 's...').css('color', '#ffc107');
                        
                        reconnectTimeout = setTimeout(function() {
                            connectSSE();
                        }, delay);
                    } else {
                        $('#connection-status').html('● Failed to connect - Click Reconnect').css('color', '#dc3545');
                        $('#status-icon').css('color', '#dc3545');
                    }
                }
            };
        } catch (err) {
            console.error('Failed to create EventSource:', err);
            $('#connection-status').html('● Error connecting').css('color', '#dc3545');
            $('#status-icon').css('color', '#dc3545');
            
            reconnectTimeout = setTimeout(function() {
                connectSSE();
            }, 5000);
        }
    }

    // Manual reconnect function (exposed globally)
    window.reconnectSSE = function() {
        reconnectAttempts = 0;
        connectSSE();
    };

    // Load chart data after a short delay
    setTimeout(function() {
        loadChartData();
    }, 500);

    // Start SSE connection
    connectSSE();

    // Clean up on page unload
    $(window).on('beforeunload', function() {
        if (eventSource) {
            eventSource.close();
            eventSource = null;
        }
        if (reconnectTimeout) {
            clearTimeout(reconnectTimeout);
            reconnectTimeout = null;
        }
    });

    // Handle page visibility change
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            if (!eventSource || eventSource.readyState === EventSource.CLOSED) {
                connectSSE();
            }
        }
    });
});
</script>

<style>
.today-highlight { 
    border: 2px solid #d15b47 !important; 
    font-weight: bold; 
}

.progress-mini { 
    height: 10px; 
    margin-bottom: 0; 
}

.widget-box { 
    margin-bottom: 15px; 
}

/* Live dot animation */
.live-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    background: #28a745;
    border-radius: 50%;
    animation: pulse 2s infinite;
    margin-right: 5px;
    vertical-align: middle;
}

@keyframes  pulse {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.8); }
    100% { opacity: 1; transform: scale(1); }
}

/* Trend indicator styles */
.trend-indicator {
    display: inline-block;
    margin-left: 5px;
    font-size: 12px;
}

.trend-up-green {
    color: #28a745 !important;
    font-weight: bold;
}

.trend-up-yellow {
    color: #ffc107 !important;
}

.trend-down-red {
    color: #dc3545 !important;
}

.trend-neutral {
    color: #888 !important;
}

/* Widget header highlight for changes */
.widget-header-flat {
    transition: background-color 0.5s ease;
}

/* Smooth progress bar transitions */
.progress-bar {
    transition: width 0.5s ease-in-out !important;
}

/* Live indicator pulse */
#live-indicator .fa-circle {
    animation: pulse 2s infinite;
}

/* Connection status */
#connection-status {
    font-weight: 500;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>