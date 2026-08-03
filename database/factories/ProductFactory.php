<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.Str::random(4),
            'sku' => strtoupper(Str::random(8)),
            'price' => fake()->randomFloat(2, 10, 100),
            'discount_price' => null,
            'description' => fake()->sentence(),
            'quantity' => 0,
            'status' => ProductStatus::Active,
            'is_86ed' => false,
            'published_at' => now()->subDay(),
        ];
    }
}
