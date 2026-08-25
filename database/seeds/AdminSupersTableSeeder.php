<?php

use Illuminate\Database\Seeder;

class AdminSupersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\AdminSuper::class, 3)->create();
    }
}
