<?php

use Illuminate\Database\Seeder;

class PhonebookCampaignCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\PhonebookCampaignCategory::class, 20)->create();
    }
}
