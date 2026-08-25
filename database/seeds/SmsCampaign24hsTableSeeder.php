<?php

use Illuminate\Database\Seeder;

class SmsCampaign24hsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\SmsCampaign_24h::class, 200)->create();
    }
}
