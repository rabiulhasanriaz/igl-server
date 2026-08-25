<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillReportIndex extends Migration
{
    public function up()
    {
        Schema::table('acc_sms_balances', function (Blueprint $table) {
            $table->index(['asb_pay_to', 'asb_submit_time'], 'asb_pay_to_submit_time_idx');
        });
    }

    public function down()
    {
        Schema::table('acc_sms_balances', function (Blueprint $table) {
            $table->dropIndex('asb_pay_to_submit_time_idx');
        });
    }
}
