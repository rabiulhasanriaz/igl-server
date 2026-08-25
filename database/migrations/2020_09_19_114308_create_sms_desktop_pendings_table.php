<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsDesktopPendingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_desktop_pendings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('Who done this campaign');
            // $table->integer('sender_id')->unsigned()->comment('sender id from reg_sender_ids table');
            $table->integer('campaign_id')->unsigned()->comment('campaign id from sms_campaign_ids table');
            $table->string('sdp_cell_no', 25)->comment('Number');
            $table->text('sdp_message')->comment('content of sms');
            $table->float('sdp_sms_cost')->comment('Cost of this sms');
            $table->integer('operator_id')->unsigned()->comment('operator id from operators table');
            $table->tinyInteger('sdp_campaign_type')->comment('1=instant, 2=Schedule');
            $table->tinyInteger('sdp_deal_type')->comment('1=SMS, 2=Campaign');
            $table->tinyInteger('sdp_sms_type')->comment('1=NonMasking, 2=Masking');
            $table->string('sdp_sms_id', 30)->comment('SMS ID From Operator');
            $table->tinyInteger('sdp_tried')->comment('Try For Send');
            $table->tinyInteger('sdp_picked')->comment('0=not try, 1= try');
            $table->string('sdp_sms_text_type' , 20)->comment('Sms Type');
            $table->tinyInteger('sdp_priority')->default(0);
            $table->timestamp('sdp_target_time')->comment('target time for send ss');
            $table->timestamp('sdp_campaign_status');
            $table->timestamps();
            $table->tinyInteger('sdp_status')->comment('sms status');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('sender_id')->references('id')->on('sender_id_registers')->onDelete('cascade');
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
        Schema::dropIfExists('sms_desktop_pendings');
    }
}
