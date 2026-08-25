<?php

use Faker\Generator as Faker;

$factory->define(App\Model\Country::class, function (Faker $faker) {
    return [
        //
        'country_name' => 'Bangladesh',
        'country_code' => '88',
    ];
});
