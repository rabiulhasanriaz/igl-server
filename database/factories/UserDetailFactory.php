<?php

use Faker\Generator as Faker;

$factory->define(App\Model\UserDetail::class, function (Faker $faker) {
    return [
        //
        'user_id' => random_int(1, 30),
        'limit' => random_int(0, 5000),
        'company_name' => $faker->name,
        'designation' => $faker->name,
        'address' => $faker->address,
        'logo' => 'https://picsum.photos/200/300/?random',
        'user_p' => '123456',
        'last_log_ip' => '27.147.180.165',
        'last_log_os' => 'windows',
        'api_key' => str_random(12),
        'hotline' => str_random(11),
        'logout_url' => 'localhost/smsL',
    ];
});
