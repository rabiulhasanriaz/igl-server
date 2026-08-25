<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportPerformanceIndexes extends Migration
{
    public function up()
    {
        Schema::table('sms_campaign_ids', function (Blueprint $table) {
            $table->index(
                ['user_id', 'sci_deal_type', 'sci_from_api', 'sci_targeted_time'],
                'sci_user_deal_api_target_idx'
            );
        });

        Schema::table('sms_desktop_campaign_ids', function (Blueprint $table) {
            $table->index(
                ['user_id', 'sdci_deal_type', 'sdci_from_api', 'sdci_targeted_time'],
                'sdci_user_deal_api_target_idx'
            );
        });

        Schema::table('acc_user_credit_histories', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'uch_user_created_idx');
            $table->index(['campaign_id', 'user_id'], 'uch_campaign_user_idx');
        });

        Schema::table('sms_campaign_24hs', function (Blueprint $table) {
            $table->index(['campaign_id', 'user_id', 'id'], 'sct_campaign_user_id_idx');
        });

        Schema::table('sms_cam_pendings', function (Blueprint $table) {
            $table->index(['campaign_id', 'user_id', 'id'], 'scp_campaign_user_id_idx');
        });
    }

    public function down()
    {
        Schema::table('sms_campaign_ids', function (Blueprint $table) {
            $table->dropIndex('sci_user_deal_api_target_idx');
        });

        Schema::table('sms_desktop_campaign_ids', function (Blueprint $table) {
            $table->dropIndex('sdci_user_deal_api_target_idx');
        });

        Schema::table('acc_user_credit_histories', function (Blueprint $table) {
            $table->dropIndex('uch_user_created_idx');
            $table->dropIndex('uch_campaign_user_idx');
        });

        Schema::table('sms_campaign_24hs', function (Blueprint $table) {
            $table->dropIndex('sct_campaign_user_id_idx');
        });

        Schema::table('sms_cam_pendings', function (Blueprint $table) {
            $table->dropIndex('scp_campaign_user_id_idx');
        });
    }
}
