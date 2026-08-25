<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeUserCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_user_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('eu_id');
            $table->string('eu_ref_id', 50)->comment('Commission reference');
            $table->float('euc_credit')->comment('employee user credit balance');
            $table->float('euc_debit')->comment('employee user debit balance');
            $table->tinyInteger('euc_status')->comment('1=User Account Credit, 2=User Account Debit, 3=Amount Paid(Own)');
            $table->timestamps();

            $table->foreign('eu_id')->references('id')->on('employee_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_user_commissions');
    }
}
