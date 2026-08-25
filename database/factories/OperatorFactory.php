<?php

use Faker\Generator as Faker;

$factory->define(App\Model\Operator::class, function (Faker $faker) {
    return [
        //

        'ope_operator_name' => function(){
            $opeName = array('Robi', 'Airtel', 'Banglalink', 'GP', 'Teletalk', 'RangsTel', 'BanglarPhone', 'IGL Tel');
            $i = random_int(0,7);
            return $opeName[$i];
        },
        'ope_country_code' => '88',
        'ope_number' => function(){
            $opeNumber = array('88018', '88016', '88019', '88017', '88015', '88044', '88035', '88445');
            $j = random_int(0,7);
            return $opeNumber[$j];
        },
    ];
});
