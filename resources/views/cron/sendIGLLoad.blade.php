<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>IGL Flexiload Sender</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card-header { background-color: #fff; border-bottom: 1px solid #e3e6f0; padding: 15px 20px; }
        .card-header h3 { margin: 0; font-size: 1.5rem; display: inline-block; }
        .card-header-actions { float: right; }
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        .table-responsive { overflow-x: auto; }
        .rounded { border-radius: 8px; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .mt-3 { margin-top: 15px; }
        .mb-0 { margin-bottom: 0; }
        .mb-4 { margin-bottom: 20px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fa-3x { font-size: 2em; }
        .bg-primary { background-color: #4e73df !important; }
        .bg-success { background-color: #1cc88a !important; }
        .bg-danger { background-color: #e74a3b !important; }
        .bg-info { background-color: #36b9cc !important; }
        .bg-warning { background-color: #f6c23e !important; }
        .bg-secondary { background-color: #858796 !important; }
        .bg-dark { background-color: #5a5c69 !important; }
        .text-white { color: white !important; }
        .alert { padding: 12px 20px; border-radius: 5px; margin-bottom: 20px; }
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .alert-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .container-fluid { padding: 20px; }
        table { width: 100%; background-color: white; }
        th { background-color: #f8f9fc; padding: 12px; text-align: left; border-bottom: 2px solid #e3e6f0; }
        td { padding: 10px 12px; border-bottom: 1px solid #e3e6f0; vertical-align: middle; }
        .btn-primary { background-color: #4e73df; border: none; color: white; padding: 6px 12px; text-decoration: none; display: inline-block; border-radius: 4px; }
        .btn-info { background-color: #36b9cc; border: none; color: white; padding: 6px 12px; text-decoration: none; display: inline-block; border-radius: 4px; }
        .btn-success { background-color: #1cc88a; border: none; color: white; padding: 6px 12px; text-decoration: none; display: inline-block; border-radius: 4px; }
        .btn-warning { background-color: #f6c23e; border: none; color: #212529; padding: 6px 12px; text-decoration: none; display: inline-block; border-radius: 4px; }
        .close { float: right; font-size: 1.5rem; font-weight: 700; line-height: 1; color: #000; text-shadow: 0 1px 0 #fff; opacity: .5; background: transparent; border: 0; cursor: pointer; }
        .alert-dismissible { padding-right: 40px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-money"></i> IGL (API Gateway Load) - Flexiload Sender</h3>
                        <div class="card-header-actions">
                            <a href="{{ route('igl.send') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-refresh"></i> Refresh
                            </a>
                            <a href="{{ route('igl.test') }}" class="btn btn-info btn-sm" target="_blank">
                                <i class="fa fa-flask"></i> Test Connection
                            </a>
                            <a href="{{ route('igl.sim-profiles') }}" class="btn btn-success btn-sm" target="_blank">
                                <i class="fa fa-list"></i> SIM Profiles
                            </a>
                            <a href="{{ route('igl.balance') }}" class="btn btn-warning btn-sm" target="_blank">
                                <i class="fa fa-database"></i> Check Balance
                            </a>
                        </div>
                        <div style="clear:both"></div>
                    </div>
                    <div class="card-body">
                        
                        @if(isset($error))
                            <div class="alert alert-danger alert-dismissible">
                                <strong><i class="fa fa-exclamation-triangle"></i> Error!</strong> {{ $error }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <h5 class="card-title">Total Pending</h5>
                                                <h2 class="mb-0">{{ $rest_pendings ?? 0 }}</h2>
                                            </div>
                                            <div class="col-4 text-right">
                                                <i class="fa fa-clock-o fa-3x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <h5 class="card-title">Success Today</h5>
                                                <h2 class="mb-0">{{ $success_count ?? 0 }}</h2>
                                            </div>
                                            <div class="col-4 text-right">
                                                <i class="fa fa-check-circle fa-3x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <h5 class="card-title">Failed Today</h5>
                                                <h2 class="mb-0">{{ $failed_count ?? 0 }}</h2>
                                            </div>
                                            <div class="col-4 text-right">
                                                <i class="fa fa-times-circle fa-3x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <h5 class="card-title">Last Run</h5>
                                                <h6 class="mb-0">{{ now()->format('H:i:s') }}</h6>
                                                <small>{{ now()->format('d M Y') }}</small>
                                            </div>
                                            <div class="col-4 text-right">
                                                <i class="fa fa-calendar fa-3x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending by Operator -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fa fa-bar-chart"></i> Pending Loads by Operator</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-2">
                                                <div class="bg-danger text-white p-3 rounded">
                                                    <h5>GP</h5>
                                                    <h3>{{ $gp_pending ?? 0 }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="bg-success text-white p-3 rounded">
                                                    <h5>Airtel</h5>
                                                    <h3>{{ $airtel_pending ?? 0 }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="bg-warning text-white p-3 rounded">
                                                    <h5>Banglalink</h5>
                                                    <h3>{{ $bl_pending ?? 0 }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="bg-info text-white p-3 rounded">
                                                    <h5>Robi</h5>
                                                    <h3>{{ $robi_pending ?? 0 }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="bg-secondary text-white p-3 rounded">
                                                    <h5>Teletalk</h5>
                                                    <h3>{{ $tt_pending ?? 0 }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="bg-dark text-white p-3 rounded">
                                                    <h5>Total</h5>
                                                    <h3>{{ ($gp_pending ?? 0) + ($airtel_pending ?? 0) + ($bl_pending ?? 0) + ($robi_pending ?? 0) + ($tt_pending ?? 0) }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Results Table -->
                        @if(isset($results) && count($results) > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fa fa-list"></i> Transaction Results</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#ID</th>
                                                        <th>SMS ID</th>
                                                        <th>Phone Number</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Transaction ID</th>
                                                        <th>Message/Error</th>
                                                        <th>Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($results as $result)
                                                    <tr>
                                                        <td>{{ $result['id'] ?? '-' }}</td>
                                                        <td>{{ $result['sms_id'] ?? '-' }}</td>
                                                        <td>{{ $result['phone'] ?? '-' }}</td>
                                                        <td>TK {{ $result['amount'] ?? '-' }}</td>
                                                        <td>
                                                            @if($result['status'] == 'success')
                                                                <span class="badge badge-success">Success</span>
                                                            @elseif($result['status'] == 'failed')
                                                                <span class="badge badge-danger">Failed</span>
                                                            @elseif($result['status'] == 'retry')
                                                                <span class="badge badge-warning">Retry</span>
                                                            @elseif($result['status'] == 'duplicate')
                                                                <span class="badge badge-info">Duplicate</span>
                                                            @else
                                                                <span class="badge badge-secondary">{{ $result['status'] }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(isset($result['transaction_id']))
                                                                <small>{{ $result['transaction_id'] }}</small>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small>{{ $result['message'] ?? $result['error'] ?? '-' }}</small>
                                                        </td>
                                                        <td>
                                                            <small>{{ now()->format('H:i:s') }}</small>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Auto Refresh Info -->
                        <div class="row mt-3">
                            <div class="col-md-12 text-center">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> 
                                    This page processes 5 pending loads at a time. 
                                    <strong>SMS ID (sms_id)</strong> is used as the <strong>idempotency_key</strong> for duplicate prevention.
                                    <br>
                                    <small>Page auto-refreshes every 30 seconds. 
                                        <a href="javascript:location.reload();" style="color: #0c5460;">Click here to refresh manually</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function() {
            location.reload();
        }, 30000);
        
        document.querySelectorAll('.close').forEach(function(button) {
            button.addEventListener('click', function() {
                this.closest('.alert').style.display = 'none';
            });
        });
    </script>
</body>
</html>
