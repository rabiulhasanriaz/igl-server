<?php

use Faker\Generator as Faker;

$factory->define(App\Model\SenderIdUserDefault::class, function (Faker $faker) {
    return [
        //
        'user_id' => random_int(1, 30),
        'sender_id' => random_int(1, 20),
    ];
});
