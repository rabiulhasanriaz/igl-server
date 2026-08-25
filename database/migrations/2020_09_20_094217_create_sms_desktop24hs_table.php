<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsDesktop24hsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_desktop24hs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('Who Done This Campaign');
            // $table->integer('sender_id')->unsigned()->comment('sender id from reg_sender_ids table');
            $table->integer('campaign_id')->unsigned()->comment('campaign id from sms_campaign_ids table');
            $table->string('sdt_cell_no', 25)->comment('Number');
            $table->text('sdt_message')->comment('content of sms');
            $table->float('sdt_sms_cost')->comment('Cost of this sms');
            $table->integer('operator_id')->unsigned()->comment('operator id from operators table');
            $table->tinyInteger('sdt_campaign_type')->comment('1=instant, 2=Schedule');
            $table->tinyInteger('sdt_deal_type')->comment('1=SMS, 2=Campaign');
            $table->tinyInteger('sdt_sms_type')->comment('1=NonMasking, 2=Masking');
            $table->string('sdt_sms_id', 60)->comment('SMS ID From Operator');
            $table->string('sdt_sms_text_type' , 20)->comment('Sms Type');
            $table->timestamp('sdt_target_time')->comment('target time for send sms');
            $table->timestamps();
            $table->string('sdt_delivery_report', 15)->comment('Sms delivery report');
            $table->string('sdt_status', 10)->comment('sms status');

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
        Schema::dropIfExists('sms_desktop24hs');
    }
}
