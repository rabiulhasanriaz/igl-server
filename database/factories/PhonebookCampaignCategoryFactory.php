<?php

use Faker\Generator as Faker;

$factory->define(App\Model\PhonebookCampaignCategory::class, function (Faker $faker) {
    return [
        //
        'name' => $faker->unique()->name,
        'slug' => str_random('12'),
    ];
});
