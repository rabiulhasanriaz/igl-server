<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Flexiload Operator Monitoring</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root {
    --primary-color: #2074b5;
    --secondary-color: #2c3e50;
    --accent-color: #3498db;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --light-color: #f8fafc;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: var(--light-color);
    color: #364152;
}

.navbar {
    background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
    padding: 1rem 0;
}

.navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
    color: white;
}

.card {
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s, box-shadow 0.3s;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    font-weight: 900;
    border-radius: 12px 12px 0 0;
    font-size: 1.1rem;
}

.operator-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: white;
    border-radius: 8px;
    padding: 5px;
    margin-right: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.operator-icon img {
    max-width: 100%;
    max-height: 100%;
}

.count-badge {
    font-size: 1.1rem;
    font-weight: 900;
    padding: 0.45em 0.75em;
    border-radius: 8px;
    background-color: rgba(40, 167, 69, 0.1);
    color: var(--danger-color);
}

.alert-refresh {
    background-color: white;
    border-left: 4px solid var(--danger-color);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.progress-container {
    margin-top: 0.5rem;
    background-color: #f1f5f9;
    border-radius: 50px;
    height: 6px;
    overflow: hidden;
}

.refresh-progress {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
    width: 0%;
    transition: width 1s linear;
}

footer {
    background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
    color: white;
    text-align: center;
    padding: 1.5rem;
    font-size: 0.9rem;
}

footer a {
    color: white;
    text-decoration: none;
}
</style>
</head>
<body>
<header>
<nav class="navbar navbar-dark">
    <div class="container d-flex justify-content-between align-items-center">
        <h3 class="navbar-brand"><i class="bi bi-broadcast-pin-fill me-2"></i>Flexiload Cron</h3>
        <span class="badge bg-light text-dark">Live Monitoring</span>
    </div>
</nav>
</header>

<main class="py-5">
<div class="container">

    <div class="alert-refresh">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-arrow-repeat text-danger me-2"></i>
            <strong>Auto-refresh enabled</strong> — Refreshes every 10 seconds.
        </div>
        <div class="progress-container">
            <div class="refresh-progress" id="refresh-progress"></div>
        </div>
        <p class="mt-2 mb-0 text-muted">Total Pending Loads: <strong>{{ $rest_pendings }}</strong></p>
    </div>

    <div class="row g-4">
        <!-- Robi -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-header d-flex justify-content-center align-items-center">
                    <div class="operator-icon">
                        <img src="https://www.robi.com.bd/_next/static/media/robi-logo-2.d08ae93d.svg" alt="Robi">
                    </div>
                    <span class="ms-2">Robi</span>
                </div>
                <div class="card-body">
                    <p class="card-text">Total Loads:</p>
                    <span class="count-badge">{{ $robi_pending }}</span>
                </div>
            </div>
        </div>

        <!-- Grameenphone -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-header d-flex justify-content-center align-items-center">
                    <div class="operator-icon">
                      <img src="https://upload.wikimedia.org/wikipedia/commons/9/98/Grameenphone_Logo_GP_Logo.svg" alt="GP">
                    </div>
                    <span class="ms-2">Grameenphone</span>
                </div>
                <div class="card-body">
                    <p class="card-text">Total Loads:</p>
                    <span class="count-badge">{{ $gp_pending }}</span>
                </div>
            </div>
        </div>

        <!-- Banglalink -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-header d-flex justify-content-center align-items-center">
                    <div class="operator-icon">
                        <img src="https://banglalink.net/logo.svg" alt="Banglalink">
                    </div>
                    <span class="ms-2">Banglalink</span>
                </div>
                <div class="card-body">
                    <p class="card-text">Total Loads:</p>
                    <span class="count-badge">{{ $bl_pending }}</span>
                </div>
            </div>
        </div>

        <!-- Airtel -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-header d-flex justify-content-center align-items-center">
                    <div class="operator-icon">
                        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/a/a2/Airtel_%28Bangladesh%29_logo.png/120px-Airtel_%28Bangladesh%29_logo.png" alt="Airtel">
                    </div>
                    <span class="ms-2">Airtel</span>
                </div>
                <div class="card-body">
                    <p class="card-text">Total Loads:</p>
                    <span class="count-badge">{{ $airtel_pending }}</span>
                </div>
            </div>
        </div>

        <!-- Teletalk -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-header d-flex justify-content-center align-items-center">
                    <div class="operator-icon">
                        <img src="https://upload.wikimedia.org/wikipedia/en/3/3f/Teletalk_Bangladesh_Limited.svg" alt="Teletalk">
                    </div>
                    <span class="ms-2">Teletalk</span>
                </div>
                <div class="card-body">
                    <p class="card-text">Total Loads:</p>
                    <span class="count-badge">{{ $tt_pending }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<footer>
    &copy; <a href="https://iglweb.com">IGL Web Ltd</a> — All Rights Reserved
</footer>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let progress = 0;
const progressBar = document.getElementById('refresh-progress');

setInterval(() => {
    progress += 1;
    if(progress > 100) progress = 0;
    progressBar.style.width = progress + '%';
}, 100);

// Auto-refresh every 10 seconds
setTimeout(() => { location.reload(); }, 10000);
</script>
</body>
</html>

