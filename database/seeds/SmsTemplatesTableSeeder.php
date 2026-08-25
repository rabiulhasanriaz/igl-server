<?php

use Illuminate\Database\Seeder;

class SmsTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Model\SmsTemplate::class, 20)->create();
    }
}
