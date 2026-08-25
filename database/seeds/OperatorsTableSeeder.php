<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperatorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\Operator::class, 8)->create();
       /* DB::table('operators')->insert([
            'ope_country_code' => '88',
            'ope_operator_name' => 'Robi',
            'ope_number' => '88018',
        ]);*/
    }
}
