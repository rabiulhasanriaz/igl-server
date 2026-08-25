<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('create_by')->unsigned()->nullable();
            $table->integer('employee_user_id')->unsigned()->nullable();
            $table->string('name', 50);
            $table->string('email', 50)->unique()->nullable();
            $table->string('cellphone', 25)->unique()->nullable();
            $table->string('password', 150);
            $table->string('flexipin')->nullable()->comment('A pin for flexiload from users');
            $table->tinyInteger('status')->default('1')->comment('1=active, 2=suspend, 3=expired');
            $table->tinyInteger('role')->default('3')->comment('1=Root, 2=Root user1, 3=Root user2, 4=Reseller, 5=User');
            $table->tinyInteger('position')->nullable();
            $table->tinyInteger('employee_limit')->default(2);
            $table->string('flexiload_type')->default('0')->comment('In string describe each number to determine hows eligible ...'));
            $table->mediumInteger('flexiload_limit')->default('10000')->comment('Minimum balance amount to make a load   ');
            $table->float('flexiload_commission')->default(0)->comment('flexiload commission')
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
