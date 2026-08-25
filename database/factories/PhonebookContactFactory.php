<?php

use Faker\Generator as Faker;

$factory->define(App\Model\PhonebookContact::class, function (Faker $faker) {
    return [
        //
        'user_id' => random_int(1,30),
        'category_id' => random_int(1,10),
        'phone_number' => $faker->phoneNumber,
    ];
});
