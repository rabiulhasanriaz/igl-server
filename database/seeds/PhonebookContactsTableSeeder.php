<?php

use Illuminate\Database\Seeder;

class PhonebookContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\PhonebookContact::class, 300)->create();
    }
}
