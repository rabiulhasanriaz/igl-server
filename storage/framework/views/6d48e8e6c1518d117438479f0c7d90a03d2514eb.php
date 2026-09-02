<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo e($filename); ?> - IGL SMS Log Viewer</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f1e8;
            color: #222;
        }

        .wrap {
            width: 96%;
            max-width: 1550px;
            margin: 22px auto;
        }

        .topbar {
            background: #efe2b6;
            border: 2px solid #2f4f2f;
            border-radius: 16px;
            padding: 15px 18px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
        }

        .topbar h1 {
            margin: 0;
            font-size: 24px;
            word-break: break-all;
        }

        .btn {
            display: inline-block;
            border: 0;
            text-decoration: none;
            padding: 9px 13px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-back {
            background: #2f6f45;
            color: #fff;
        }

        .btn-download {
            background: #315a8b;
            color: #fff;
        }

        .flash {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 14px;
            font-weight: 700;
        }

        .flash-success {
            background: #e6f4ea;
            border: 1px solid #79b889;
            color: #1d5f2e;
        }

        .flash-error {
            background: #fde9e7;
            border: 1px solid #d9877d;
            color: #92251d;
        }

        /*
        |--------------------------------------------------------------------------
        | Sticky Tabs
        |--------------------------------------------------------------------------
        */

        .tabs {
            display: flex;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 10px 0;
            margin-bottom: 15px;
            background: #f4f1e8;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 14px 18px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            background: #fff;
            border: 2px solid #ddd;
            color: #444;
            transition: 0.2s;
        }

        .tab:hover {
            transform: translateY(-1px);
        }

        .tab-error {
            border-color: #c9302c;
            color: #c9302c;
        }

        .tab-error.active {
            background: #c9302c;
            color: #fff;
        }

        .tab-success {
            border-color: #2f8a4c;
            color: #2f8a4c;
        }

        .tab-success.active {
            background: #2f8a4c;
            color: #fff;
        }

        .tab-count {
            display: inline-block;
            margin-left: 6px;
            padding: 2px 8px;
            border-radius: 20px;
            background: rgba(0, 0, 0, 0.12);
            font-size: 12px;
        }

        .active .tab-count {
            background: rgba(255, 255, 255, 0.25);
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        .filters {
            background: #fffdf8;
            border: 1px solid #d9d3c7;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filters input {
            padding: 9px 10px;
            border: 1px solid #bdb6aa;
            border-radius: 8px;
            min-width: 250px;
            flex: 1;
        }

        .filters button {
            padding: 9px 13px;
            border: 0;
            border-radius: 8px;
            background: #2d3b2d;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .reset {
            text-decoration: none;
            padding: 9px 13px;
            background: #787878;
            color: #fff;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
        }

        /*
        |--------------------------------------------------------------------------
        | Log Entries
        |--------------------------------------------------------------------------
        */

        .entry {
            background: #fff;
            border: 1px solid #ddd;
            border-left-width: 6px;
            border-radius: 10px;
            margin-bottom: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
        }

        .entry-head {
            padding: 10px 13px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            font-size: 13px;
            font-weight: 700;
            background: #fafafa;
            border-bottom: 1px solid #eee;
        }

        .entry-head-left,
        .entry-head-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .entry-body {
            margin: 0;
            padding: 13px;
            white-space: pre-wrap;
            word-break: break-word;
            overflow-x: auto;
            font-family: Consolas, Monaco, monospace;
            font-size: 13px;
            line-height: 1.48;
            background: #111814;
            color: #e7f3e8;
            max-height: 520px;
            overflow-y: auto;
        }

        .level-error,
        .level-critical,
        .level-alert,
        .level-emergency {
            border-left-color: #c9302c;
        }

        .level-warning {
            border-left-color: #d88712;
        }

        .level-info,
        .level-notice {
            border-left-color: #2f8a4c;
        }

        .level-debug {
            border-left-color: #4f6ea8;
        }

        .badge {
            color: #fff;
            padding: 3px 7px;
            border-radius: 999px;
            text-transform: uppercase;
            font-size: 11px;
        }

        .badge-error,
        .badge-critical,
        .badge-alert,
        .badge-emergency {
            background: #c9302c;
        }

        .badge-warning {
            background: #d88712;
        }

        .badge-info,
        .badge-notice {
            background: #2f8a4c;
        }

        .badge-debug {
            background: #4f6ea8;
        }

        /*
        |--------------------------------------------------------------------------
        | Delete One Entry
        |--------------------------------------------------------------------------
        */

        .delete-entry-form {
            margin: 0;
        }

        .btn-delete-entry {
            border: 0;
            background: #b42318;
            color: #fff;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-delete-entry:hover {
            background: #8f1b13;
        }

        .empty {
            padding: 40px 20px;
            text-align: center;
            color: #777;
            background: #fff;
            border-radius: 12px;
        }

        .auto {
            font-size: 12px;
            color: #555;
        }

    </style>

</head>

<body>

<div class="wrap">

    <div class="topbar">

        <div>

            <h1><?php echo e($filename); ?></h1>

            <div class="auto">
                Latest log data first • Auto refresh every 10 seconds
            </div>

        </div>

        <div>

            <a
                class="btn btn-back"
                href="<?php echo e(route('log-viewer.index')); ?>"
            >
                Back
            </a>

            <a
                class="btn btn-download"
                href="<?php echo e(route('log-viewer.download', ['filename' => $filename])); ?>"
            >
                Download
            </a>

        </div>

    </div>


    <?php if(session('success')): ?>

        <div class="flash flash-success">
            <?php echo e(session('success')); ?>

        </div>

    <?php endif; ?>


    <?php if(session('error')): ?>

        <div class="flash flash-error">
            <?php echo e(session('error')); ?>

        </div>

    <?php endif; ?>


    <div class="tabs">

        <a
            class="tab tab-error <?php echo e($tab === 'error' ? 'active' : ''); ?>"
            href="<?php echo e(route('log-viewer.show', [
                'filename' => $filename,
                'tab' => 'error'
            ])); ?>"
        >

            Errors

            <span class="tab-count">
                <?php echo e(count($errorEntries)); ?>

            </span>

        </a>


        <a
            class="tab tab-success <?php echo e($tab === 'success' ? 'active' : ''); ?>"
            href="<?php echo e(route('log-viewer.show', [
                'filename' => $filename,
                'tab' => 'success'
            ])); ?>"
        >

            Success

            <span class="tab-count">
                <?php echo e(count($successEntries)); ?>

            </span>

        </a>

    </div>


    <form
        class="filters"
        method="GET"
        action="<?php echo e(route('log-viewer.show', ['filename' => $filename])); ?>"
    >

        <input
            type="hidden"
            name="tab"
            value="<?php echo e($tab); ?>"
        >

        <input
            type="text"
            name="search"
            value="<?php echo e($search); ?>"
            placeholder="Search log text"
        >

        <button type="submit">
            Search
        </button>

        <a
            class="reset"
            href="<?php echo e(route('log-viewer.show', [
                'filename' => $filename,
                'tab' => $tab
            ])); ?>"
        >
            Reset
        </a>

    </form>


    <?php

        if ($tab === 'success') {
            $entries = $successEntries;
        } else {
            $entries = $errorEntries;
        }

    ?>


    <?php if(count($entries)): ?>

        <?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <div class="entry level-<?php echo e($entry['level']); ?>">

                <div class="entry-head">

                    <div class="entry-head-left">

                        <span class="badge badge-<?php echo e($entry['level']); ?>">
                            <?php echo e(strtoupper($entry['level'])); ?>

                        </span>

                        <span>
                            <?php echo e($entry['date']); ?>

                        </span>

                        <span>
                            <?php echo e($entry['environment']); ?>

                        </span>

                    </div>

                    <div class="entry-head-right">

                        <?php if(!empty($entry['id'])): ?>

                            <form
                                class="delete-entry-form"
                                method="POST"
                                action="<?php echo e(route('log-viewer.entry.delete', [
                                    'filename' => $filename,
                                    'entryId' => $entry['id']
                                ])); ?>"
                                onsubmit="return confirm('Delete only this log entry?');"
                            >

                                <?php echo e(csrf_field()); ?>


                                <?php echo e(method_field('DELETE')); ?>


                                <input
                                    type="hidden"
                                    name="tab"
                                    value="<?php echo e($tab); ?>"
                                >

                                <input
                                    type="hidden"
                                    name="search"
                                    value="<?php echo e($search); ?>"
                                >

                                <button
                                    class="btn-delete-entry"
                                    type="submit"
                                >
                                    Delete
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                </div>

                <pre class="entry-body"><?php echo e($entry['content']); ?></pre>

            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php else: ?>

        <div class="empty">

            <?php if($tab === 'error'): ?>

                No error log entries found.

            <?php else: ?>

                No success log entries found.

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>


<script>

    setTimeout(function () {

        window.location.reload();

    }, 10000);

</script>


</body>

</html>
