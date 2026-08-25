<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadApiRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_api_registration', function (Blueprint $table) {
            $table->increments('operator_id');
            $table->string('operator_name')->comment('The sim operator name');
            $table->string('operator_subname')->nullable()->default(NULL)->comment('The sim operator sub-name');
            $table->string('operator_ip',100)->comment('IP');
            $table->string('operator_subname')->nullable()->default(NULL)->comment('GP=gp, Banglalink=blink,Robi=robi,Teletalk=teletalk');
            $table->string('operator_port',20)->comment('Operator port');
            $table->string('operator_ussd_prepaid',10)->comment('USSD For Prepaid Operator');
            $table->string('operator_ussd_postpaid',10)->comment('USSD For Postpaid Operator');
            $table->string('operator_user')->unsigned()->comment('PORT User id');
            $table->string('operator_password',50)->comment('PORT user password');
            $table->string('operator_flexipin',10)->comment('A pin for flexiload from port');
            $table->string('operator_user_port',20)->comment('Operator User Port');
            $table->float('operator_balance')->comment('PORT balance');
            $table->tinyInteger('operator_status')->default('1')->comment('Default Value is 0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('load_api_registration');
    }
}
