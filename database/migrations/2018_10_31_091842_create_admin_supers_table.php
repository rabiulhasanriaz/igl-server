<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminSupersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('admin_supers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('as_user_name', 50)->unique();
            $table->string('as_email', 50)->unique();
            $table->string('as_cellphone', 15)->unique();
            $table->string('as_password', 200);
            $table->string('as_designation', 30);
            $table->string('as_address', 100);
            $table->string('as_image', 200)->nullable();
            $table->tinyInteger('as_status')->default('1')->comment('1=active, 2=expire, 3=suspend');
            $table->tinyInteger('as_user_type')->default('1')->comment('1=admin, 2=user');
            $table->timestamp('as_last_login_time');
            $table->string('as_last_log_ip', 25)->nullable()->comment('Last Login IP');
            $table->string('as_last_log_os', 20)->nullable()->comment('Last Login OS');
            $table->rememberToken();
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_supers');
    }
}
