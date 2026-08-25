<?php

use Illuminate\Database\Seeder;

class EmployeeUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Model\EmployeeUser::class, 20)->create();
    }
}
