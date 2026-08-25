<?php

use Illuminate\Database\Seeder;

class SmsCamPendingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\SmsCamPending::class, 650)->create();
    }
}
