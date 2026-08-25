<?php

use Faker\Generator as Faker;

$factory->define(App\Model\AccPayMethod::class, function (Faker $faker) {
    return [
        //
        'apm_name' => 'Cash'.random_int(1,1000),
        'apm_status' => '1',
    ];
});
