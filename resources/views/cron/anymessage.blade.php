<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>IGL Masking/Non-Masking Cron</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2074b5;
        }

        footer {
            background-color: #2074b5;
            color: #fff;
            padding: 1rem;
            font-size: 0.9rem;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        footer a {
            color: #fff;
        }

        main {
            padding-bottom: 60px;
        }

        .api-status {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container d-flex justify-content-center">
            <h3 class="navbar-brand mb-0">SMS Sending Cron</h3>
        </div>
    </nav>
</header>

<main role="main">
    <section class="pricing py-5">
        <div class="container">
            <div class="alert alert-danger" role="alert">
                This page is refreshing every 10 seconds to send SMS. Do not close it.
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card mb-5">
                        <div class="card-body">
                            <h5 class="card-title text-center">MASK/NON MASK CRON</h5>
                            <hr>
                            @if(count($returnData['sms_sent']) > 0)
                                <ul class="list-unstyled">
                                    @foreach ($returnData['sms_sent'] as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No SMS messages sent yet.</p>
                            @endif

                            @if(count($returnError['sms_failed']) > 0)
                                <ul class="list-unstyled text-danger">
                                    @foreach ($returnError['sms_failed'] as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

              
            </div>
        </div>
    </section>
</main>

<footer class="bg-dark text-white text-center py-3">
    <div class="container">
        <p>&copy; <a href="https://iglweb.com" class="text-white">IGL Web Ltd</a> All Rights Reserved</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Refresh the page after it has completely loaded and executed the cron data
    window.addEventListener('load', function () {
        setTimeout(function () {
            location.reload();
        }, 10000); // Refresh after 30 seconds
    });
</script>
</body>
</html>
