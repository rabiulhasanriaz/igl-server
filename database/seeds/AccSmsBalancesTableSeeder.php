<?php

use Illuminate\Database\Seeder;

class AccSmsBalancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\AccSmsBalance::class, 200)->create();
    }
}
