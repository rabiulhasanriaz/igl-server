<?php

use Faker\Generator as Faker;

$factory->define(App\Model\SmsCampaignId::class, function (Faker $faker) {
    return [
        //
        'user_id' => random_int(1,30),
        'sender_id' => random_int(1,10),
        'sci_campaign_id' => str_random(10),
        'sci_total_submitted' => '186',
        'sci_total_cost' => '50.25',
        'sci_campaign_type' => random_int(1,2),
        'sci_deal_type' => random_int(1,2),
        'sci_targeted_time' => \Carbon\Carbon::now(),
    ];
});
