<?php

use Faker\Generator as Faker;

$factory->define(App\Model\SmsCampaign::class, function (Faker $faker) {
    return [
        //
        'user_id' => random_int(1,30),
        'sender_id' => random_int(1,20),
        'campaign_id' => random_int(1,20),
        'sc_cell_no' => $faker->phoneNumber,
        'sc_message' => $faker->paragraph,
        'sc_sms_cost' => '.48',
        'operator_id' => random_int(1,4),
        'sc_campaign_type' => random_int(1,2),
        'sc_deal_type' => random_int(1,2),
        'sc_sms_type' => random_int(1,2),
        'sc_sms_id' => str_random(10),
        'sc_sms_text_type' => 'text',
        'sc_submitted_time' => \Carbon\Carbon::now(),
        'sc_targeted_time' => \Carbon\Carbon::now(),
        'sc_delivery_report' => function(){
            $deliveryReport = array('DELIVERED', 'PENDING', 'UNDELIVERED');
            $i = random_int(0,2);
            return $deliveryReport[$i];
        },
        'sc_status' => random_int(0,1),
    ];
});
