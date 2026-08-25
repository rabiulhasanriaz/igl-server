<!DOCTYPE html>
<html>
<head>
    <title>Data Cron</title>
    <meta http-equiv="refresh" content="5">
</head>
<body>

<h2 style="text-align: center;">Data Passing </h2>

<span>This page is refreshing 30 seconds to passing data.<b style="color: red;">Dont close it.</b></span>
<p>----------------------------------</p>
<p>
    @if(is_array(@$retText))
        @foreach($retText as $text)
            {!! $text !!}
        @endforeach
    @else
        {!! @$retText !!}
    @endif
</p>
</body>
</html>
