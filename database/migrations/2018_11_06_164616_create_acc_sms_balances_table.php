<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccSmsBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acc_sms_balances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asb_paid_by')->unsigned()->comment('who paid this');
            $table->integer('asb_pay_to')->unsigned()->comment('Who Got This Payment ');
            $table->string('asb_pay_ref', 30)->comment('Payment Referance');
            $table->float('asb_credit')->comment('credited balance/Deposit Here ');
            $table->float('asb_debit')->comment('Debited balance/Cost');
            $table->timestamp('asb_submit_time')->nullable();
            $table->timestamp('asb_target_time')->nullable();
            $table->integer('asb_pay_mode')->unsigned()->comment('1=Cash, 2=BankDeposit, 3=Check, 4=SendSms');
            $table->tinyInteger('asb_payment_status')->comment('1=Paid, 2=Checking');
            $table->tinyInteger('asb_deal_type')->comment('1=Deposit, 2=Campaign');
            $table->tinyInteger('credit_return_type')->default(0);
            $table->timestamps();

            $table->foreign('asb_paid_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('asb_pay_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('asb_pay_mode')->references('id')->on('acc_pay_methods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_sms_balances');
    }
}
