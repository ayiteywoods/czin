<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('about_image_path');
            $table->string('logo_text_path')->nullable()->after('logo_path');
            $table->string('logo_text_on_light_path')->nullable()->after('logo_text_path');
            $table->string('footer_logo_path')->nullable()->after('logo_text_on_light_path');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'logo_text_path',
                'logo_text_on_light_path',
                'footer_logo_path',
            ]);
        });
    }
};
