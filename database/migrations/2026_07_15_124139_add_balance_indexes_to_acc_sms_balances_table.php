<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBalanceIndexesToAccSmsBalancesTable extends Migration
{
    public function up()
    {
        Schema::table('acc_sms_balances', function (Blueprint $table) {

            $table->index(
                'asb_pay_to',
                'acc_sms_balances_pay_to_index'
            );

            $table->index(
                ['asb_pay_to', 'asb_submit_time'],
                'acc_sms_balances_pay_to_submit_time_index'
            );

        });
    }

    public function down()
    {
        Schema::table('acc_sms_balances', function (Blueprint $table) {

            $table->dropIndex('acc_sms_balances_pay_to_index');

            $table->dropIndex(
                'acc_sms_balances_pay_to_submit_time_index'
            );

        });
    }
}
