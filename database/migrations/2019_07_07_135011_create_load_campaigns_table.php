<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedMediumInteger('user_id')->comment('The user who has made this load');
            $table->string('operator_id', 25)->comment('Operator ID');
            $table->string('sms_id',25)->comment('SMS id');
            $table->string('campaign_id')->comment('Campaign ID');
            $table->string('targeted_number')->comment('Which number will get this flexiload');
            $table->string('owner_name', 255)->nullable()->comment('Mobile number owner');
            $table->unsignedMediumInteger('package_id');
            $table->unsignedTinyInteger('number_type')->comment('1: Prepaid 2: Postpaid');
            $table->unsignedTinyInteger('campaign_type')->comment('1: single, 2: package, 3: bulk');
            $table->unsignedMediumInteger('campaign_price');
            $table->string('remarks')->nullable();
            $table->string('transaction_id', 64)->nullable()->default(NULL);
            $table->unsignedTinyInteger('status');
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
        Schema::dropIfExists('load_campaigns');
    }
}
