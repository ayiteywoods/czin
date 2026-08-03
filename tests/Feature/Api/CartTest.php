<?php

namespace Tests\Feature\Api;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private string $deviceId = 'test-device-abc123';

    private function makeProduct(float $price = 30): Product
    {
        $category = Category::factory()->create([
            'slug' => 'mains-'.Str::random(4),
            'status' => CategoryStatus::Active,
        ]);

        return Product::factory()->create([
            'category_id' => $category->id,
            'slug' => 'product-'.Str::random(6),
            'price' => $price,
            'status' => ProductStatus::Active,
            'published_at' => now()->subDay(),
        ]);
    }

    public function test_guest_can_view_empty_cart(): void
    {
        $this->withHeaders(['X-Device-Id' => $this->deviceId])
            ->getJson('/api/v1/cart')
            ->assertOk()
            ->assertJsonStructure(['data' => ['items', 'subtotal']]);
    }

    public function test_guest_can_add_item_to_cart(): void
    {
        $product = $this->makeProduct();

        $this->withHeaders(['X-Device-Id' => $this->deviceId])
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'quantity' => 2,
            ])
            ->assertOk();

        $cart = $this->withHeaders(['X-Device-Id' => $this->deviceId])
            ->getJson('/api/v1/cart')
            ->assertOk();

        $this->assertCount(1, $cart->json('data.items'));
    }

    public function test_authenticated_user_can_add_item_to_cart(): void
    {
        $user = User::factory()->create(['role' => UserRole::Customer, 'is_active' => true]);
        $token = $user->createToken('test')->plainTextToken;
        $product = $this->makeProduct();

        $this->withToken($token)
            ->withHeaders(['X-Device-Id' => $this->deviceId])
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'quantity' => 1,
            ])
            ->assertOk();
    }

    public function test_guest_can_clear_cart(): void
    {
        $product = $this->makeProduct();

        $headers = ['X-Device-Id' => $this->deviceId];

        $this->withHeaders($headers)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->withHeaders($headers)->deleteJson('/api/v1/cart')->assertOk();

        $cart = $this->withHeaders($headers)->getJson('/api/v1/cart')->assertOk();
        $this->assertCount(0, $cart->json('data.items'));
    }
}
