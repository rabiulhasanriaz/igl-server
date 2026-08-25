<?php

use Faker\Generator as Faker;

$factory->define(App\Model\SmsCamPending::class, function (Faker $faker) {
    return [
        //
        'user_id' => random_int(1,30),
        'sender_id' => random_int(1,20),
        'campaign_id' => random_int(1,20),
        'scp_cell_no' => $faker->phoneNumber,
        'scp_message' => $faker->paragraph(5),
        'scp_sms_cost' => '.48',
        'operator_id' => random_int(1,4),
        'scp_campaign_type' => random_int(1,2),
        'scp_deal_type' => random_int(1,2),
        'scp_sms_type' => random_int(1,2),
        'scp_sms_id' => str_random(10),
        'scp_tried' => random_int(1,2),
        'scp_picked' => random_int(1,2),
        'scp_sms_text_type' => 'text',
        'scp_target_time' => \Carbon\Carbon::now(),
        'scp_status' => random_int(0,1),
    ];
});
