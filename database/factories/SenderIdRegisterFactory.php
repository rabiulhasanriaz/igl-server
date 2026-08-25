<?php

use Faker\Generator as Faker;

$factory->define(App\Model\SenderIdRegister::class, function (Faker $faker) {
    return [
        //
        'sir_sender_id' => str_random(11),
        'sir_reg_date' => NOW(),
        'sir_robi_vn' => random_int(1,6),
        'sir_robi_confirmation' => random_int(1,2),
        'sir_airtel_vn' => random_int(1,6),
        'sir_airtel_confirmation' => random_int(1,2),
        'sir_banglalink_vn' => random_int(1,6),
        'sir_banglalink_confirmation' => random_int(1,2),
        'sir_teletalk_vn' => random_int(1,6),
        'sir_teletalk_confirmation' => random_int(1,2),
        'sir_gp_vn' => random_int(1,6),
        'sir_gp_confirmation' => random_int(1,2),
        'sir_confirmation_date' => NOW(),
        'sir_status' => '1',
        'sir_active' => '0',
    ];
});
