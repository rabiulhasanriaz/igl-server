<?php

namespace App\Jobs;

use App\Services\IRechargeWebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessIRechargeWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [5, 15, 30];
    
    protected $webhookData;

    public function __construct(array $webhookData)
    {
        $this->webhookData = $webhookData;
    }

    public function handle(IRechargeWebhookService $service)
    {
        try {
            $result = $service->process($this->webhookData);
            
            Log::info('Webhook job completed', [
                'reference' => $this->webhookData['reference'] ?? 'unknown',
                'result' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Webhook job failed', [
                'error' => $e->getMessage(),
                'data' => $this->webhookData
            ]);
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::critical('Webhook job permanently failed', [
            'data' => $this->webhookData,
            'error' => $exception->getMessage()
        ]);
    }
}