<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsCampaignIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_campaign_ids', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('sender_id')->unsigned();
            $table->string('sci_campaign_title')->nullable()->comment('Campaign Title');
            $table->string('sci_campaign_id', 15)->unique()->comment('sms campaign id');
            $table->mediumInteger('sci_total_submitted')->comment('number of total sent sms');
            $table->float('sci_total_cost')->comment('cost of this campaign');
            $table->tinyInteger('sci_campaign_type')->comment('1=instant, 2=Schedule');
            $table->tinyInteger('sci_deal_type')->comment('1=SMS, 2=Campaign');
            $table->tinyInteger('sci_sms_type')->nullable()->comment('1=SMS, 2=Campaign');
            $table->tinyInteger('sci_dynamic_type')->default('0')->comment('1=dynamic, 0=general');
            $table->tinyInteger('sci_sender_operator')->nullable()->comment('1=Robi/Airtel, 2=gp , 3 = bl , 4=tt');
            $table->timestamp('sci_targeted_time')->comment('this campaign targeted time');
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
        Schema::dropIfExists('sms_campaign_ids');
    }
}
