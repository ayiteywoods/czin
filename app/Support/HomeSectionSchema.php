<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class HomeSectionSchema
{
    public static function ensureCarouselPathsColumn(): bool
    {
        self::forgetSchemaCache();

        if (Schema::hasColumn('home_sections', 'carousel_paths')) {
            return true;
        }

        try {
            Schema::table('home_sections', function (Blueprint $table) {
                $table->longText('carousel_paths')->nullable();
            });
        } catch (Throwable $exception) {
            report($exception);
            self::forgetSchemaCache();

            return Schema::hasColumn('home_sections', 'carousel_paths');
        }

        self::forgetSchemaCache();

        if (! Schema::hasColumn('home_sections', 'carousel_paths')) {
            return false;
        }

        $defaults = json_encode([
            'images/brand/food-hero-1.jpg',
            'images/brand/food-hero-2.jpg',
            'images/brand/food-hero-3.jpg',
            'images/brand/food-hero-4.jpg',
        ]);

        try {
            DB::table('home_sections')
                ->where('key', 'hero')
                ->where(function ($query) {
                    $query->whereNull('carousel_paths')
                        ->orWhere('carousel_paths', '')
                        ->orWhere('carousel_paths', '[]');
                })
                ->update(['carousel_paths' => $defaults]);
        } catch (Throwable $exception) {
            report($exception);
        }

        return true;
    }

    private static function forgetSchemaCache(): void
    {
        try {
            Artisan::call('schema:clear');
        } catch (Throwable) {
            // Older Laravel / no schema cache command — ignore.
        }

        try {
            Schema::getConnection()->getSchemaBuilder()->getColumns('home_sections');
        } catch (Throwable) {
            // Force a fresh schema lookup on the next hasColumn call.
        }

        if (method_exists(Schema::getFacadeRoot(), 'clearCache')) {
            try {
                Schema::clearCache();
            } catch (Throwable) {
                //
            }
        }
    }
}
