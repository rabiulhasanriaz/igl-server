<?php

use Faker\Generator as Faker;

$factory->define(App\Model\AccUserCreditHistory::class, function (Faker $faker) {
    $i = random_int(1,5);
    return [
        //
        'campaign_id' => str_random(12),
        'user_id' => random_int(1, 30),
        'uch_sms_count' => $i,
        'uch_sms_cost' => ($i*0.25),
    ];
});
