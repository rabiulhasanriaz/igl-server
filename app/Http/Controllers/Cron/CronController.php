<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Interfaces\CronServiceInterface;

class CronController extends Controller
{
    protected $cronService;

    public function __construct(CronServiceInterface $cronService)
    {
        $this->cronService = $cronService;
    }

    public function nonMaskingSms()
    {
        return $this->cronService->nonMaskingSms();
    }

    public function maskingSms()
    {
        return $this->cronService->maskingSms();
    }

    public function nonMaskingDeliveryReport()
    {
        return $this->cronService->nonMaskingDeliveryReport();
    }

    public function gpDeliveryReport()
    {
        return $this->cronService->gpDeliveryReport();
    }

    public function sendMaskingNonMaskingSms()
    {
        return $this->cronService->sendMaskingNonMaskingSms();
    }

    public function total_submit_of_this_month()
    {
        return $this->cronService->total_submit_of_this_month();
    }

}
