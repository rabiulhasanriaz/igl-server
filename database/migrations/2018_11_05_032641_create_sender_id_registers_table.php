<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSenderIdRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sender_id_registers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sir_sender_id', 15);
            $table->timestamp('sir_reg_date');

            $table->integer('sir_robi_vn')->unsigned()->nullable()->comment('Robi Virtual Number id');
            $table->string('sir_robi_confirmation', 10)->comment('Robi Confirmation 1=confirm, 2=pending');

            $table->integer('sir_airtel_vn')->unsigned()->nullable()->comment('Airtel Virtual Number id');
            $table->string('sir_airtel_confirmation', 10)->comment('Airtel Confirmation 1=confirm, 2=pending');

            $table->integer('sir_banglalink_vn')->unsigned()->nullable()->comment('Banglalink Virtual Number id');
            $table->string('sir_banglalink_confirmation', 10)->comment('Banglalink Confirmation 1=confirm, 2=pending');

            $table->integer('sir_teletalk_vn')->unsigned()->nullable()->comment('Teletalk Virtual Number id');
            $table->string('sir_teletalk_confirmation', 10)->comment('Teletalk Confirmation 1=confirm, 2=pending');

            $table->string('sir_teletalk_user_name', 30)->comment('teletalk User name')->nullable();

            $table->string('sir_teletalk_user_password', 30)->comment('teletalk User Password')->nullable();

            $table->integer('sir_gp_vn')->unsigned()->nullable()->comment('GP Virtual Number id');
            $table->string('sir_gp_confirmation', 10)->comment('GP Confirmation 1=confirm, 2=pending');

            $table->timestamp('sir_confirmation_date')->nullable();
            $table->tinyInteger('sir_status')->default('1')->comment('1=Active, 2=Inactive, 5=Pending');
            $table->tinyInteger('sir_active')->default('0')->comment('Defult Sender ID');
            $table->timestamps();

            $table->foreign('sir_robi_vn')->references('id')->on('sender_id_virtual_numbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sir_airtel_vn')->references('id')->on('sender_id_virtual_numbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sir_banglalink_vn')->references('id')->on('sender_id_virtual_numbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sir_teletalk_vn')->references('id')->on('sender_id_virtual_numbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sir_gp_vn')->references('id')->on('sender_id_virtual_numbers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sender_id_registers');
    }
}
