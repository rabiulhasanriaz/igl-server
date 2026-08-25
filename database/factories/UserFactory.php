<?php

use Faker\Generator as Faker;
use App\Model\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Model\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'create_by' => function(){
            try{
                return User::all()->random();
            }
            catch (Exception $e){
                return null;
            }
        },
        'email' => $faker->unique()->safeEmail,
        'cellphone' => $faker->unique()->phoneNumber,
        'password' => bcrypt('123456'), // secret
        'status' => random_int(1,3),
        'role' => random_int(1,5),
        'position' => random_int(1,5),
        'remember_token' => str_random(10),
    ];
});
