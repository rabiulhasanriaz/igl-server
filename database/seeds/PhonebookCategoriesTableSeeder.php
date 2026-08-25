<?php

use Illuminate\Database\Seeder;

class PhonebookCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\PhonebookCategory::class, 10)->create();
    }
}
