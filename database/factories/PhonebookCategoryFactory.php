<?php

use Faker\Generator as Faker;

$factory->define(App\Model\PhonebookCategory::class, function (Faker $faker) {
    return [
        //
        'user_id' => random_int(1,30),
        'name' => $faker->name,
    ];
});
