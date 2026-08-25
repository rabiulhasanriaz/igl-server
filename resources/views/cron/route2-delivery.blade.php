<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>R2 Report</title>
</head>
<body>
    
    <h1 class="text-center"><span class="badge badge-primary">Pending Data Fetching</span></h1>
    {{--@php
      $total = $delivered + $failed +  $sentButNotRecieve +  $notEnableDeliver;
    @endphp--}}
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Cron Work
                  <span class="badge badge-primary badge-pill" style="font-size: 20px;">{{ $result }}</span>
                </li>
                {{--<li class="list-group-item d-flex justify-content-between align-items-center">
                  Total Delivered
                  <span class="badge badge-success badge-pill" style="font-size: 20px;">{{ $delivered }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Total Failed
                  <span class="badge badge-danger badge-pill" style="font-size: 20px;">{{ $failed }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Total Sent But Not Recieve
                  <span class="badge badge-warning badge-pill" style="font-size: 20px;">{{ $sentButNotRecieve }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Total Not Enable Deliver
                  <span class="badge badge-info badge-pill" style="font-size: 20px;">{{ $notEnableDeliver }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Total
                  <span class="badge badge-info badge-pill" style="font-size: 20px;">{{ $total }}</span>
                </li>--}}
              </ul>
        </div>
    </div>
    


      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>