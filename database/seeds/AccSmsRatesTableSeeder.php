<?php

use Illuminate\Database\Seeder;

class AccSmsRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\AccSmsRate::class, 20)->create();
    }
}
