<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('operator_id')->comment('Operator ID');
            $table->tinyInteger('package_category')->comment('1: Minute, 2: SMS, 3: Data, 4: Combo');
            $table->integer('package_price')->comment('Price of this package in Tk');
            $table->string('package_details')->nullable()->comment('Package Details');
            $table->string('validity')->nullable()->comment('Validity Time');
            $table->tinyInteger('status')->comment('1: Active 0: Inactive')->default(1);
            $table->float('commission')->comment('Commission for admin')->default(0.0);
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
        Schema::dropIfExists('load_packages');
    }
}
