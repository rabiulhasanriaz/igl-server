<?php

use Faker\Generator as Faker;

$factory->define(App\Model\PhonebookCampaignContact::class, function (Faker $faker) {
    return [
        //
        'category_id' => random_int(1, 20),
        'phone_number' => $faker->phoneNumber,
    ];
});
