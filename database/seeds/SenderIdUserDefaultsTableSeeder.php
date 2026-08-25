<?php

use Illuminate\Database\Seeder;

class SenderIdUserDefaultsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\SenderIdUserDefault::class, 30)->create();
    }
}
