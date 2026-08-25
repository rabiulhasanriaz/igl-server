<?php
// process-sms.php - Optimized for Local Speed
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Jobs\ProcessSmsCampaignJob;

// Remove echo for faster execution (optional - comment out for max speed)
// echo "[" . date('Y-m-d H:i:s') . "] Processing SMS...\n";

try {
    $job = new ProcessSmsCampaignJob();
    $job->handle();
    // echo "[" . date('Y-m-d H:i:s') . "] SMS Processed!\n";
} catch (Exception $e) {
    // echo "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n";
    // Log error silently for speed
    error_log($e->getMessage());
}