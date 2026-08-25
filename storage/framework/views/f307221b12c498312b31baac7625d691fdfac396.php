<?php $__env->startSection('dashboard_menu_class','active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
        </li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_header'); ?>
    <h1>
        <i class="ace-icon fa fa-dashboard"></i>
        Dashboard
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Overview & Statistics
        </small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
    <div class="row">
        <!-- Main Content - Left Side -->
        <div class="col-sm-9">
            <!-- Statistics Cards Row -->
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="panel panel-primary dashboard-card">
                        <div class="panel-body text-center">
                            <i class="ace-icon fa fa-envelope fa-3x blue"></i>
                            <h2 class="stat-number"><?php echo e(number_format($data['last_week_sms'], 0)); ?></h2>
                            <p class="stat-label">SMS LAST WEEK</p>
                        </div>
                        <div class="panel-footer">
                            <div class="text-center">
                                <i class="ace-icon fa fa-arrow-circle-up"></i>
                                <small>Weekly Performance</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="panel panel-green dashboard-card">
                        <div class="panel-body text-center">
                            <i class="ace-icon fa fa-money fa-3x green"></i>
                            <h2 class="stat-number">৳ <?php echo e(number_format($data['last_week_cost'], 2)); ?></h2>
                            <p class="stat-label">COST LAST WEEK</p>
                        </div>
                        <div class="panel-footer">
                            <div class="text-center">
                                <i class="ace-icon fa fa-line-chart"></i>
                                <small>Weekly Spending</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="panel panel-orange dashboard-card">
                        <div class="panel-body text-center">
                            <i class="ace-icon fa fa-bar-chart fa-3x orange"></i>
                            <h2 class="stat-number"><?php echo e(number_format($data['last_month_sms'], 0)); ?></h2>
                            <p class="stat-label">SMS LAST MONTH</p>
                        </div>
                        <div class="panel-footer">
                            <div class="text-center">
                                <i class="ace-icon fa fa-arrow-circle-up"></i>
                                <small>Monthly Performance</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="panel panel-red dashboard-card">
                        <div class="panel-body text-center">
                            <i class="ace-icon fa fa-credit-card fa-3x red"></i>
                            <h2 class="stat-number">৳ <?php echo e(number_format($data['last_month_cost'], 2)); ?></h2>
                            <p class="stat-label">COST LAST MONTH</p>
                        </div>
                        <div class="panel-footer">
                            <div class="text-center">
                                <i class="ace-icon fa fa-pie-chart"></i>
                                <small>Monthly Budget</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SMS Usage Graph -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="widget-box transparent">
                        <div class="widget-header widget-header-flat">
                            <h4 class="widget-title lighter">
                                <i class="ace-icon fa fa-line-chart orange"></i>
                                SMS Usage Overview - Last 6 Months
                            </h4>
                            <div class="widget-toolbar">
                                <a href="#" data-action="collapse">
                                    <i class="ace-icon fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <canvas id="smsUsageChart" height="120" style="width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SMS History Section -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="widget-box transparent">
                        <div class="widget-header widget-header-flat">
                            <h4 class="widget-title lighter">
                                <i class="ace-icon fa fa-history orange"></i>
                                SMS History - Last 12 Months
                            </h4>
                            <div class="widget-toolbar">
                                <a href="#" data-action="collapse">
                                    <i class="ace-icon fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>

                     <div class="widget-body">
    <div class="widget-main no-padding">
        <div class="table-responsive">
            <table class="table table-condensed table-bordered table-striped table-hover" style="margin-bottom: 0;">
                <thead class="bg-light-blue">
                    <tr>
                        <th class="text-center" style="padding: 8px 5px;">
                            <i class="ace-icon fa fa-calendar blue"></i> Month
                        </th>
                        <th class="text-center" style="padding: 8px 5px;">
                            <i class="ace-icon fa fa-money blue"></i> Cost
                        </th>
                        <th class="text-center" style="padding: 8px 5px;">
                            <i class="ace-icon fa fa-bar-chart blue"></i> SMS
                        </th>
                        <th class="text-center" style="padding: 8px 5px;">
                            <i class="ace-icon fa fa-line-chart blue"></i> Avg/SMS
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $data['monthly_sms']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthly): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-center" style="padding: 6px 4px; text-transform: uppercase; font-size: 11px;">
                            <strong><?php echo e(DateTime::createFromFormat('!m', $monthly->month)->format('M')); ?>, <?php echo e($monthly->year); ?></strong>
                        </td>
                        <td class="text-center" style="padding: 6px 4px;">
                            <span class="badge badge-success" style="font-size: 10px; padding: 3px 6px;">৳ <?php echo e(number_format($monthly->total_sms_cost, 2)); ?></span>
                        </td>
                        <td class="text-center" style="padding: 6px 4px;">
                            <span class="badge badge-info" style="font-size: 10px; padding: 3px 6px;"><?php echo e(number_format($monthly->total_sms, 0)); ?></span>
                        </td>
                        <td class="text-center" style="padding: 6px 4px;">
                            <span class="badge badge-warning" style="font-size: 10px; padding: 3px 6px;">
                                ৳ <?php echo e($monthly->total_sms > 0 ? number_format($monthly->total_sms_cost / $monthly->total_sms, 4) : '0.0000'); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr class="bg-light-grey">
                        <td class="text-right" style="padding: 6px 4px; font-size: 11px;"><strong>Total:</strong></td>
                        <td class="text-center" style="padding: 6px 4px;">
                            <strong style="font-size: 11px;">৳ <?php echo e(number_format($data['monthly_sms']->sum('total_sms_cost'), 2)); ?></strong>
                        </td>
                        <td class="text-center" style="padding: 6px 4px;">
                            <strong style="font-size: 11px;"><?php echo e(number_format($data['monthly_sms']->sum('total_sms'), 0)); ?></strong>
                        </td>
                        <td class="text-center" style="padding: 6px 4px;">
                            <strong style="font-size: 11px;">৳ <?php echo e($data['monthly_sms']->sum('total_sms') > 0 ? number_format($data['monthly_sms']->sum('total_sms_cost') / $data['monthly_sms']->sum('total_sms'), 4) : '0.0000'); ?></strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Right Side -->
        <div class="col-sm-3">
            <!-- Statistics Panel -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ace-icon fa fa-bar-chart-o"></i>
                        QUICK STATISTICS
                    </h3>
                </div>
                <div class="panel-body">
                    <div id="statistics-loader" style="display: none; text-align: center; padding: 20px;">
                        <i class="ace-icon fa fa-spinner fa-spin blue fa-2x"></i>
                        <p class="text-muted">Loading statistics...</p>
                    </div>
                    
                    <div id="statistics-content">
                        <!-- Balance Display - Initially Hidden -->
                        <div id="balance-display-container" style="display: none;">
                            <div class="well well-sm bg-info">
                                <h4 class="text-center" style="margin: 0;">
                                    <i class="ace-icon fa fa-wallet"></i>
                                    Current Balance
                                </h4>
                                <h2 class="text-center text-success" style="margin: 10px 0;">
                                    <span id="balance-display">0.00</span>
                                    <small style="font-size: 0.6em;">BDT</small>
                                </h2>
                            </div>
                        </div>

                        <!-- SMS Credit Table -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <i class="ace-icon fa fa-mobile"></i>
                                    SMS Credit Estimate
                                </h5>
                            </div>
                            <div class="panel-body" style="padding: 0;">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-hover">
                                        <thead class="bg-light-blue">
                                            <tr>
                                                <th>Operator</th>
                                                <th class="text-center">Masking</th>
                                                <th class="text-center">Non-Masking</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sms-credit-data">
                                            <tr>
                                                <td colspan="3" class="text-center" style="padding: 20px;">
                                                    <button class="btn btn-sm btn-primary btn-round" id="load-statistics-btn" onclick="loadStatistics()">
                                                        <i class="ace-icon fa fa-refresh"></i>
                                                        Load Statistics
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
               <div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title">
            <i class="ace-icon fa fa-exchange"></i>
            Recent Transactions
        </h5>
    </div>
    <div class="panel-body" style="padding: 0;">
        <ul class="list-group" style="margin-bottom: 0;">
            <?php $__currentLoopData = $data['transactions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item" style="padding: 8px 12px; border-radius: 0; border-left: none; border-right: none;">
                <div class="clearfix">
                    <span class="pull-left">
                        <i class="ace-icon fa fa-calendar"></i>
                        <?php echo e(\Carbon\Carbon::parse($tran->created_at)->format('M d')); ?>

                    </span>
                    <span class="pull-right text-success">
                        <strong>৳ <?php echo e(number_format($tran->asb_credit - $tran->asb_debit, 2)); ?></strong>
                    </span>
                </div>
                <small class="text-muted" style="font-size: 11px;">
                    <?php echo e(\Carbon\Carbon::parse($tran->created_at)->format('h:i A')); ?>

                </small>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</div>


                    <!-- Campaign Refunds -->
           <div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title">
            <i class="ace-icon fa fa-undo"></i>
            Campaign Refunds
        </h5>
    </div>
    <div class="panel-body" style="padding: 0;">
        <ul class="list-group" style="margin-bottom: 0;">
            <?php $__currentLoopData = $data['campaign_rejects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $camp_rej): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item" style="padding: 6px 10px; border-radius: 0; border: none; border-bottom: 1px solid #eee;">
                <div class="clearfix">
                    <span class="pull-left" style="font-size: 12px;">
                        <i class="ace-icon fa fa-calendar"></i>
                        <?php echo e(\Carbon\Carbon::parse($camp_rej->created_at)->format('M d')); ?>

                    </span>
                    <span class="pull-right text-info" style="font-size: 12px;">
                        <strong>৳ <?php echo e(number_format($camp_rej->asb_credit - $camp_rej->asb_debit, 2)); ?></strong>
                    </span>
                </div>
                <small class="text-muted" style="font-size: 10px;">
                    <?php echo e(\Carbon\Carbon::parse($camp_rej->created_at)->format('h:i A')); ?>

                </small>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_style'); ?>
<style>
    .dashboard-card {
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        margin-bottom: 20px;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .panel-body {
    padding: 0px;
}
 
    .dashboard-card .panel-footer {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
        padding: 10px 15px;
    }
    
    .stat-number {
        font-size: 24px;
        font-weight: bold;
        margin: 10px 0;
        color: #2c3e50;
    }
    
    .stat-label {
        color: #7f8c8d;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .bg-light-blue {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    }
    
    .bg-light-grey {
        background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
    }
    
    .badge {
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 11px;
    }
    
    .btn-round {
        border-radius: 20px;
        padding: 6px 20px;
    }
    
    .list-group-item {
        border: none;
        border-bottom: 1px solid #eee;
        padding: 12px 15px;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    .well {
        border-radius: 8px;
        background: linear-gradient(135deg, #e8f4fd 0%, #d1e9ff 100%);
        border: 1px solid #b8daff;
    }
    
    .table-hover tbody tr:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .chart-container {
        position: relative;
        height: 120px;
        width: 100%;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function loadStatistics() {
        // Show loader, hide button
        document.getElementById('statistics-loader').style.display = 'block';
        document.getElementById('load-statistics-btn').style.display = 'none';
        
        // Make AJAX request to get statistics
        fetch('<?php echo e(route("user.statistics")); ?>', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Check if there's an error in the response
            if (data.error) {
                throw new Error(data.error);
            }
            
            console.log('Statistics data:', data);
            
            // Show balance display container
            const balanceContainer = document.getElementById('balance-display-container');
            balanceContainer.style.display = 'block';
            balanceContainer.classList.add('fade-in-up');
            
            // Update balance with animation
            const balanceElement = document.getElementById('balance-display');
            const newBalance = new Intl.NumberFormat().format((data.balance_bd).toFixed(2));
            balanceElement.textContent = newBalance;
            
            // Update SMS credit table
            let smsCreditHtml = '';
            if (data.sms_credit && data.sms_credit.length > 0) {
                data.sms_credit.forEach(credit => {
                    const operatorName = credit.operator ? credit.operator.ope_operator_name : 'Unknown';
                    const masking = credit.asr_masking ? Math.floor(data.balance_bd / credit.asr_masking) : 'N/A';
                    const nonMasking = credit.asr_nonmasking ? Math.floor(data.balance_bd / credit.asr_nonmasking) : 'N/A';
                    
                    smsCreditHtml += `
                        <tr>
                            <td><small>${operatorName}</small></td>
                            <td class="text-center"><span class="badge badge-primary">${masking}</span></td>
                            <td class="text-center"><span class="badge badge-info">${nonMasking}</span></td>
                        </tr>
                    `;
                });
            } else {
                smsCreditHtml = `
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            <small>No SMS credit data available</small>
                        </td>
                    </tr>
                `;
            }
            document.getElementById('sms-credit-data').innerHTML = smsCreditHtml;
            
            // Hide loader
            document.getElementById('statistics-loader').style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
            document.getElementById('statistics-loader').style.display = 'none';
            document.getElementById('load-statistics-btn').style.display = 'block';
            
            // Show error message
            const errorHtml = `
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        <small>Error loading statistics</small>
                        <br>
                        <button class="btn btn-xs btn-warning" onclick="loadStatistics()">
                            <i class="ace-icon fa fa-refresh"></i>
                            Try Again
                        </button>
                    </td>
                </tr>
            `;
            document.getElementById('sms-credit-data').innerHTML = errorHtml;
        });
    }
    
    // Create SMS Usage Chart
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare chart data from monthly SMS data
        const monthlyData = <?php echo json_encode($data['monthly_sms'], 15, 512) ?>;
        
        // Get last 6 months for the chart
        const last6Months = monthlyData.slice(0, 6).reverse();
        
        const months = last6Months.map(item => {
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return `${monthNames[item.month - 1]} '${item.year.toString().slice(2)}`;
        });
        
        const smsCounts = last6Months.map(item => item.total_sms);
        const smsCosts = last6Months.map(item => item.total_sms_cost);
        
        // Create the chart
        const ctx = document.getElementById('smsUsageChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'SMS Count',
                        data: smsCounts,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Cost (BDT)',
                        data: smsCosts,
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // Add hover effects to cards
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>