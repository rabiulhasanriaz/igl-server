<?php

use Illuminate\Database\Seeder;

class AccUserCreditHistoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\AccUserCreditHistory::class, 200)->create();
    }
}
