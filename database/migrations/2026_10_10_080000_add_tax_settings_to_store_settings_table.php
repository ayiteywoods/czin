<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('store_settings', 'tax_enabled')) {
                $table->boolean('tax_enabled')->default(false);
            }

            if (! Schema::hasColumn('store_settings', 'tax_rate')) {
                $table->decimal('tax_rate', 8, 4)->default(0);
            }

            if (! Schema::hasColumn('store_settings', 'tax_label')) {
                $table->string('tax_label')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $columns = collect(['tax_enabled', 'tax_rate', 'tax_label'])
                ->filter(fn (string $column) => Schema::hasColumn('store_settings', $column))
                ->values()
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
