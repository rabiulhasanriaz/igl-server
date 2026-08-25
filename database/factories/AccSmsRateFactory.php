<?php

use Faker\Generator as Faker;

$factory->define(App\Model\AccSmsRate::class, function (Faker $faker) {
    return [
        //
        'country_id' => '1',
        'user_id' => random_int(1, 20),
        'operator_id' => random_int(1,5),
        'asr_masking' => '0.48',
        'asr_nonmasking' => '0.22',
    ];
});
