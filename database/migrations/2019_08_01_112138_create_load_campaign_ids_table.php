<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadCampaignIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_campaign_ids', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('User id');
            $table->string('campaign_id')->comment('Campaign id');
            $table->string('campaign_name')->comment('Campaign name');
            $table->integer('total_number')->unsigned()->comment('Total numbers');
            $table->integer('total_amount')->unsigned()->comment('total price amount');
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
        Schema::dropIfExists('load_campaign_ids');
    }
}
