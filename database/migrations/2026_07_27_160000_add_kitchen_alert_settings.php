<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->boolean('kitchen_sms_enabled')->default(false)->after('social_whatsapp');
            $table->string('kitchen_sms_phone')->nullable()->after('kitchen_sms_enabled');
            $table->boolean('kitchen_whatsapp_enabled')->default(false)->after('kitchen_sms_phone');
            $table->string('kitchen_whatsapp_phone')->nullable()->after('kitchen_whatsapp_enabled');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('kitchen_alert_sent_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'kitchen_sms_enabled',
                'kitchen_sms_phone',
                'kitchen_whatsapp_enabled',
                'kitchen_whatsapp_phone',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('kitchen_alert_sent_at');
        });
    }
};
