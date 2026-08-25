<?php

use Illuminate\Database\Seeder;

class PhonebookCampaignContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\PhonebookCampaignContact::class, 200)->create();
    }
}
