<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Model\AdminUser::class, function (Faker $faker) {
    return [
        //
        'aa_create_by' => random_int(1, 3),
    	'aa_com_domain' => 'http://www.iglweb.com',
    	'aa_limit' => '0',
    	'aa_company_name' => $faker->name,
    	'aa_user_name' => $faker->unique()->name,
    	'aa_email' => $faker->unique()->safeEmail,
    	'aa_cellphone' => random_int(1111, 99999),
    	'aa_password' => bcrypt('123456'),
    	'aa_designation' => $faker->name,
    	'aa_address' => $faker->address,
    	'aa_logo' => 'https://picsum.photos/200/300/?random',
    	'aa_status' => '1',
    	'aa_user_type' => '1',
    	'aa_reg_date' => Now(),
    	'aa_exp_date' => Now(),
    	'aa_last_log_ip' => '198.0.0.1',
    	'aa_last_log_os' => 'Windows',
    	'aa_api_key' => random_int(00000000, 99999999),
    	'aa_facebookid' => 'http://www.facebook.com',
    	'aa_senderId' => '14514751475',
    	'aa_hotline' => '',
    	'aa_logout_url' => 'http://www.iglweb.com',
    ];
});
