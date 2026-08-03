<?php

namespace Tests\Feature\Api;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    private function createCategoryWithProduct(string $name = 'Mains', string $productName = 'Jollof Rice'): Product
    {
        $category = Category::factory()->create([
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => CategoryStatus::Active,
            'show_in_navbar' => true,
        ]);

        return Product::factory()->create([
            'category_id' => $category->id,
            'name' => $productName,
            'slug' => Str::slug($productName),
            'price' => 55,
            'status' => ProductStatus::Active,
            'published_at' => now()->subDay(),
        ]);
    }

    public function test_menu_returns_categories_with_products(): void
    {
        $this->createCategoryWithProduct();

        $this->getJson('/api/v1/menu')
            ->assertOk()
            ->assertJsonStructure(['data' => [['name', 'slug', 'products']]]);
    }

    public function test_inactive_products_are_excluded_from_menu(): void
    {
        $category = Category::factory()->create([
            'slug' => 'drinks',
            'status' => CategoryStatus::Active,
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'slug' => 'hidden-item',
            'status' => ProductStatus::Inactive,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/v1/menu')->assertOk();

        $products = collect($response->json('data'))->flatMap(fn ($cat) => $cat['products']);
        $this->assertEmpty($products);
    }

    public function test_can_fetch_single_product_by_slug(): void
    {
        $product = $this->createCategoryWithProduct();

        $this->getJson("/api/v1/menu/products/{$product->slug}")
            ->assertOk()
            ->assertJsonPath('data.slug', $product->slug);
    }

    public function test_unknown_product_returns_404(): void
    {
        $this->getJson('/api/v1/menu/products/does-not-exist')->assertNotFound();
    }
}
