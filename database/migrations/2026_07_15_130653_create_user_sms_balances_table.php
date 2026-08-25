<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSmsBalancesTable extends Migration
{
    public function up()
    {
        Schema::create('user_sms_balances', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->decimal('balance', 18, 4)->default(0);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_sms_balances');
    }
}
