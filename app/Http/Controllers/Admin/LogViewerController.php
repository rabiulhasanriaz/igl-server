<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    /**
     * Show available Laravel log files.
     */
    public function index()
    {
        $logPath = storage_path('logs');

        if (!File::exists($logPath)) {
            return response('Log directory not found.', 404);
        }

        $files = File::files($logPath);

        $logs = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'log') {
                continue;
            }

            $logs[] = [
                'name' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'modified' => date(
                    'Y-m-d H:i:s',
                    $file->getMTime()
                ),
            ];
        }

        usort($logs, function ($a, $b) {
            return strcmp($b['modified'], $a['modified']);
        });

        return view('admin.logs.index', compact('logs'));
    }

    /**
     * Show one log file.
     */
    public function show(Request $request, $filename)
    {
        $filename = basename($filename);

        $path = storage_path('logs/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'Log file not found.');
        }

        if (pathinfo($path, PATHINFO_EXTENSION) !== 'log') {
            abort(403, 'Invalid log file.');
        }

        $content = File::get($path);

        $level = strtolower(
            trim($request->get('level', ''))
        );

        $search = trim(
            $request->get('search', '')
        );

        $entries = $this->parseLog($content);

        /*
         * Filter by log level.
         */
        if (!empty($level)) {
            $entries = array_filter(
                $entries,
                function ($entry) use ($level) {
                    return strtolower($entry['level']) === $level;
                }
            );
        }

        /*
         * Search log content.
         */
        if (!empty($search)) {
            $entries = array_filter(
                $entries,
                function ($entry) use ($search) {
                    return stripos(
                        $entry['content'],
                        $search
                    ) !== false;
                }
            );
        }

        /*
         * Latest errors first.
         */
        $entries = array_reverse($entries);

        $levels = [
            'emergency',
            'alert',
            'critical',
            'error',
            'warning',
            'notice',
            'info',
            'debug',
        ];

        return view(
            'admin.logs.show',
            compact(
                'filename',
                'entries',
                'levels',
                'level',
                'search'
            )
        );
    }

    /**
     * Download a log file.
     */
    public function download($filename)
    {
        $filename = basename($filename);

        $path = storage_path('logs/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'Log file not found.');
        }

        if (pathinfo($path, PATHINFO_EXTENSION) !== 'log') {
            abort(403, 'Invalid log file.');
        }

        return response()->download($path);
    }

    /**
     * Clear the contents of a log file.
     */
    public function clear($filename)
    {
        $filename = basename($filename);

        $path = storage_path('logs/' . $filename);

        if (!File::exists($path)) {
            return redirect()
                ->back()
                ->with('error', 'Log file not found.');
        }

        if (pathinfo($path, PATHINFO_EXTENSION) !== 'log') {
            abort(403, 'Invalid log file.');
        }

        File::put($path, '');

        return redirect()
            ->route('admin.logs.index')
            ->with('success', 'Log file cleared successfully.');
    }

    /**
     * Delete a log file.
     */
    public function delete($filename)
    {
        $filename = basename($filename);

        $path = storage_path('logs/' . $filename);

        if (!File::exists($path)) {
            return redirect()
                ->back()
                ->with('error', 'Log file not found.');
        }

        if (pathinfo($path, PATHINFO_EXTENSION) !== 'log') {
            abort(403, 'Invalid log file.');
        }

        File::delete($path);

        return redirect()
            ->route('admin.logs.index')
            ->with('success', 'Log file deleted successfully.');
    }

    /**
     * Parse Laravel log file.
     */
    private function parseLog($content)
    {
        $entries = [];

        /*
         * Laravel log format:
         *
         * [2026-09-02 10:30:00] local.ERROR: message
         */
        $pattern =
            '/^\[([^\]]+)\]\s+' .
            '([a-zA-Z0-9_-]+)\.' .
            '(EMERGENCY|ALERT|CRITICAL|ERROR|WARNING|NOTICE|INFO|DEBUG):\s*/m';

        preg_match_all(
            $pattern,
            $content,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        $count = count($matches[0]);

        for ($i = 0; $i < $count; $i++) {

            $start = $matches[0][$i][1];

            if ($i + 1 < $count) {
                $end = $matches[0][$i + 1][1];
            } else {
                $end = strlen($content);
            }

            $entryContent = substr(
                $content,
                $start,
                $end - $start
            );

            $entries[] = [
                'date' => $matches[1][$i][0],
                'environment' => $matches[2][$i][0],
                'level' => strtolower(
                    $matches[3][$i][0]
                ),
                'content' => trim($entryContent),
            ];
        }

        /*
         * If parsing failed, still show the raw log.
         */
        if (empty($entries) && !empty(trim($content))) {
            $entries[] = [
                'date' => '',
                'environment' => '',
                'level' => 'unknown',
                'content' => $content,
            ];
        }

        return $entries;
    }

    /**
     * Convert bytes to readable size.
     */
    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format(
                $bytes / 1073741824,
                2
            ) . ' GB';
        }

        if ($bytes >= 1048576) {
            return number_format(
                $bytes / 1048576,
                2
            ) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format(
                $bytes / 1024,
                2
            ) . ' KB';
        }

        return $bytes . ' bytes';
    }
}