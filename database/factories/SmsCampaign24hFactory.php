<?php

use Faker\Generator as Faker;

$factory->define(App\Model\SmsCampaign_24h::class, function (Faker $faker) {
    return [
        //
        'user_id' => random_int(1,30),
        'sender_id' => random_int(1,20),
        'campaign_id' => random_int(1, 20),
        'sct_cell_no' => $faker->phoneNumber,
        'sct_message' => $faker->paragraph,
        'sct_sms_cost' => '.48',
        'operator_id' => random_int(1,4),
        'sct_campaign_type' => random_int(1,2),
        'sct_deal_type' => random_int(1,2),
        'sct_sms_type' => random_int(1,2),
        'sct_sms_id' => str_random(10),
        'sct_sms_text_type' => 'text',
        'sct_target_time' => \Carbon\Carbon::now(),
        'sct_delivery_report' => function(){
            $deliveryReport = array('DELIVERED', 'PENDING', 'UNDELIVERED');
            $i = random_int(0,2);
            return $deliveryReport[$i];
        },
        'sct_status' => random_int(0,1),
    ];
});
