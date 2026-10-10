<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class PromotionSchema
{
    public static function ensureMultiTargetColumns(): bool
    {
        self::forgetSchemaCache();

        if (self::columnsExist()) {
            return true;
        }

        try {
            if (! Schema::hasColumn('promotions', 'category_ids')) {
                Schema::table('promotions', function (Blueprint $table) {
                    $table->longText('category_ids')->nullable();
                });
            }

            if (! Schema::hasColumn('promotions', 'product_ids')) {
                Schema::table('promotions', function (Blueprint $table) {
                    $table->longText('product_ids')->nullable();
                });
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        self::forgetSchemaCache();

        if (! self::columnsExist()) {
            return false;
        }

        try {
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
        } catch (Throwable $exception) {
            report($exception);
        }

        return true;
    }

    private static function columnsExist(): bool
    {
        return Schema::hasColumn('promotions', 'category_ids')
            && Schema::hasColumn('promotions', 'product_ids');
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
