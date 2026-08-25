<?php

use Illuminate\Database\Seeder;

class SmsCampaignIdsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\SmsCampaignId::class, 20)->create();
    }
}
