<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($filename); ?> - IGL SMS Log Viewer</title>
    <style>
        *{box-sizing:border-box}body{margin:0;font-family:Arial,Helvetica,sans-serif;background:#f4f1e8;color:#222}.wrap{width:96%;max-width:1550px;margin:22px auto}.topbar{background:#efe2b6;border:2px solid #2f4f2f;border-radius:16px;padding:15px 18px;margin-bottom:16px;display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap}.topbar h1{margin:0;font-size:24px;word-break:break-all}.btn{display:inline-block;border:0;text-decoration:none;padding:9px 13px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer}.btn-back{background:#2f6f45;color:#fff}.btn-download{background:#315a8b;color:#fff}.filters{background:#fffdf8;border:1px solid #d9d3c7;border-radius:12px;padding:12px;margin-bottom:14px;display:flex;gap:10px;flex-wrap:wrap;align-items:center}.filters input,.filters select{padding:9px 10px;border:1px solid #bdb6aa;border-radius:8px;min-width:180px}.filters button{padding:9px 13px;border:0;border-radius:8px;background:#2d3b2d;color:#fff;font-weight:700;cursor:pointer}.reset{text-decoration:none;padding:9px 13px;background:#787878;color:#fff;border-radius:8px;font-weight:700;font-size:13px}.entry{background:#fff;border:1px solid #ddd;border-left-width:6px;border-radius:10px;margin-bottom:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,.05)}.entry-head{padding:10px 13px;display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;font-size:13px;font-weight:700;background:#fafafa;border-bottom:1px solid #eee}.entry-body{margin:0;padding:13px;white-space:pre-wrap;word-break:break-word;overflow-x:auto;font-family:Consolas,Monaco,monospace;font-size:13px;line-height:1.48;background:#111814;color:#e7f3e8;max-height:520px;overflow-y:auto}.level-error,.level-critical,.level-alert,.level-emergency{border-left-color:#c9302c}.level-warning{border-left-color:#d88712}.level-info,.level-notice{border-left-color:#2f8a4c}.level-debug{border-left-color:#4f6ea8}.level-unknown{border-left-color:#777}.badge{color:#fff;padding:3px 7px;border-radius:999px;text-transform:uppercase;font-size:11px}.badge-error,.badge-critical,.badge-alert,.badge-emergency{background:#c9302c}.badge-warning{background:#d88712}.badge-info,.badge-notice{background:#2f8a4c}.badge-debug{background:#4f6ea8}.badge-unknown{background:#777}.empty{padding:40px 20px;text-align:center;color:#777;background:#fff;border-radius:12px}.auto{font-size:12px;color:#555}
    </style>
</head>
<body>
<div class="wrap">
    <div class="topbar">
        <div><h1><?php echo e($filename); ?></h1><div class="auto">Auto refresh: every 10 seconds</div></div>
        <div><a class="btn btn-back" href="<?php echo e(route('log-viewer.index')); ?>">Back</a> <a class="btn btn-download" href="<?php echo e(route('log-viewer.download', ['filename' => $filename])); ?>">Download</a></div>
    </div>

    <form class="filters" method="GET" action="<?php echo e(route('log-viewer.show', ['filename' => $filename])); ?>">
        <select name="level">
            <option value="">All levels</option>
            <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($item); ?>" <?php echo e($level === $item ? 'selected' : ''); ?>><?php echo e(strtoupper($item)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search log text">
        <button type="submit">Filter</button>
        <a class="reset" href="<?php echo e(route('log-viewer.show', ['filename' => $filename])); ?>">Reset</a>
    </form>

    <?php if(count($entries)): ?>
        <?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="entry level-<?php echo e($entry['level']); ?>">
                <div class="entry-head">
                    <div><span class="badge badge-<?php echo e($entry['level']); ?>"><?php echo e(strtoupper($entry['level'])); ?></span>&nbsp; <?php echo e($entry['date']); ?></div>
                    <div><?php echo e($entry['environment']); ?></div>
                </div>
                <pre class="entry-body"><?php echo e($entry['content']); ?></pre>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="empty">No matching log entries found.</div>
    <?php endif; ?>
</div>
<script>setTimeout(function(){window.location.reload();},10000);</script>
</body>
</html>
