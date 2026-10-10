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
                $table->boolean('tax_enabled')->default(false)->after('online_ordering_enabled');
            }

            if (! Schema::hasColumn('store_settings', 'tax_rate')) {
                $table->decimal('tax_rate', 8, 4)->default(0)->after('tax_enabled');
            }

            if (! Schema::hasColumn('store_settings', 'tax_label')) {
                $table->string('tax_label')->nullable()->after('tax_rate');
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
