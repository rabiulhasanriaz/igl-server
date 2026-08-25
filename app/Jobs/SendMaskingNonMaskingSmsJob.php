<?php

namespace App\Jobs;

use App\Http\Controllers\Cron\CronController;
use App\Models\SmsCamPending;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMaskingNonMaskingSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600;

    public function handle()
    {
        Log::info('=== SMS JOB STARTED ===');
        
        try {
            // Check environment
            Log::info('Environment: ' . app()->environment());
            Log::info('Database: ' . config('database.connections.mysql.database'));
            Log::info('Current Time: ' . Carbon::now()->toDateTimeString());
            Log::info('Timezone: ' . config('app.timezone'));
            
            // Check pending records
            $total = SmsCamPending::count();
            Log::info('Total pending records: ' . $total);
            
            $status1 = SmsCamPending::where('scp_campaign_status', 1)->count();
            Log::info('Status=1 records: ' . $status1);
            
            $targetTime = SmsCamPending::where('scp_target_time', '<=', Carbon::now())->count();
            Log::info('Target time passed records: ' . $targetTime);
            
            $nonMasking = SmsCamPending::where('scp_sms_type', 1)->count();
            Log::info('Non-masking records: ' . $nonMasking);
            
            // Show sample records
            $samples = SmsCamPending::where('scp_campaign_status', 1)->take(3)->get();
            foreach ($samples as $sample) {
                Log::info('Sample record:', [
                    'id' => $sample->id,
                    'type' => $sample->scp_sms_type,
                    'target_time' => $sample->scp_target_time,
                    'status' => $sample->scp_campaign_status
                ]);
            }
            
            // Call your controller
            $controller = app(CronController::class);
            $result = $controller->sendMaskingNonMaskingSms();
            
            Log::info('=== SMS JOB COMPLETED ===');
            
        } catch (\Exception $e) {
            Log::error('SMS JOB FAILED: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
