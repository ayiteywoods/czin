<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Throwable;

class StoreSettingSchema
{
    public static function ensureTaxColumns(): bool
    {
        self::forgetSchemaCache();

        if (self::taxColumnsExist()) {
            return true;
        }

        try {
            if (! Schema::hasColumn('store_settings', 'tax_enabled')) {
                Schema::table('store_settings', function (Blueprint $table) {
                    $table->boolean('tax_enabled')->default(false);
                });
            }

            if (! Schema::hasColumn('store_settings', 'tax_rate')) {
                Schema::table('store_settings', function (Blueprint $table) {
                    $table->decimal('tax_rate', 8, 4)->default(0);
                });
            }

            if (! Schema::hasColumn('store_settings', 'tax_label')) {
                Schema::table('store_settings', function (Blueprint $table) {
                    $table->string('tax_label')->nullable();
                });
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        self::forgetSchemaCache();

        return self::taxColumnsExist();
    }

    private static function taxColumnsExist(): bool
    {
        return Schema::hasColumn('store_settings', 'tax_enabled')
            && Schema::hasColumn('store_settings', 'tax_rate')
            && Schema::hasColumn('store_settings', 'tax_label');
    }

    private static function forgetSchemaCache(): void
    {
        try {
            Artisan::call('schema:clear');
        } catch (Throwable) {
            //
        }
    }
}
