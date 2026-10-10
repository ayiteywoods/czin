<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('home_sections', 'carousel_paths')) {
            Schema::table('home_sections', function (Blueprint $table) {
                $table->json('carousel_paths')->nullable();
            });
        }

        $defaults = [
            'images/brand/food-hero-1.jpg',
            'images/brand/food-hero-2.jpg',
            'images/brand/food-hero-3.jpg',
            'images/brand/food-hero-4.jpg',
        ];

        DB::table('home_sections')
            ->where('key', 'hero')
            ->where(function ($query) {
                $query->whereNull('carousel_paths')
                    ->orWhere('carousel_paths', '')
                    ->orWhere('carousel_paths', '[]');
            })
            ->update(['carousel_paths' => json_encode($defaults)]);
    }

    public function down(): void
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->dropColumn('carousel_paths');
        });
    }
};
