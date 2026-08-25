<?php

namespace App\Interfaces;

interface CronServiceInterface
{
    public function nonMaskingSms();
    public function maskingSms();
    public function nonMaskingDeliveryReport();
    public function gpDeliveryReport();
    public function sendMaskingNonMaskingSms();
    public function total_submit_of_this_month();
}
