<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Model\AdminSuper::class, function (Faker $faker) {
    return [
        //
        'as_user_name' => $faker->unique()->name,
    	'as_email' => $faker->unique()->safeEmail,
    	'as_cellphone' => random_int(111, 9999),
    	'as_password' => bcrypt('123456'),
    	'as_designation' => $faker->title,
    	'as_address' => $faker->address,
    	'as_image' => 'https://picsum.photos/200/300/?random',
    	'as_status' => '1',
    	'as_user_type' => '1',
    	'as_last_login_time' => Now(),
    	'remember_token' => bcrypt('456655'),
    	'as_last_log_ip' => '198.0.0.1',
    	'as_last_log_os' => 'Windows',
    ];
});
