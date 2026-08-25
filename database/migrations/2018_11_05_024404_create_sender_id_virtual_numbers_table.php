<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSenderIdVirtualNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sender_id_virtual_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operator_id')->unsigned();
            $table->string('sivn_number', 50);
            $table->string('sivn_name', 50)->nullable();
            $table->tinyInteger('sivn_status')->default('1');
            $table->string('sivn_api_user_name', 50);
            $table->string('sivn_api_password', 100);
            $table->timestamps();

            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sender_id_virtual_numbers');
    }
}
