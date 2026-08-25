<?php

use Faker\Generator as Faker;

$factory->define(App\Model\SenderIdVirtualNumber::class, function (Faker $faker) {
    return [
        //
        'operator_id' => random_int(1, 6),
        'sivn_number' => $faker->phoneNumber,
        'sivn_api_user_name' => $faker->name,
        'sivn_api_password' => '123456',
    ];
});
