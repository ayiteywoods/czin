<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            if (! Schema::hasColumn('promotions', 'category_ids')) {
                $table->json('category_ids')->nullable()->after('category_id');
            }

            if (! Schema::hasColumn('promotions', 'product_ids')) {
                $table->json('product_ids')->nullable()->after('product_id');
            }
        });

        foreach (DB::table('promotions')->orderBy('id')->get() as $promotion) {
            $categoryIds = [];
            $productIds = [];

            if (! empty($promotion->category_ids)) {
                $decoded = json_decode((string) $promotion->category_ids, true);
                if (is_array($decoded)) {
                    $categoryIds = $decoded;
                }
            } elseif (! empty($promotion->category_id)) {
                $categoryIds = [(int) $promotion->category_id];
            }

            if (! empty($promotion->product_ids)) {
                $decoded = json_decode((string) $promotion->product_ids, true);
                if (is_array($decoded)) {
                    $productIds = $decoded;
                }
            } elseif (! empty($promotion->product_id)) {
                $productIds = [(int) $promotion->product_id];
            }

            DB::table('promotions')->where('id', $promotion->id)->update([
                'category_ids' => $categoryIds === [] ? null : json_encode(array_values(array_unique(array_map('intval', $categoryIds)))),
                'product_ids' => $productIds === [] ? null : json_encode(array_values(array_unique(array_map('intval', $productIds)))),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $columns = collect(['category_ids', 'product_ids'])
                ->filter(fn (string $column) => Schema::hasColumn('promotions', $column))
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
