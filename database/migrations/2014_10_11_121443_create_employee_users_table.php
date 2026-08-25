<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('create_by')->nullable();
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->float('commission', 10)->default('0');
            $table->string('password', 100);
            $table->string('employee_p', 100);
            $table->string('avatar')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->rememberToken();
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
        Schema::dropIfExists('employee_users');
    }
}
