<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('admin_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aa_create_by', 10)->comment('Who create this user');
            $table->string('aa_com_domain', 50)->comment('Reseller Company Domain');
            $table->double('aa_limit');
            $table->string('aa_company_name', 50);
            $table->string('aa_user_name', 50)->unique();
            $table->string('aa_email', 60)->unique();
            $table->string('aa_cellphone', 15)->unique();
            $table->string('aa_password', 200);
            $table->string('aa_designation', 60)->nullable();
            $table->string('aa_address', 200);
            $table->string('aa_logo', 200)->nullable();
            $table->tinyInteger('aa_status')->default('1')->comment('1=active, 2=expire, 3=suspend');
            $table->tinyInteger('aa_user_type')->default('1')->comment('1=admin, 2=user');
            $table->timestamp('aa_reg_date');
            $table->timestamp('aa_exp_date')->nullable();
            $table->string('aa_last_log_ip', 25)->nullable()->comment('Last Login IP');
            $table->string('aa_last_log_os', 20)->nullable()->comment('Last Login OS');
            $table->string('aa_api_key', 30)->unique()->comment('Api unique key');
            $table->string('aa_facebookid', 60)->default(' https://www.facebook.com');
            $table->string('aa_senderId', 20)->nullable();
            $table->string('aa_hotline', 15)->nullable();
            $table->string('aa_logout_url', 50)->nullable();
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
        Schema::dropIfExists('admin_users');
    }
}
