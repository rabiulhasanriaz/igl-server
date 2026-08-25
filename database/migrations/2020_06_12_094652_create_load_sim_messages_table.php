<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadSimMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_sim_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->comment('user reference id');
            $table->string('operator_company', 30);
            $table->string('sim_no', 30);
            $table->mediumText('message');
            $table->string('sender', 30);
            $table->integer('serial_id')->unsigned()->nullable()->comment('total price amount');
            $table->mediumInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('load_sim_messages');
    }
}
