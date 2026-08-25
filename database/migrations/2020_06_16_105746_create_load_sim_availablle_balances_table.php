<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadSimAvailablleBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_sim_availablle_balances', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('airtel', '10', '2')->nullable();
            $table->decimal('blink', '10', '2')->nullable();
            $table->decimal('gp', '10', '2')->nullable();
            $table->decimal('robi', '10', '2')->nullable();
            $table->decimal('teletalk', '10', '2')->nullable();

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
        Schema::dropIfExists('load_sim_availablle_balances');
    }
}
