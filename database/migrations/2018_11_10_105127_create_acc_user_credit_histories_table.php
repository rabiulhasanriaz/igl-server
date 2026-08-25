<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccUserCreditHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acc_user_credit_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('campaign_id');
            $table->integer('user_id')->unsigned();
            $table->integer('uch_sms_count');
            $table->float('uch_sms_cost');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDeleette('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_user_credit_histories');
    }
}
