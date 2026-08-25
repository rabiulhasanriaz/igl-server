<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('user_details', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        /*Schema::table('sender_id_virtual_numbers', function (Blueprint $table) {
            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('cascade');
        });*/

        Schema::table('sender_id_registers', function (Blueprint $table) {
            $table->foreign('sir_robi_vn')->references('id')->on('sender_id_virtual_numbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sir_airtel_vn')->references('id')->on('sender_id_virtual_numbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sir_banglalink_vn')->references('id')->on('sender_id_virtual_numbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sir_teletalk_vn')->references('id')->on('sender_id_virtual_numbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sir_gp_vn')->references('id')->on('sender_id_virtual_numbers')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('sender_id_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('sender_id_registers')->onDelete('cascade');
        });

        Schema::table('sender_id_user_defaults', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('sender_id_registers')->onDelete('cascade');
        });

        /*Schema::table('acc_sms_rates', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('cascade');
        });*/

        /*Schema::table('acc_sms_balances', function (Blueprint $table) {
            $table->foreign('asb_paid_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('asb_pay_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('asb_pay_mode')->references('id')->on('acc_pay_methods')->onDelete('cascade');
        });*/

        /*Schema::table('phonebook_campaign_contacts', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('phonebook_campaign_categories')->onDelete('cascade');
        });*/

        Schema::table('sms_templates', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('phonebook_categories', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('phonebook_contacts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('phonebook_categories')->onDelete('cascade');
        });

        /*Schema::table('acc_user_credit_histories', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDeleette('cascade');
        });*/

        Schema::table('sms_campaign_ids', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('sender_id_registers')->onDelete('cascade');
        });

        Schema::table('sms_cam_pendings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('sender_id_registers')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('sms_campaign_ids')->onDelete('cascade');
            /*$table->foreign('operator_id')->references('id')->on('operators')->onDelete('cascade');*/
        });

        Schema::table('sms_campaign_24hs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('sender_id_registers')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('sms_campaign_ids')->onDelete('cascade');
            /*$table->foreign('operator_id')->references('id')->on('operators')->onDelete('cascade');*/
        });

        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('sender_id_registers')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('sms_campaign_ids')->onDelete('cascade');
            /*$table->foreign('operator_id')->references('id')->on('operators')->onDelete('cascade');*/
        });

        /*Schema::table('employee_user_commissions', function (Blueprint $table) {
            $table->foreign('eu_id')->references('id')->on('employee_users')->onDelete('cascade');
        });*/

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
