
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>IGL SMS Sending Cron</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2074b5;
            --secondary-color: #2c3e50;
            --accent-color: #3498db;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #364152;
        }

        .navbar {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 1.5rem;
        }

        .card {
            border: 1px solid rgba(0, 0, 0, 0.03);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            color: white;
            font-weight: 600;
            border-bottom: none;
            padding: 1.25rem 1.5rem;
            font-size: 1.1rem;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-title {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            border-radius: 50px;
            font-weight: 600;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .alert-refresh {
            background-color: white;
            border-left: 4px solid var(--danger-color);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-top: 1px solid rgba(0, 0, 0, 0.03);
            border-right: 1px solid rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        .progress-container {
            margin-top: 1rem;
            background-color: #f1f5f9;
            border-radius: 50px;
            height: 6px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            transition: width 1s linear;
        }

        footer {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 1.5rem;
            font-size: 0.9rem;
            margin-top: 2rem;
        }

        footer a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        .operator-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-radius: 8px;
            padding: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .operator-icon img,
        .operator-icon svg {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .list-group-item {
            transition: all 0.2s;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        .list-group-item:hover {
            background-color: #f8fafc;
        }

        .count-badge {
            font-size: 1rem;
            font-weight: 600;
            padding: 0.35em 0.65em;
            border-radius: 8px;
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            max-width: 60%;
            text-align: right;
            white-space: normal;
            word-break: break-word;
        }

        .mask-bg {
            background: linear-gradient(135deg, #2074b5, #3498db);
        }

        .nonmask-bg {
            background: linear-gradient(135deg, #2c3e50, #4a6fa5);
        }

        .alert-icon {
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .section-title {
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            font-size: 1.3rem;
        }

        .cron-error {
            white-space: pre-wrap;
            word-break: break-word;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-dark">
        <div class="container">
            <div class="d-flex align-items-center">
                <i class="bi bi-send-fill me-2" style="font-size: 1.8rem;color:white"></i>
                <h3 class="navbar-brand mb-0">SMS Sending Cron</h3>
            </div>
            <div class="d-flex align-items-center">
                <h3 class="navbar-brand mb-0">IGL Web</h3>
            </div>
            <span class="badge bg-light text-dark">Live Monitoring</span>
        </div>
    </nav>
</header>

<main role="main">
    <section class="pricing py-5">
        <div class="container">
           <?php
    if (!function_exists('shortSmsStatus')) {
        function shortSmsStatus($text) {
            $text = trim((string) $text);

            if ($text === '' || strtolower($text) === 'n/a') {
                return 'No Content';
            }

            if (preg_match('/Processed\s+(\d+)/i', $text, $match)) {
                return $match[1] . ' SMS Processing';
            }

            if (preg_match('/Working\.*\s*(\d*)/i', $text, $match)) {
                return !empty($match[1])
                    ? $match[1] . ' SMS Processing'
                    : 'SMS Processing';
            }

            if (stripos($text, 'No content') !== false) {
                return 'No Content';
            }

            if (strlen($text) > 35) {
                return substr($text, 0, 35) . '...';
            }

            return $text;
        }
    }
?>

            <?php if(!empty($returnError['errorNotify'])): ?>
                <div class="alert alert-danger cron-error mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error:</strong> <?php echo e($returnError['errorNotify']); ?>

                </div>
            <?php endif; ?>

            <?php if(!empty($returnError['gpError'])): ?>
                <div class="alert alert-danger cron-error mb-3" role="alert">
                    <strong>GP Error:</strong> <?php echo e($returnError['gpError']); ?>

                </div>
            <?php endif; ?>

            <?php if(!empty($returnError['blError'])): ?>
                <div class="alert alert-danger cron-error mb-3" role="alert">
                    <strong>Banglalink Error:</strong> <?php echo e($returnError['blError']); ?>

                </div>
            <?php endif; ?>

            <?php if(!empty($returnError['robiAirtelError'])): ?>
                <div class="alert alert-danger cron-error mb-3" role="alert">
                    <strong>Robi/Airtel Error:</strong> <?php echo e($returnError['robiAirtelError']); ?>

                </div>
            <?php endif; ?>

            <?php if(!empty($returnError['ttError'])): ?>
                <div class="alert alert-danger cron-error mb-3" role="alert">
                    <strong>Teletalk Error:</strong> <?php echo e($returnError['ttError']); ?>

                </div>
            <?php endif; ?>

            <div class="alert alert-refresh alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-arrow-repeat alert-icon text-danger"></i>
                    <div>
                        <strong>Auto-refresh enabled</strong> - This page refreshes every 5 seconds to send SMS. Do not close it.
                    </div>
                </div>
                <div class="progress-container mt-2">
                    <div class="progress-bar" id="refresh-progress" role="progressbar"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header mask-bg text-white d-flex justify-content-between align-items-center">
                            <div class="section-title">
                                <i class="bi bi-mask"></i>
                                <h5 class="card-title mb-0">MASK SMS</h5>
                            </div>
                            <span class="status-badge">Active</span>
                        </div>

                        <div class="card-body p-0">
                            <?php if(isset($returnData['message'])): ?>
                                <div class="alert alert-info m-3 mb-0">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <?php echo e($returnData['message']); ?>

                                </div>
                            <?php endif; ?>

                            <div class="list-group list-group-flush">
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="operator-icon me-3">
                                                <img src="https://www.robi.com.bd/_next/static/media/robi-logo-2.d08ae93d.svg" class="img-fluid" alt="Robi">
                                            </div>
                                            <div class="operator-icon me-3">
                                                <img src="https://www.bd.airtel.com/_next/static/media/airtel-logo.7cd96f91.svg" class="img-fluid" alt="Airtel">
                                            </div>
                                            <span class="fw-medium">Robi/Airtel</span>
                                        </div>
                                        <span class="count-badge"><?php echo e(shortSmsStatus($returnData['robi_airtel'] ?? 'N/A')); ?></span>
                                    </div>
                                </div>

                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="operator-icon me-3">
                                                <img src="https://banglalink.net/logo.svg" class="img-fluid" alt="Banglalink">
                                            </div>
                                            <span class="fw-medium">Banglalink</span>
                                        </div>
                                        <span class="count-badge"><?php echo e(shortSmsStatus($returnData['banglalink'] ?? 'N/A')); ?></span>
                                    </div>
                                </div>

                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="operator-icon me-3">GP</div>
                                            <span class="fw-medium">Grameenphone</span>
                                        </div>
                                        <span class="count-badge"><?php echo e(shortSmsStatus($returnData['gp'] ?? 'N/A')); ?></span>
                                    </div>
                                </div>

                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="operator-icon me-3">
                                                <img src="https://upload.wikimedia.org/wikipedia/en/thumb/3/3f/Teletalk_Bangladesh_Limited.svg/260px-Teletalk_Bangladesh_Limited.svg.png?20160320162058" class="img-fluid" alt="Teletalk">
                                            </div>
                                            <span class="fw-medium">Teletalk</span>
                                        </div>
                                        <span class="count-badge"><?php echo e(shortSmsStatus($returnData['teletalk'] ?? 'N/A')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header nonmask-bg text-white d-flex justify-content-between align-items-center">
                            <div class="section-title">
                                <i class="bi bi-mask"></i>
                                <h5 class="card-title mb-0">NON-MASK SMS</h5>
                            </div>
                            <span class="status-badge">Active</span>
                        </div>

                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="operator-icon me-3">
                                                <img src="https://www.robi.com.bd/_next/static/media/robi-logo-2.d08ae93d.svg" class="img-fluid" alt="Robi">
                                            </div>
                                            <span class="fw-medium">Robi</span>
                                        </div>
                                        <span class="count-badge"><?php echo e(shortSmsStatus($retTextRobi ?? 'N/A')); ?></span>
                                    </div>
                                </div>

                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="operator-icon me-3">GP</div>
                                            <span class="fw-medium">Grameenphone</span>
                                        </div>
                                        <span class="count-badge"><?php echo e(shortSmsStatus($retTextGp ?? 'N/A')); ?></span>
                                    </div>
                                </div>

                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="operator-icon me-3">
                                                <img src="https://banglalink.net/logo.svg" class="img-fluid" alt="Banglalink">
                                            </div>
                                            <span class="fw-medium">Banglalink</span>
                                        </div>
                                        <span class="count-badge"><?php echo e(shortSmsStatus($retTextBl ?? 'N/A')); ?></span>
                                    </div>
                                </div>

                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="operator-icon me-3">
                                                <img src="https://iglgroup.org/media/logos/IGL_logo_PNG_WITH_LOGO-01.png" class="img-fluid" alt="IP TSP">
                                            </div>
                                            <span class="fw-medium">IP TSP</span>
                                        </div>
                                        <span class="count-badge"><?php echo e(shortSmsStatus($retTextIptsp ?? 'N/A')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <p class="mb-0">&copy; <a href="https://iglweb.com" class="text-white">IGL Web</a> - All Rights Reserved</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.addEventListener('load', function () {
        setTimeout(function () {
            location.reload();
        }, 5000);
    });
</script>
</body>
</html>
