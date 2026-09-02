<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IGL SMS Sending Cron</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root{
            --ink:#26351d;
            --green:#4f7f2a;
            --green-dark:#315516;
            --jade:#6c963d;
            --orange:#c94e17;
            --gold:#d9a928;
            --paper:#f8f1df;
            --paper-2:#fffaf0;
            --line:#c9b88f;
            --danger:#a91f1f;
            --success:#2f7c2f;
            --terminal:#11150f;
        }

        *{box-sizing:border-box}

        body{
            margin:0;
            color:var(--ink);
            font-family:Georgia,"Times New Roman",serif;
            background:
                linear-gradient(rgba(110,91,52,.035) 1px,transparent 1px),
                linear-gradient(90deg,rgba(110,91,52,.035) 1px,transparent 1px),
                #efe5cd;
            background-size:34px 34px;
        }

        .page-frame{
            position:relative;
            min-height:100vh;
            overflow:hidden;
        }

        .bamboo{
            position:fixed;
            top:0;
            bottom:0;
            width:36px;
            z-index:1;
            opacity:.55;
            pointer-events:none;
            background:
                repeating-linear-gradient(
                    to bottom,
                    #6f8f33 0 45px,
                    #9ab55b 45px 50px,
                    #587925 50px 95px,
                    #a5c268 95px 100px
                );
            border-left:2px solid rgba(54,83,21,.35);
            border-right:2px solid rgba(54,83,21,.35);
        }
        .bamboo.left{left:0}
        .bamboo.right{right:0}

        .hero{
            position:relative;
            z-index:2;
            min-height:220px;
            padding:30px 40px 24px;
            background:
                radial-gradient(circle at 18% 35%,rgba(255,255,255,.65) 0 85px,transparent 86px),
                linear-gradient(180deg,#fff9eb,#efe2c5);
            border-bottom:6px solid #6d4b28;
            box-shadow:0 7px 18px rgba(66,45,20,.15);
            overflow:hidden;
        }

        .hero::after{
            content:"";
            position:absolute;
            left:0;
            right:0;
            bottom:0;
            height:10px;
            background:repeating-linear-gradient(
                90deg,
                #78512b 0 38px,
                #96663c 38px 42px
            );
        }

        .mountains{
            position:absolute;
            inset:auto 0 0 0;
            height:105px;
            opacity:.12;
            background:
                linear-gradient(145deg,transparent 20%,#49614d 21% 42%,transparent 43%) 0 100%/240px 100% repeat-x;
        }

        .hero-inner{
            position:relative;
            z-index:2;
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:165px;
            text-align:center;
        }

        .main-title{
            margin:0;
            font-family:"Trebuchet MS",Arial,sans-serif;
            font-weight:1000;
            text-transform:uppercase;
            letter-spacing:.04em;
            color:#1d2119;
            font-size:clamp(2rem,4.5vw,4rem);
            text-shadow:0 2px 0 #fff4cd;
        }

        .main-title .sms{
            color:var(--orange);
        }

        .sub-title{
            display:inline-flex;
            align-items:center;
            gap:10px;
            margin-top:12px;
            color:var(--green-dark);
            font-weight:800;
            font-size:1.1rem;
        }

        .sub-title::before,
        .sub-title::after{
            content:"";
            width:95px;
            height:2px;
            background:linear-gradient(90deg,transparent,var(--green-dark));
        }
        .sub-title::after{
            background:linear-gradient(90deg,var(--green-dark),transparent);
        }

        .igl-brand{
            position:absolute;
            right:48px;
            top:24px;
            color:var(--green-dark);
            font-family:"Trebuchet MS",Arial,sans-serif;
            font-weight:900;
            font-size:1.7rem;
            text-align:center;
        }
        .igl-brand small{
            display:block;
            font-size:.62rem;
            font-weight:700;
        }

        .panda{
            position:absolute;
            z-index:3;
            width:150px;
            bottom:8px;
        }
        .panda.left{left:65px}
        .panda.right{right:65px}

        .content-wrap{
            position:relative;
            z-index:2;
            max-width:1420px;
            margin:0 auto;
            padding:26px 50px 0;
        }

        .stats-grid{
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:16px;
            margin-bottom:18px;
        }

        .stat-card,
        .panel,
        .terminal-shell,
        .error-banner{
            background:
                linear-gradient(180deg,rgba(255,255,255,.35),rgba(255,255,255,.05)),
                var(--paper);
            border:2px solid var(--line);
            border-radius:10px;
            box-shadow:0 5px 12px rgba(71,52,27,.10);
        }

        .stat-card{
            padding:15px 18px;
            display:flex;
            align-items:center;
            gap:14px;
        }

        .stat-icon{
            width:46px;
            height:46px;
            border-radius:50%;
            display:grid;
            place-items:center;
            color:white;
            background:var(--green);
            border:3px solid #37551f;
            font-size:1.2rem;
        }

        .stat-label{
            font-size:.75rem;
            font-weight:800;
            letter-spacing:.06em;
            text-transform:uppercase;
            color:#5d604b;
        }

        .stat-value{
            margin-top:4px;
            color:var(--success);
            font-family:"Trebuchet MS",Arial,sans-serif;
            font-size:1.12rem;
            font-weight:1000;
        }

        .terminal-shell{
            width:92%;
            margin:0 auto 20px;
            overflow:hidden;
            border-color:#423d2e;
            background:var(--terminal);
            color:#effbd9;
        }

        .terminal-topbar{
            display:flex;
            justify-content:space-between;
            gap:12px;
            align-items:center;
            padding:9px 13px;
            background:#191e16;
            border-bottom:1px solid #3a4932;
            font-family:Consolas,"Courier New",monospace;
            font-size:.78rem;
            color:#8ac45a;
        }

        .terminal-dots{display:flex;gap:7px}
        .terminal-dot{
            width:10px;height:10px;border-radius:50%;
        }
        .terminal-dot.red{background:#d9573f}
        .terminal-dot.yellow{background:#e7b441}
        .terminal-dot.green{background:#82b73e}

        .terminal-body{
            padding:14px 16px 15px;
            font-family:Consolas,"Courier New",monospace;
            font-size:.9rem;
        }

        .terminal-prompt{
            color:#8fdc45;
            font-weight:800;
        }

        .terminal-muted{
            margin-top:7px;
            color:#d7ddc9;
        }

        .progress-shell{
            margin-top:13px;
            height:9px;
            background:#24301c;
            border:1px solid #4c612e;
            overflow:hidden;
        }

        .progress-run{
            width:0;
            height:100%;
            background:repeating-linear-gradient(
                90deg,
                #6eba35,
                #6eba35 14px,
                #92d75a 14px,
                #92d75a 28px
            );
            animation:refresh5 5s linear infinite;
        }

        @keyframes refresh5{
            from{width:0}
            to{width:100%}
        }

        .error-banner{
            padding:12px 14px;
            margin-bottom:12px;
            border-color:#c36a59;
            color:#8d231b;
            background:#fff0e7;
            font-weight:800;
        }

        .panel{
            overflow:hidden;
            height:100%;
        }

        .panel-head{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            padding:13px 16px;
            border-bottom:2px solid var(--line);
            font-family:"Trebuchet MS",Arial,sans-serif;
            font-weight:1000;
            letter-spacing:.05em;
            text-transform:uppercase;
        }

        .panel.mask .panel-head{
            background:linear-gradient(135deg,#ca551c,#9f3210);
            color:white;
        }

        .panel.nonmask .panel-head{
            background:linear-gradient(135deg,#72963b,#4c6c24);
            color:white;
        }

        .panel-state{
            background:#f8f0d8;
            color:#355d1b;
            border:1px solid rgba(255,255,255,.55);
            padding:4px 10px;
            border-radius:4px;
            font-size:.73rem;
        }

        .info-line{
            margin:12px;
            padding:9px 11px;
            color:#9e4513;
            background:#fff3cf;
            border:1px solid #dfc583;
            border-radius:6px;
            font-weight:700;
        }

        .operator-row{
            padding:11px 14px;
            border-bottom:1px solid #ded1b2;
            background:rgba(255,250,240,.62);
        }
        .operator-row:last-child{border-bottom:0}

        .operator-row:hover{
            background:#fff6dc;
        }

        .operator-info{
            display:flex;
            align-items:center;
            gap:11px;
        }

        .operator-icon{
            width:38px;
            height:38px;
            min-width:38px;
            display:grid;
            place-items:center;
            background:#fffdf5;
            border:1px solid #d6c79f;
            border-radius:6px;
            overflow:hidden;
            color:var(--green-dark);
            font-family:"Trebuchet MS",Arial,sans-serif;
            font-weight:900;
        }

        .operator-icon img{
            max-width:86%;
            max-height:86%;
            object-fit:contain;
        }

        .operator-name{
            font-family:"Trebuchet MS",Arial,sans-serif;
            font-size:.88rem;
            font-weight:900;
            color:#232619;
        }

        .count-badge{
            max-width:55%;
            padding:6px 10px;
            border:1px solid #c6d79d;
            background:#f4f8df;
            color:#3d6c26;
            border-radius:5px;
            font-family:Consolas,"Courier New",monospace;
            font-size:.78rem;
            font-weight:800;
            text-align:right;
            word-break:break-word;
        }

        footer{
            position:relative;
            z-index:2;
            margin-top:24px;
            padding:22px 50px;
            background:#e7dac0;
            border-top:4px solid #654522;
            color:#514b3b;
        }

        footer .quote{
            text-align:center;
            font-style:italic;
            font-weight:700;
        }

        footer a{
            color:var(--green-dark);
            text-decoration:none;
            font-weight:900;
        }

        @media(max-width:1100px){
            .stats-grid{grid-template-columns:repeat(2,1fr)}
            .panda{width:115px}
            .panda.left{left:20px}
            .panda.right{right:20px}
            .igl-brand{right:20px}
        }

        @media(max-width:768px){
            .hero{min-height:235px;padding-inline:18px}
            .hero-inner{align-items:flex-start;padding-top:40px}
            .sub-title::before,.sub-title::after{width:35px}
            .panda{width:88px}
            .panda.left{left:8px}
            .panda.right{right:8px}
            .igl-brand{font-size:1.1rem}
            .content-wrap{padding:18px 20px 0}
            .stats-grid{grid-template-columns:1fr}
            .terminal-shell{width:100%}
            footer{padding:18px 20px}
            .count-badge{max-width:48%;font-size:.7rem}
        }
    
        /* ===== COMPACT DESKTOP LAYOUT: keep all operator options visible ===== */
        @media (min-width: 992px) {
            .hero {
                min-height: 145px;
                padding: 14px 34px 10px;
            }

            .hero-inner {
                min-height: 118px;
            }

            .main-title {
                font-size: clamp(2rem, 3.6vw, 3.25rem);
                line-height: 1;
            }

            .sub-title {
                margin-top: 7px;
                font-size: .95rem;
            }

            .sub-title::before,
            .sub-title::after {
                width: 70px;
            }

            .panda {
                width: 100px;
                bottom: 3px;
            }

            .panda.left {
                left: 42px;
            }

            .panda.right {
                right: 42px;
            }

            .igl-brand {
                top: 12px;
                right: 42px;
                font-size: 1.25rem;
            }

            .content-wrap {
                max-width: 1500px;
                padding: 14px 34px 0;
            }

            .stats-grid {
                gap: 10px;
                margin-bottom: 10px;
            }

            .stat-card {
                min-height: 66px;
                padding: 8px 12px;
                gap: 10px;
            }

            .stat-icon {
                width: 38px;
                height: 38px;
                min-width: 38px;
                font-size: 1rem;
                border-width: 2px;
            }

            .stat-label {
                font-size: .68rem;
            }

            .stat-value {
                margin-top: 1px;
                font-size: 1rem;
            }

            .terminal-shell {
                width: 88%;
                margin-bottom: 12px;
            }

            .terminal-topbar {
                padding: 6px 10px;
                font-size: .7rem;
            }

            .terminal-body {
                padding: 8px 12px 9px;
                font-size: .78rem;
            }

            .terminal-muted {
                margin-top: 3px;
            }

            .progress-shell {
                margin-top: 7px;
                height: 7px;
            }

            .row.g-4 {
                --bs-gutter-x: 1rem;
                --bs-gutter-y: 1rem;
            }

            .panel-head {
                padding: 8px 12px;
                font-size: .9rem;
            }

            .panel-state {
                padding: 3px 8px;
                font-size: .65rem;
            }

            .info-line {
                margin: 7px 10px;
                padding: 6px 9px;
                font-size: .78rem;
            }

            .operator-row {
                padding: 7px 10px;
                min-height: 52px;
            }

            .operator-info {
                gap: 8px;
            }

            .operator-icon {
                width: 31px;
                height: 31px;
                min-width: 31px;
            }

            .operator-name {
                font-size: .78rem;
            }

            .count-badge {
                max-width: 52%;
                padding: 4px 7px;
                font-size: .7rem;
            }

            footer {
                margin-top: 12px;
                padding: 10px 34px;
                font-size: .75rem;
            }

            footer .quote {
                font-size: .75rem;
            }
        }

        /* Short laptop screens (e.g. 768–850px content height) */
        @media (min-width: 992px) and (max-height: 850px) {
            .hero {
                min-height: 118px;
                padding-top: 8px;
                padding-bottom: 6px;
            }

            .hero-inner {
                min-height: 92px;
            }

            .main-title {
                font-size: 2.55rem;
            }

            .sub-title {
                font-size: .82rem;
                margin-top: 4px;
            }

            .panda {
                width: 82px;
            }

            .igl-brand {
                font-size: 1.05rem;
                top: 8px;
            }

            .content-wrap {
                padding-top: 9px;
            }

            .stats-grid {
                margin-bottom: 8px;
            }

            .stat-card {
                min-height: 56px;
                padding: 6px 10px;
            }

            .stat-icon {
                width: 32px;
                height: 32px;
                min-width: 32px;
            }

            .terminal-shell {
                margin-bottom: 8px;
            }

            .terminal-body {
                padding-top: 6px;
                padding-bottom: 7px;
            }

            .operator-row {
                min-height: 45px;
                padding-top: 5px;
                padding-bottom: 5px;
            }

            .operator-icon {
                width: 28px;
                height: 28px;
                min-width: 28px;
            }

            .panel-head {
                padding-top: 6px;
                padding-bottom: 6px;
            }

            footer {
                margin-top: 8px;
                padding-top: 7px;
                padding-bottom: 7px;
            }
        }

    
        /* ===== SLIGHTLY TALLER / LONGER LAYOUT ===== */
        @media (min-width: 992px) {
            .hero {
                min-height: 160px;
                padding-top: 18px;
                padding-bottom: 14px;
            }

            .hero-inner {
                min-height: 130px;
            }

            .panda {
                width: 110px;
            }

            .content-wrap {
                padding-top: 18px;
            }

            .stat-card {
                min-height: 74px;
                padding-top: 11px;
                padding-bottom: 11px;
            }

            .terminal-body {
                padding-top: 11px;
                padding-bottom: 12px;
            }

            .terminal-shell {
                width: 88%;
            }

            .panel-head {
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .operator-row {
                min-height: 58px;
                padding-top: 9px;
                padding-bottom: 9px;
            }

            .operator-icon {
                width: 34px;
                height: 34px;
                min-width: 34px;
            }

            footer {
                margin-top: 16px;
                padding-top: 13px;
                padding-bottom: 13px;
            }
        }

        @media (min-width: 992px) and (max-height: 850px) {
            .hero {
                min-height: 135px;
            }

            .hero-inner {
                min-height: 105px;
            }

            .panda {
                width: 92px;
            }

            .stat-card {
                min-height: 64px;
            }

            .operator-row {
                min-height: 51px;
                padding-top: 7px;
                padding-bottom: 7px;
            }
        }

    </style>
</head>

<body>
<div class="page-frame">
    <div class="bamboo left"></div>
    <div class="bamboo right"></div>

    @php
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

                return strlen($text) > 40 ? substr($text, 0, 40) . '...' : $text;
            }
        }
    @endphp

    <header class="hero">
        <div class="mountains"></div>

        <svg class="panda left" viewBox="0 0 180 180" aria-hidden="true">
            <circle cx="47" cy="41" r="22" fill="#151515"/>
            <circle cx="133" cy="41" r="22" fill="#151515"/>
            <circle cx="90" cy="80" r="58" fill="#f8f8f0" stroke="#222" stroke-width="5"/>
            <ellipse cx="67" cy="72" rx="18" ry="25" fill="#171717" transform="rotate(22 67 72)"/>
            <ellipse cx="113" cy="72" rx="18" ry="25" fill="#171717" transform="rotate(-22 113 72)"/>
            <circle cx="68" cy="72" r="6" fill="#fff"/>
            <circle cx="112" cy="72" r="6" fill="#fff"/>
            <circle cx="90" cy="93" r="7" fill="#111"/>
            <path d="M70 112c12 12 28 12 40 0" fill="none" stroke="#222" stroke-width="5" stroke-linecap="round"/>
            <path d="M47 130c-11 15-16 31-18 46h122c-3-21-10-37-22-49-16 15-61 17-82 3z" fill="#1c1c1c"/>
            <path d="M26 118l-20 20" stroke="#1c1c1c" stroke-width="17" stroke-linecap="round"/>
            <path d="M154 115l18-25" stroke="#1c1c1c" stroke-width="17" stroke-linecap="round"/>
            <circle cx="171" cy="86" r="8" fill="#1c1c1c"/>
        </svg>

        <div class="hero-inner">
            <div>
                <h1 class="main-title">IGL <span class="sms">SMS</span> Sending Cron</h1>
                <div class="sub-title">
                    <i class="bi bi-arrow-through-heart-fill"></i>
                    Live Monitoring Dashboard
                </div>
            </div>
        </div>

        

        <svg class="panda right" viewBox="0 0 180 180" aria-hidden="true">
            <circle cx="47" cy="41" r="22" fill="#151515"/>
            <circle cx="133" cy="41" r="22" fill="#151515"/>
            <circle cx="90" cy="80" r="58" fill="#f8f8f0" stroke="#222" stroke-width="5"/>
            <ellipse cx="67" cy="72" rx="18" ry="25" fill="#171717" transform="rotate(22 67 72)"/>
            <ellipse cx="113" cy="72" rx="18" ry="25" fill="#171717" transform="rotate(-22 113 72)"/>
            <circle cx="68" cy="72" r="6" fill="#fff"/>
            <circle cx="112" cy="72" r="6" fill="#fff"/>
            <circle cx="90" cy="93" r="7" fill="#111"/>
            <path d="M70 112c12 12 28 12 40 0" fill="none" stroke="#222" stroke-width="5" stroke-linecap="round"/>
            <path d="M48 131c-12 13-18 29-20 45h124c-3-20-11-37-23-49-18 15-60 17-81 4z" fill="#1c1c1c"/>
            <path d="M31 121l-17-8" stroke="#1c1c1c" stroke-width="17" stroke-linecap="round"/>
            <path d="M151 118l20 10" stroke="#1c1c1c" stroke-width="17" stroke-linecap="round"/>
        </svg>
    </header>

    <main class="content-wrap">

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-wifi"></i></div>
                <div>
                    <div class="stat-label">Node Status</div>
                    <div class="stat-value">ONLINE</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
                <div>
                    <div class="stat-label">Refresh Cycle</div>
                    <div class="stat-value">5 SEC</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div class="stat-label">Mask Engine</div>
                    <div class="stat-value">ACTIVE</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-fan"></i></div>
                <div>
                    <div class="stat-label">Non-Mask Engine</div>
                    <div class="stat-value">ACTIVE</div>
                </div>
            </div>
        </div>

        @if(!empty($returnError['errorNotify']))
            <div class="error-banner">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>CORE ERROR:</strong> {{ $returnError['errorNotify'] }}
            </div>
        @endif

        @if(!empty($returnError['gpError']))
            <div class="error-banner"><strong>GP ERROR:</strong> {{ $returnError['gpError'] }}</div>
        @endif

        @if(!empty($returnError['blError']))
            <div class="error-banner"><strong>BANGLALINK ERROR:</strong> {{ $returnError['blError'] }}</div>
        @endif

        @if(!empty($returnError['robiAirtelError']))
            <div class="error-banner"><strong>ROBI/AIRTEL ERROR:</strong> {{ $returnError['robiAirtelError'] }}</div>
        @endif

        @if(!empty($returnError['ttError']))
            <div class="error-banner"><strong>TELETALK ERROR:</strong> {{ $returnError['ttError'] }}</div>
        @endif

        <section class="terminal-shell">
            <div class="terminal-topbar">
                <div class="terminal-dots">
                    <span class="terminal-dot red"></span>
                    <span class="terminal-dot yellow"></span>
                    <span class="terminal-dot green"></span>
                </div>
                <div>root@igl-server:/cron/sms-monitor</div>
            </div>

            <div class="terminal-body">
                <div class="terminal-prompt">
                    root@igl-server:~$ watch --interval=5 sms:dispatch
                </div>
                <div class="terminal-muted">
                    Auto-refresh enabled. Monitoring masking and non-masking SMS pipelines...
                </div>
                <div class="progress-shell">
                    <div class="progress-run"></div>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-lg-6">
                <section class="panel mask">
                    <div class="panel-head">
                        <span><i class="bi bi-mask me-2"></i>Mask SMS</span>
                        <span class="panel-state">[ ACTIVE ]</span>
                    </div>

                    @if(isset($returnData['message']))
                        <div class="info-line">{{ $returnData['message'] }}</div>
                    @endif

                    <div class="operator-row">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="operator-info">
                                <div class="operator-icon">
                                    <img src="https://www.robi.com.bd/_next/static/media/robi-logo-2.d08ae93d.svg" alt="Robi">
                                </div>
                                <div class="operator-icon">
                                    <img src="https://www.bd.airtel.com/_next/static/media/airtel-logo.7cd96f91.svg" alt="Airtel">
                                </div>
                                <div class="operator-name">ROBI / AIRTEL</div>
                            </div>
                            <div class="count-badge">
                                {{ shortSmsStatus($returnData['robi_airtel'] ?? 'N/A') }}
                            </div>
                        </div>
                    </div>

                    <div class="operator-row">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="operator-info">
                                <div class="operator-icon">
                                    <img src="https://banglalink.net/logo.svg" alt="Banglalink">
                                </div>
                                <div class="operator-name">BANGLALINK</div>
                            </div>
                            <div class="count-badge">
                                {{ shortSmsStatus($returnData['banglalink'] ?? 'N/A') }}
                            </div>
                        </div>
                    </div>

                    <div class="operator-row">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="operator-info">
                                <div class="operator-icon">GP</div>
                                <div class="operator-name">GRAMEENPHONE</div>
                            </div>
                            <div class="count-badge">
                                {{ shortSmsStatus($returnData['gp'] ?? 'N/A') }}
                            </div>
                        </div>
                    </div>

                    <div class="operator-row">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="operator-info">
                                <div class="operator-icon">
                                    <img src="https://upload.wikimedia.org/wikipedia/en/thumb/3/3f/Teletalk_Bangladesh_Limited.svg/260px-Teletalk_Bangladesh_Limited.svg.png?20160320162058" alt="Teletalk">
                                </div>
                                <div class="operator-name">TELETALK</div>
                            </div>
                            <div class="count-badge">
                                {{ shortSmsStatus($returnData['teletalk'] ?? 'N/A') }}
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-6">
                <section class="panel nonmask">
                    <div class="panel-head">
                        <span><i class="bi bi-fan me-2"></i>Non-Mask SMS</span>
                        <span class="panel-state">[ ACTIVE ]</span>
                    </div>

                    <div class="operator-row">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="operator-info">
                                <div class="operator-icon">
                                    <img src="https://www.robi.com.bd/_next/static/media/robi-logo-2.d08ae93d.svg" alt="Robi">
                                </div>
                                <div class="operator-name">ROBI</div>
                            </div>
                            <div class="count-badge">
                                {{ shortSmsStatus($retTextRobi ?? 'N/A') }}
                            </div>
                        </div>
                    </div>

                    <div class="operator-row">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="operator-info">
                                <div class="operator-icon">GP</div>
                                <div class="operator-name">GRAMEENPHONE</div>
                            </div>
                            <div class="count-badge">
                                {{ shortSmsStatus($retTextGp ?? 'N/A') }}
                            </div>
                        </div>
                    </div>

                    <div class="operator-row">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="operator-info">
                                <div class="operator-icon">
                                    <img src="https://banglalink.net/logo.svg" alt="Banglalink">
                                </div>
                                <div class="operator-name">BANGLALINK</div>
                            </div>
                            <div class="count-badge">
                                {{ shortSmsStatus($retTextBl ?? 'N/A') }}
                            </div>
                        </div>
                    </div>

                    <div class="operator-row">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="operator-info">
                                <div class="operator-icon">
                                    <img src="https://iglgroup.org/media/logos/IGL_logo_PNG_WITH_LOGO-01.png" alt="IP TSP">
                                </div>
                                <div class="operator-name">IP TSP</div>
                            </div>
                            <div class="count-badge">
                                {{ shortSmsStatus($retTextIptsp ?? 'N/A') }}
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

    </main>

    <footer>
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <strong>root@igl-server:</strong> session active
            </div>
            <div class="col-md-4 quote">
                “There is no secret ingredient. Keep improving.”
            </div>
            <div class="col-md-4 text-md-end">
                &copy; {{ date('Y') }} <a href="https://iglweb.com">IGL Web</a>. All rights reserved.
            </div>
        </div>
    </footer>
</div>

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
