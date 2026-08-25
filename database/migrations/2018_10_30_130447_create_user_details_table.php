<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('domain_name', 50)->nullable()->comment('Reseller Company Domain');
            $table->double('limit')->default(0);
            $table->string('company_name', 50);
            $table->string('designation', 60)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('logo', 200)->nullable();
            $table->timestamp('exp_date')->nullable();
            $table->string('user_p', 20)->nullable();
            $table->string('last_log_ip', 25)->nullable()->comment('Last Login IP');
            $table->string('last_log_os', 20)->nullable()->comment('Last Login OS');
            $table->string('api_key', 30)->unique()->comment('Api unique key');
            $table->string('facebookid', 60)->default(' https://www.facebook.com');
            $table->string('hotline', 15)->nullable();
            $table->string('logout_url', 50)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_details');
    }
}
