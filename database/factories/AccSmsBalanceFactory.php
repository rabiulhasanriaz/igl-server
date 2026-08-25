<?php

use Faker\Generator as Faker;

$factory->define(App\Model\AccSmsBalance::class, function (Faker $faker) {
    return [
        //
        'asb_paid_by' => random_int(1, 30),
        'asb_pay_to' => random_int(1, 30),
        'asb_pay_ref' => str_random(10),
        'asb_credit' => random_int(0, 50),
        'asb_debit' => random_int(0, 60),
        'asb_submit_time' => \Carbon\Carbon::Now(),
        'asb_target_time' => \Carbon\Carbon::Now(),
        'asb_pay_mode' => random_int(1,3),
        'asb_payment_status' => random_int(1,2),
        'asb_deal_type' => random_int(1,2),
        'credit_return_type' => '0',
    ];
});
