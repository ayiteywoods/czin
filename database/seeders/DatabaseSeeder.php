<?php

namespace Database\Seeders;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@czin.com'],
            [
                'first_name' => 'CZIN',
                'last_name' => 'Admin',
                'name' => 'CZIN Admin',
                'phone' => '0200000000',
                'password' => 'password',
                'role' => UserRole::Admin,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $categories = [
            [
                'name' => 'Mains',
                'description' => 'Hearty plates and signature restaurant favourites.',
                'navbar_sort_order' => 1,
                'products' => [
                    ['name' => 'Jollof Rice Special', 'price' => 55, 'discount' => 45, 'desc' => 'Smoky party jollof served with your choice of protein and salad.'],
                    ['name' => 'Grilled Chicken Plate', 'price' => 70, 'discount' => null, 'desc' => 'Charcoal-grilled chicken with banku or fries and pepper sauce.'],
                    ['name' => 'Waakye Combo', 'price' => 50, 'discount' => null, 'desc' => 'Classic waakye with gari, spaghetti, egg, and shito.'],
                ],
            ],
            [
                'name' => 'Sides',
                'description' => 'Extras and shareable sides to complete your meal.',
                'navbar_sort_order' => 2,
                'products' => [
                    ['name' => 'Fried Plantain', 'price' => 20, 'discount' => null, 'desc' => 'Crispy golden plantain, lightly seasoned.'],
                    ['name' => 'Coleslaw', 'price' => 15, 'discount' => null, 'desc' => 'Fresh creamy slaw — a cool contrast to spicy mains.'],
                    ['name' => 'Extra Banku', 'price' => 12, 'discount' => null, 'desc' => 'Soft banku portion to round out your plate.'],
                ],
            ],
            [
                'name' => 'Drinks',
                'description' => 'Cold drinks and local favourites to go with your order.',
                'navbar_sort_order' => 3,
                'products' => [
                    ['name' => 'Sobolo', 'price' => 15, 'discount' => null, 'desc' => 'Chilled hibiscus drink with a hint of ginger.'],
                    ['name' => 'Fresh Juice', 'price' => 18, 'discount' => null, 'desc' => 'Seasonal fruit blend, made to order.'],
                    ['name' => 'Bottled Water', 'price' => 5, 'discount' => null, 'desc' => '500ml still water.'],
                ],
            ],
            [
                'name' => 'Desserts',
                'description' => 'Sweet finishes after a satisfying meal.',
                'navbar_sort_order' => 4,
                'products' => [
                    ['name' => 'Chocolate Cake Slice', 'price' => 25, 'discount' => 20, 'desc' => 'Rich chocolate sponge with cream frosting.'],
                    ['name' => 'Ice Cream Cup', 'price' => 18, 'discount' => null, 'desc' => 'Two scoops of rotating seasonal flavours.'],
                    ['name' => 'Puff Puff (6pcs)', 'price' => 15, 'discount' => null, 'desc' => 'Warm golden doughnuts dusted with sugar.'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::query()->updateOrCreate(
                ['slug' => Str::slug($categoryData['name'])],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'image' => null,
                    'status' => CategoryStatus::Active,
                    'show_in_navbar' => true,
                    'navbar_sort_order' => $categoryData['navbar_sort_order'],
                    'shop_sort_order' => $categoryData['navbar_sort_order'],
                ]
            );

            foreach ($categoryData['products'] as $index => $item) {
                $sku = strtoupper(Str::slug($categoryData['name'], '')).'-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
                $product = Product::query()->updateOrCreate(
                    ['sku' => $sku],
                    [
                        'category_id' => $category->id,
                        'name' => $item['name'],
                        'slug' => Str::slug($item['name']),
                        'price' => $item['price'],
                        'discount_price' => $item['discount'],
                        'description' => $item['desc'],
                        'quantity' => 0,
                        'status' => ProductStatus::Active,
                        'published_at' => now()->subDays($index),
                    ]
                );

                $this->call(ProductVariantSeeder::class, false, ['product' => $product]);
            }
        }

        $this->call(ShippingSeeder::class);
        $this->call(CouponSeeder::class);
        $this->call(ProductImageSeeder::class);
        $this->call(DiningTableSeeder::class);
        $this->call(HomeContentSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(EmailTemplateSeeder::class);
    }
}
