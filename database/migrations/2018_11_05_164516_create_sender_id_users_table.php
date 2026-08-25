<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSenderIdUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sender_id_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('user id or customer id');
            $table->integer('sender_id')->unsigned();
            $table->tinyInteger('status')->default('1')->comment('1=Active, 2=Inactive, 5=Pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('sender_id_registers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sender_id_users');
    }
}
