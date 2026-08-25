<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivedPdfMessageIndex extends Migration
{
    public function up()
    {
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->index(['campaign_id', 'id'], 'sc_campaign_id_idx');
        });
    }

    public function down()
    {
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->dropIndex('sc_campaign_id_idx');
        });
    }
}
