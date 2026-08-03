<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private array $imagesBySku = [
        'MAINS-001' => 'images/products/jollof-rice.jpg',
        'MAINS-002' => 'images/products/grilled-chicken.jpg',
        'MAINS-003' => 'images/products/waakye.jpg',
        'SIDES-001' => 'images/products/fried-plantain.jpg',
        'SIDES-002' => 'images/products/coleslaw.jpg',
        'SIDES-003' => 'images/products/banku.jpg',
        'DRINKS-001' => 'images/products/sobolo.jpg',
        'DRINKS-002' => 'images/products/fresh-juice.jpg',
        'DRINKS-003' => 'images/products/bottled-water.jpg',
        'DESSERTS-001' => 'images/products/chocolate-cake.jpg',
        'DESSERTS-002' => 'images/products/ice-cream.jpg',
        'DESSERTS-003' => 'images/products/puff-puff.jpg',
    ];

    public function run(): void
    {
        foreach ($this->imagesBySku as $sku => $path) {
            $product = Product::query()->where('sku', $sku)->first();

            if (! $product) {
                continue;
            }

            ProductImage::query()->updateOrCreate(
                [
                    'product_id' => $product->id,
                    'path' => $path,
                ],
                [
                    'is_primary' => true,
                    'sort_order' => 0,
                ]
            );
        }
    }
}
