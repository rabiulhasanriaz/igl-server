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
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                'modified_timestamp' => $file->getMTime(),
            ];
        }

        usort($logs, function ($a, $b) {
            return $b['modified_timestamp'] <=> $a['modified_timestamp'];
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

        $this->validateLogFile($path);

        $content = File::get($path);

        $search = trim($request->get('search', ''));

        $tab = strtolower(
            trim($request->get('tab', 'error'))
        );

        if (!in_array($tab, ['success', 'error'])) {
            $tab = 'error';
        }

        $entries = $this->parseLog($content);

        /*
         * Latest log data first.
         */
        usort($entries, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        $successLevels = [
            'info',
            'notice',
            'debug',
        ];

        $errorLevels = [
            'warning',
            'error',
            'critical',
            'alert',
            'emergency',
        ];

        $successEntries = array_values(
            array_filter(
                $entries,
                function ($entry) use ($successLevels) {
                    return in_array(
                        strtolower($entry['level']),
                        $successLevels
                    );
                }
            )
        );

        $errorEntries = array_values(
            array_filter(
                $entries,
                function ($entry) use ($errorLevels) {
                    return in_array(
                        strtolower($entry['level']),
                        $errorLevels
                    );
                }
            )
        );

        /*
         * Search both categories.
         */
        if (!empty($search)) {
            $successEntries = $this->searchEntries(
                $successEntries,
                $search
            );

            $errorEntries = $this->searchEntries(
                $errorEntries,
                $search
            );
        }

        return view(
            'admin.logs.show',
            compact(
                'filename',
                'successEntries',
                'errorEntries',
                'search',
                'tab'
            )
        );
    }

    /**
     * Delete one specific log entry.
     *
     * This removes only the selected Laravel log block,
     * not the complete log file.
     */
    public function deleteEntry(Request $request, $filename, $entryId)
    {
        $filename = basename($filename);
        $path = storage_path('logs/' . $filename);

        $this->validateLogFile($path);

        $content = File::get($path);
        $entries = $this->parseLog($content);

        $selectedEntry = null;

        foreach ($entries as $entry) {
            if ($entry['id'] === $entryId) {
                $selectedEntry = $entry;
                break;
            }
        }

        if (!$selectedEntry) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Log entry not found. The log file may have changed.'
                );
        }

        /*
         * Remove only the selected block.
         */
        $newContent =
            substr($content, 0, $selectedEntry['start']) .
            substr($content, $selectedEntry['end']);

        File::put($path, $newContent);

        $tab = $request->get('tab', 'error');
        $search = $request->get('search', '');

        return redirect()
            ->route(
                'log-viewer.show',
                [
                    'filename' => $filename,
                    'tab' => $tab,
                    'search' => $search,
                ]
            )
            ->with(
                'success',
                'Selected log entry deleted successfully.'
            );
    }

    /**
     * Download a log file.
     */
    public function download($filename)
    {
        $filename = basename($filename);
        $path = storage_path('logs/' . $filename);

        $this->validateLogFile($path);

        return response()->download($path);
    }

    /**
     * Clear the contents of a log file.
     */
    public function clear($filename)
    {
        $filename = basename($filename);
        $path = storage_path('logs/' . $filename);

        $this->validateLogFile($path);

        File::put($path, '');

        return redirect()
            ->route('log-viewer.index')
            ->with(
                'success',
                'Log file cleared successfully.'
            );
    }

    /**
     * Delete a complete log file.
     */
    public function delete($filename)
    {
        $filename = basename($filename);
        $path = storage_path('logs/' . $filename);

        $this->validateLogFile($path);

        File::delete($path);

        return redirect()
            ->route('log-viewer.index')
            ->with(
                'success',
                'Log file deleted successfully.'
            );
    }

    /**
     * Parse Laravel log file.
     */
    private function parseLog($content)
    {
        $entries = [];

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

            $date = $matches[1][$i][0];
            $timestamp = strtotime($date);

            /*
             * start position is included so identical
             * log messages still get different IDs.
             */
            $entryId = sha1(
                $start . '|' . $entryContent
            );

            $entries[] = [
                'id' => $entryId,
                'date' => $date,
                'timestamp' => $timestamp !== false
                    ? $timestamp
                    : 0,
                'environment' => $matches[2][$i][0],
                'level' => strtolower(
                    $matches[3][$i][0]
                ),
                'content' => trim($entryContent),

                /*
                 * Used internally for deleting one block.
                 */
                'start' => $start,
                'end' => $end,
            ];
        }

        /*
         * Raw/unparsed file fallback.
         *
         * A raw fallback entry is shown but cannot be
         * individually deleted safely.
         */
        if (
            empty($entries)
            &&
            !empty(trim($content))
        ) {
            $entries[] = [
                'id' => null,
                'date' => '',
                'timestamp' => 0,
                'environment' => '',
                'level' => 'info',
                'content' => $content,
                'start' => 0,
                'end' => strlen($content),
            ];
        }

        return $entries;
    }

    /**
     * Search entries.
     */
    private function searchEntries(array $entries, $search)
    {
        return array_values(
            array_filter(
                $entries,
                function ($entry) use ($search) {
                    return
                        stripos(
                            $entry['content'],
                            $search
                        ) !== false
                        ||
                        stripos(
                            $entry['date'],
                            $search
                        ) !== false
                        ||
                        stripos(
                            $entry['environment'],
                            $search
                        ) !== false;
                }
            )
        );
    }

    /**
     * Validate log file path.
     */
    private function validateLogFile($path)
    {
        if (!File::exists($path)) {
            abort(404, 'Log file not found.');
        }

        if (
            pathinfo(
                $path,
                PATHINFO_EXTENSION
            ) !== 'log'
        ) {
            abort(403, 'Invalid log file.');
        }
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
