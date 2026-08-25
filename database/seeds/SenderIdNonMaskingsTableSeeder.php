<?php

use Illuminate\Database\Seeder;

class SenderIdNonMaskingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\SenderIdNonMasking::class, 5)->create();
    }
}
