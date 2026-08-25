<!DOCTYPE html>
<html>
<head>
    <title>Non-Masking Delivery Report || Bulk Sms</title>
    <meta http-equiv="refresh" content="15">
</head>
<body>

<h2 style="text-align: center;">Fetch delivery report .... </h2>
<br>
@if(isset($returnData['no_number']))
    {{ $returnData['no_number'] }}
@else
    Still Pending after check = {{$returnData['still_pending']}}
    <br>
    check complete? = {{$returnData['check_complete']}}
    <br><br>
@endif
</body>
</html>
