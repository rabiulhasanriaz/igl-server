<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhonebookCampaignContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phonebook_campaign_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->comment('category id');
            $table->string('name', 30)->nullable()->comment('contact name');
            $table->string('designation', 30)->nullable()->comment('contact person designation');
            $table->string('phone_number',  25)->comment('number');
            $table->tinyInteger('status')->default('1')->comment('1=Active, 0=InActive');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('phonebook_campaign_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phonebook_campaign_contacts');
    }
}
