<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadFlexibooksDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_flexibooks_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('load_flexibooks_id');
            $table->string('name')->comment('person name');
            $table->string('number');
            $table->string('operator',10);
            $table->tinyInteger('number_type');
            $table->integer('amount');
            $table->string('remarks');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('load_flexibooks_datas');
    }
}
