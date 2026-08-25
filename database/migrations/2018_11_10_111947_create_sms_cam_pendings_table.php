<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsCamPendingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_cam_pendings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('Who done this campaign');
            $table->integer('sender_id')->unsigned()->comment('sender id from reg_sender_ids table');
            $table->integer('campaign_id')->unsigned()->comment('campaign id from sms_campaign_ids table');
            $table->string('scp_cell_no', 25)->comment('Number');
            $table->text('scp_message')->comment('content of sms');
            $table->float('scp_sms_cost')->comment('Cost of this sms');
            $table->integer('operator_id')->unsigned()->comment('operator id from operators table');
            $table->tinyInteger('scp_campaign_type')->comment('1=instant, 2=Schedule');
            $table->tinyInteger('scp_deal_type')->comment('1=SMS, 2=Campaign');
            $table->tinyInteger('scp_sms_type')->comment('1=NonMasking, 2=Masking');
            $table->string('scp_sms_id', 30)->comment('SMS ID From Operator');
            $table->tinyInteger('scp_tried')->comment('Try For Send');
            $table->tinyInteger('scp_picked')->comment('0=not try, 1= try');
            $table->string('scp_sms_text_type' , 20)->comment('Sms Type');
            $table->timestamp('scp_target_time')->comment('target time for send ss');
            $table->timestamps();
            $table->tinyInteger('scp_status')->comment('sms status');

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
        Schema::dropIfExists('sms_cam_pendings');
    }
}
