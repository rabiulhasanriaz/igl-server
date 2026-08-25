<?php

use Faker\Generator as Faker;

$factory->define(App\Model\SmsTemplate::class, function (Faker $faker) {
    return [
        //
        'user_id' => random_int(1,30),
        'st_name' => $faker->name,
        'st_content' => $faker->paragraph,
        'st_total_sms' => random_int(1,5),
        'st_content_type' => random_int(1,2),
    ];
});
