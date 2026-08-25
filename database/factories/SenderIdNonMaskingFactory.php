<?php

use Faker\Generator as Faker;

$factory->define(App\Model\SenderIdNonMasking::class, function (Faker $faker) {
    return [
        //
        'number' => function(){
            $i = 0;
            return '880444560444'.$i++;
        },
    ];
});
