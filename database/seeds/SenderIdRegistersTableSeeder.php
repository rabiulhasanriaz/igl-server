<?php

use Illuminate\Database\Seeder;

class SenderIdRegistersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\SenderIdRegister::class, 20)->create();
    }
}
