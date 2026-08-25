<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_desktops', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('Who Done This Campaign');
            $table->integer('campaign_id')->unsigned()->comment('campaign id from sms_campaign_ids table');
            $table->string('sd_cell_no', 25)->comment('Number');
            $table->text('sd_message')->comment('content of sms');
            $table->float('sd_sms_cost')->comment('Cost of this sms');
            $table->integer('operator_id')->unsigned()->comment('operator id from operators table');
            $table->tinyInteger('sd_campaign_type')->comment('1=instant, 2=schedule');
            $table->tinyInteger('sd_deal_type')->comment('1=SMS, 2=Campaign');
            $table->tinyInteger('sd_sms_type')->comment('1=NonMasking, 2=Masking');
            $table->string('sd_sms_id', 60)->comment('SMS ID From Operator');
            $table->string('sd_sms_text_type' , 20)->comment('Sms Type');
            $table->timestamp('sd_submitted_time')->comment('submit time for send sms');
            $table->timestamp('sd_targeted_time')->nullable()->comment('target time for send sms');
            $table->timestamps();
            $table->string('sd_delivery_report', 15)->comment('Sms delivery report');
            $table->string('sd_status',10)->comment('sms status');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('sms_desktop_campaign_ids')->onDelete('cascade');
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
        Schema::dropIfExists('sms_desktops');
    }
}
