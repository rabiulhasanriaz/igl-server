<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadApiControllerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_api_controller', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_port_name');
            $table->tinyInteger('api_one_status')->default('1');
            $table->tinyInteger('api_two_status')->default('1');
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
        Schema::dropIfExists('load_api_controller');
    }
}
