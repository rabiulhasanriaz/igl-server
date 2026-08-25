<!DOCTYPE html>
<html lang="en">
<head>
    <title>Flexiload Report ... </title>
    <meta http-equiv="refresh" content="60">
</head>
<body>
<div style="margin-top: 100px;">
    <div style="text-align: center">
        <h1>...Fetching Delivery Report...</h1>
    </div>

    @if(!empty($returnData))
        @foreach($returnData as $index => $data)
            <p>{{ strtoupper(implode(" ", explode("_", $index))) }} => {{ $data }}</p>
        @endforeach
    @endif

</div>
</body>
</html>
