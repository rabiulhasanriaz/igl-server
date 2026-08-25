<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBalanceColumnsToDecimal extends Migration
{
    public function up()
    {
        Schema::table('acc_sms_balances', function (Blueprint $table) {
            $table->decimal('asb_credit', 18, 4)->default(0)->change();
            $table->decimal('asb_debit', 18, 4)->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('acc_sms_balances', function (Blueprint $table) {
            $table->float('asb_credit')->change();
            $table->float('asb_debit')->change();
        });
    }
}
