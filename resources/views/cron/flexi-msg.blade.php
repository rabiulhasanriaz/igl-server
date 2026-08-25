<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="2">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="IGL Web Ltd">
    <meta name="generator" content="">
    <title>IGL Flexi Message</title>
    <link href="{{ asset('assets/cron') }}/css/bootstrap.min.css" rel="stylesheet" >
    <link href="{{ asset('assets/cron') }}/css/sms_tem.css" rel="stylesheet" >
    <meta name="msapplication-config" content="/docs/4.4/assets/img/favicons/browserconfig.xml">
    <!-- <meta name="theme-color" content="#563d7c"> -->
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
    <!-- Custom styles for this template -->
    <!-- <link href="css/album.css" rel="stylesheet"> -->
</head>
<body>
<header>
    <div class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container d-flex justify-content-between">
                <h3 style="color: white;"><b>Flexiload Data Fethcing</b></h3>
        </div>
    </div>
</header>

<main role="main">

    <section class="pricing py-5">
        <div class="container">
            <div class="alert alert-danger" role="alert">
                This page is refreshing to store flexiload Trx and Balance. Dont close it!.
            </div>
            <div class="row">
                <!-- Free Tier -->
                <div class="col-lg-12">
                    <div class="card mb-10">
                        <div class="card-body">
                            <h5 class="card-title text-uppercase text-center">STATUS</h5>
                            <hr>
                            <ul class="fa-ul">
                                <li>API STATUS => <span style="color:red; font-weight:bold;">{{ $status }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<footer class="">
    <div class="container">
        <!-- <p class="float-right">
          <a href="#">Back to top</a>
        </p> -->
        <p>Copyright &copy; <a href="https://iglweb.com">IGL Web Ltd</a> All Right Reserved</p>
    </div>
</footer>
<script src="{{ asset('assets/cron') }}/js/jquery.slim.min.js"></script>
<script src="{{ asset('assets/cron') }}/js/bootstrap.bundle.min.js"></script></body>
</html>
