<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>iRecharge Flexiload - Smart Recharge Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes  fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header Styles */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .header p {
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .badge-auto {
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Status Cards */
        .status-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .status-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .status-card.connected::before {
            background: linear-gradient(90deg, #28a745, #20c997);
        }

        .status-card.disconnected::before {
            background: linear-gradient(90deg, #dc3545, #f86c6b);
        }

        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .status-card h3 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-value {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-value.success {
            color: #28a745;
        }

        .status-value.failed {
            color: #dc3545;
        }

        .gateway-info {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            padding: 15px;
            border-radius: 12px;
            margin-top: 15px;
        }

        .gateway-info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        .gateway-info-item:last-child {
            border-bottom: none;
        }

        .gateway-label {
            font-weight: 600;
            color: #555;
        }

        .gateway-value {
            color: #667eea;
            font-weight: 500;
        }

        .balance-amount {
            font-size: 24px;
            font-weight: 800;
            color: #28a745;
        }

        /* Stats Grid */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }

        /* Results Section */
        .results-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease-out;
        }

        @keyframes  slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .results-section h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 12px;
        }

        .results-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .results-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        .results-table td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .results-table tbody tr:hover {
            background: #f8f9fa;
        }

        .status-success {
            color: #28a745;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            background: #28a74520;
            border-radius: 20px;
        }

        .status-failed {
            color: #dc3545;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            background: #dc354520;
            border-radius: 20px;
        }

        .transaction-id {
            font-family: monospace;
            font-size: 12px;
            background: #f0f0f0;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        /* Button Styles */
        .refresh-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 35px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        /* Info Box */
        .info-box {
            margin-top: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-radius: 15px;
            border-left: 4px solid #ffc107;
            position: relative;
            overflow: hidden;
        }

        .info-box small {
            font-size: 13px;
            color: #856404;
            line-height: 1.6;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .results-table {
                font-size: 12px;
            }
            
            .results-table th,
            .results-table td {
                padding: 8px;
            }
            
            .stat-number {
                font-size: 28px;
            }
            
            .status-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-bolt"></i>
                iRecharge Flexiload
                <span class="badge-auto">
                    <i class="fas fa-sync-alt"></i> Auto-Sync (30s)
                </span>
            </h1>
            <p>
                <i class="fas fa-clock"></i> Real-time recharge processing
                <i class="fas fa-chart-line"></i> Smart queue management
                <i class="fas fa-shield-alt"></i> Secure transactions
            </p>
        </div>

        <!-- Connection & Gateway Status -->
        <div class="status-cards">
            <!-- Connection Status Card -->
            <div class="status-card <?php echo e(isset($connection_status['success']) && $connection_status['success'] ? 'connected' : 'disconnected'); ?>">
                <h3>
                    <i class="fas fa-tower-broadcast"></i>
                    Gateway Connection
                </h3>
                <div class="status-value <?php echo e(isset($connection_status['success']) && $connection_status['success'] ? 'success' : 'failed'); ?>">
                    <?php if(isset($connection_status['success']) && $connection_status['success']): ?>
                        <i class="fas fa-check-circle"></i> CONNECTED
                    <?php else: ?>
                        <i class="fas fa-times-circle"></i> DISCONNECTED
                    <?php endif; ?>
                </div>
                <p style="margin-top: 10px; color: #666; font-size: 13px;">
                    <i class="fas fa-info-circle"></i>
                    <?php echo e($connection_status['message'] ?? 'Connection status unknown'); ?>

                </p>
                <?php if(isset($connection_status['active_gateways'])): ?>
                    <div class="gateway-info">
                        <div class="gateway-info-item">
                            <span class="gateway-label"><i class="fas fa-server"></i> Active Gateways:</span>
                            <span class="gateway-value"><?php echo e($connection_status['active_gateways']); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Gateway Balance Card -->
            <?php if(isset($gateway_info) && isset($gateway_info['success']) && $gateway_info['success'] === true): ?>
            <div class="status-card connected">
                <h3>
                    <i class="fas fa-wallet"></i>
                    Gateway Balance
                </h3>
                <div class="status-value success">
                    <i class="fas fa-taka-sign"></i> 
                    <span class="balance-amount"><?php echo e(number_format($gateway_info['current_balance'] ?? 0, 2)); ?></span>
                </div>
                <div class="gateway-info">
                    <div class="gateway-info-item">
                        <span class="gateway-label"><i class="fas fa-microchip"></i> Gateway:</span>
                        <span class="gateway-value"><?php echo e($gateway_info['gateway_name'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="gateway-info-item">
                        <span class="gateway-label"><i class="fas fa-signal"></i> Operator:</span>
                        <span class="gateway-value"><?php echo e($gateway_info['operator'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="gateway-info-item">
                        <span class="gateway-label"><i class="fas fa-plug"></i> Connection:</span>
                        <span class="gateway-value"><?php echo e(ucfirst($gateway_info['connection_status'] ?? 'unknown')); ?></span>
                    </div>
                    <div class="gateway-info-item">
                        <span class="gateway-label"><i class="fas fa-code-branch"></i> Software:</span>
                        <span class="gateway-value"><?php echo e($gateway_info['software_active'] ? 'Active' : 'Inactive'); ?></span>
                    </div>
                    <?php if(isset($gateway_info['supported_operators']) && count($gateway_info['supported_operators']) > 0): ?>
                    <div class="gateway-info-item">
                        <span class="gateway-label"><i class="fas fa-mobile-alt"></i> Supports:</span>
                        <span class="gateway-value"><?php echo e(implode(', ', $gateway_info['supported_operators'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="status-card disconnected">
                <h3>
                    <i class="fas fa-wallet"></i>
                    Gateway Balance
                </h3>
                <div class="status-value failed">
                    <i class="fas fa-exclamation-triangle"></i> UNAVAILABLE
                </div>
                <p style="margin-top: 10px; color: #666; font-size: 13px;">
                    <i class="fas fa-info-circle"></i>
                    <?php echo e($gateway_info['message'] ?? 'Unable to fetch gateway information'); ?>

                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pending Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-database"></i></div>
                <div class="stat-number"><?php echo e($rest_pendings); ?></div>
                <div class="stat-label">Total Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fab fa-google"></i></div>
                <div class="stat-number"><?php echo e($gp_pending); ?></div>
                <div class="stat-label">Grameenphone</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-signal"></i></div>
                <div class="stat-number"><?php echo e($robi_pending); ?></div>
                <div class="stat-label">Robi</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-circle"></i></div>
                <div class="stat-number"><?php echo e($bl_pending); ?></div>
                <div class="stat-label">Banglalink</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-wifi"></i></div>
                <div class="stat-number"><?php echo e($airtel_pending); ?></div>
                <div class="stat-label">Airtel</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-phone-alt"></i></div>
                <div class="stat-number"><?php echo e($tt_pending); ?></div>
                <div class="stat-label">Teletalk</div>
            </div>
        </div>

        <!-- Processing Results -->
        <?php if(isset($results) && count($results) > 0): ?>
        <div class="results-section">
            <h2>
                <i class="fas fa-chart-bar"></i>
                Recent Transactions
                <span style="font-size: 12px; background: #e0e0e0; padding: 4px 12px; border-radius: 20px; font-weight: normal;">
                    <i class="fas fa-clock"></i> <?php echo e(date('h:i:s A')); ?>

                </span>
            </h2>
            <table class="results-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-flag-checkered"></i> Status</th>
                        <th><i class="fas fa-fingerprint"></i> Transaction ID</th>
                        <th><i class="fas fa-envelope"></i> Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong>#<?php echo e($result['id']); ?></strong></td>
                        <td>
                            <?php if($result['status'] == 'success'): ?>
                                <span class="status-success">
                                    <i class="fas fa-check-circle"></i> Success
                                </span>
                            <?php else: ?>
                                <span class="status-failed">
                                    <i class="fas fa-exclamation-circle"></i> Failed
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($result['status'] == 'success'): ?>
                                <span class="transaction-id">
                                    <i class="fas fa-hashtag"></i> <?php echo e($result['transaction_id'] ?? 'N/A'); ?>

                                </span>
                            <?php else: ?>
                                <span style="color: #dc3545; font-size: 12px;">
                                    <i class="fas fa-ban"></i> <?php echo e($result['error'] ?? 'Unknown error'); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="max-width: 300px; word-break: break-word;"><?php echo e($result['message'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="results-section">
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No Recent Transactions</h3>
                <p>Waiting for new recharge requests...</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div style="text-align: center;">
            <button class="refresh-btn" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i>
                Process More Transactions
            </button>
        </div>

        <!-- Information Box -->
        <div class="info-box">
            <small>
                <strong><i class="fas fa-info-circle"></i> System Information</strong><br>
                🔄 <strong>Auto-refresh:</strong> Dashboard refreshes every 30 seconds<br>
                ⚡ <strong>Batch Size:</strong> Processes up to 5 pending loads per execution<br>
                🌐 <strong>API Endpoint:</strong> https://gateway.irecharge.net/api/v1/create_request<br>
                📞 <strong>Callback URL:</strong> <?php echo e(route('irecharge.callback')); ?><br>
                💡 <strong>Note:</strong> Failed transactions are automatically marked and won't be retried
            </small>
        </div>
    </div>

    <script>
        // Add loading effect on refresh button
        document.querySelector('.refresh-btn')?.addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
            
            setTimeout(() => {
                window.location.reload();
            }, 500);
        });

        // Log pending loads count
        <?php if($rest_pendings > 0): ?>
        console.log('📊 Pending loads: <?php echo e($rest_pendings); ?>');
        <?php endif; ?>
        
        // Auto-refresh status
        console.log('🔄 Auto-refresh enabled - Page refreshes every 30 seconds');
    </script>
</body>
</html>
