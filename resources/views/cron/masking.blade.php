<!DOCTYPE html>
<html>
<head>
    <title>Masking Cron || Bulk Sms</title>
    <meta http-equiv="refresh" content="10">
</head>
<body>

<h2 style="text-align: center;">Message Sending .... </h2>

<span>This page is refreshing 30 seconds to send SMS.<b style="color: red;">Dont close it.</b></span>
<p>----------------------------------</p>


<h3>Messages</h3>
<p style="font-size: 20px;">
    @if(@$returnData==true)
        @foreach($returnData as $data=>$value)
            {{ $value }}<br>
        @endforeach
    @endif
</p>
<br><br><br><br>

@if(@$returnError==true)
    <h3>Errors</h3>
    <p style="font-size: 20px;">
        @foreach($returnError as $data=>$error)
            {{ $error }}<br>
        @endforeach
    </p>
@endif
</body>
</html>
