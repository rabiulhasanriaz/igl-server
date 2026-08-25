<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsCampaign24hsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_campaign_24hs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('Who Done This Campaign');
            $table->integer('sender_id')->unsigned()->comment('sender id from reg_sender_ids table');
            $table->integer('campaign_id')->unsigned()->comment('campaign id from sms_campaign_ids table');
            $table->string('sct_cell_no', 25)->comment('Number');
            $table->text('sct_message')->comment('content of sms');
            $table->float('sct_sms_cost')->comment('Cost of this sms');
            $table->integer('operator_id')->unsigned()->comment('operator id from operators table');
            $table->tinyInteger('sct_campaign_type')->comment('1=instant, 2=Schedule');
            $table->tinyInteger('sct_deal_type')->comment('1=SMS, 2=Campaign');
            $table->tinyInteger('sct_sms_type')->comment('1=NonMasking, 2=Masking');
            $table->string('sct_sms_id', 60)->comment('SMS ID From Operator');
            $table->string('sct_sms_text_type' , 20)->comment('Sms Type');
            $table->timestamp('sct_target_time')->comment('target time for send sms');
            $table->timestamps();
            $table->string('sct_delivery_report', 15)->comment('Sms delivery report');
            $table->string('sct_status', 10)->comment('sms status');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('sender_id_registers')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('sms_campaign_ids')->onDelete('cascade');
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
        Schema::dropIfExists('sms_campaign_24hs');
    }
}
