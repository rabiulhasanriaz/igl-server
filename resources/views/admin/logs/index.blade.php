<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGL SMS Log Viewer</title>
    <style>
        *{box-sizing:border-box}body{margin:0;font-family:Arial,Helvetica,sans-serif;background:#f4f1e8;color:#202020}.wrap{width:94%;max-width:1400px;margin:28px auto}.hero{background:#efe2b6;border:2px solid #2f4f2f;border-radius:18px;padding:18px 22px;box-shadow:0 8px 22px rgba(0,0,0,.1);margin-bottom:20px}.hero h1{margin:0 0 6px;font-size:30px}.hero p{margin:0;color:#4f4f4f}.flash{padding:12px 14px;border-radius:10px;margin-bottom:14px;font-weight:700}.flash-success{background:#e6f4ea;border:1px solid #79b889;color:#1d5f2e}.flash-error{background:#fde9e7;border:1px solid #d9877d;color:#92251d}.card{background:#fffdf8;border:1px solid #d8d2c5;border-radius:14px;overflow:hidden;box-shadow:0 6px 18px rgba(0,0,0,.06)}table{width:100%;border-collapse:collapse}th,td{padding:14px 16px;border-bottom:1px solid #ece7dd;text-align:left}th{background:#2d3b2d;color:#fff;font-size:14px}tr:hover td{background:#faf7ef}.file-name{font-weight:700;word-break:break-all}.actions{display:flex;gap:8px;flex-wrap:wrap}.btn{display:inline-block;border:0;text-decoration:none;padding:8px 12px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer}.btn-view{background:#2f6f45;color:#fff}.btn-download{background:#315a8b;color:#fff}.btn-clear{background:#a66a16;color:#fff}.btn-delete{background:#a52720;color:#fff}.empty{padding:34px;text-align:center;color:#777}@media(max-width:800px){.wrap{width:96%}table,thead,tbody,th,td,tr{display:block}thead{display:none}tr{border-bottom:1px solid #ddd;padding:10px 0}td{border:0;padding:7px 14px}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="hero"><h1>IGL SMS Log Viewer</h1><p>Laravel logs from <code>storage/logs</code></p></div>

    @if(session('success'))<div class="flash flash-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash flash-error">{{ session('error') }}</div>@endif

    <div class="card">
        @if(count($logs))
            <table>
                <thead><tr><th>Log file</th><th>Size</th><th>Last modified</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td class="file-name">{{ $log['name'] }}</td>
                        <td>{{ $log['size'] }}</td>
                        <td>{{ $log['modified'] }}</td>
                        <td><div class="actions">
                            <a class="btn btn-view" href="{{ route('log-viewer.show', ['filename' => $log['name']]) }}">View</a>
                            <a class="btn btn-download" href="{{ route('log-viewer.download', ['filename' => $log['name']]) }}">Download</a>
                            <form method="POST" action="{{ route('log-viewer.clear', ['filename' => $log['name']]) }}" onsubmit="return confirm('Clear this log file?');">{{ csrf_field() }}<button class="btn btn-clear" type="submit">Clear</button></form>
                            <form method="POST" action="{{ route('log-viewer.delete', ['filename' => $log['name']]) }}" onsubmit="return confirm('Delete this log file?');">{{ csrf_field() }}{{ method_field('DELETE') }}<button class="btn btn-delete" type="submit">Delete</button></form>
                        </div></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No .log files found in storage/logs.</div>
        @endif
    </div>
</div>
</body>
</html>
