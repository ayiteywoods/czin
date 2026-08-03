<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductVariantSeeder extends Seeder
{
    public function run(?Product $product = null): void
    {
        if ($product) {
            $this->seedVariants($product);

            return;
        }

        Product::query()->each(function (Product $product) {
            $this->seedVariants($product);
        });
    }

    private function seedVariants(Product $product): void
    {
        $portions = ['Regular', 'Large'];
        $options = ['Standard', 'Mild', 'Spicy'];

        $product->variants()->delete();
        $totalStock = 0;
        $variantIndex = 0;

        foreach ($portions as $portion) {
            foreach ($options as $option) {
                $variantIndex++;
                $quantity = 8 + ($variantIndex % 5);
                $totalStock += $quantity;

                ProductVariant::query()->create([
                    'product_id' => $product->id,
                    'sku' => strtoupper("{$product->sku}-".Str::slug($portion, '').'-'.Str::slug($option, '')),
                    'size' => $portion,
                    'color' => $option,
                    'heel_length' => null,
                    'quantity' => $quantity,
                    'is_active' => true,
                ]);
            }
        }

        $product->update(['quantity' => $totalStock]);
    }
}
