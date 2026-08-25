<?php

use Faker\Generator as Faker;

$factory->define(App\Model\EmployeeUser::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'phone' => $faker->phoneNumber,
        'commission' => random_int(1,3),
        'password' => bcrypt('123456'),
        'employee_p' => '123456',
        'status' => '1',
    ];
});
