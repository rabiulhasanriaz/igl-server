<?php

use Illuminate\Database\Seeder;

class AccPayMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\AccPayMethod::class, 3)->create();
    }
}
