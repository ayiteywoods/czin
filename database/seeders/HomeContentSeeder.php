<?php

namespace Database\Seeders;

use App\Models\HomeSection;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'key' => HomeSection::KEY_HERO,
                'name' => 'Hero section',
                'eyebrow' => 'Order · Pickup · Delivery',
                'title' => 'Fresh meals,',
                'title_highlight' => 'made to order.',
                'body' => 'Homestyle Ghanaian favourites and everyday comfort food — cooked fresh and delivered across Accra.',
                'primary_label' => 'View Menu',
                'primary_url' => '/shop',
                'secondary_label' => 'Today’s Specials',
                'secondary_url' => '/shop',
                'image_path' => 'images/brand/food-hero-1.jpg',
                'carousel_paths' => HomeSection::defaultHeroCarouselPaths(),
                'sort_order' => 1,
            ],
            [
                'key' => HomeSection::KEY_FREE_DELIVERY,
                'name' => 'Free delivery banner',
                'body' => 'Free delivery on orders over {currency_symbol} {threshold} — Hot meals delivered across Accra',
                'sort_order' => 2,
            ],
            [
                'key' => HomeSection::KEY_SHOP_CATEGORY,
                'name' => 'Shop by category',
                'eyebrow' => 'Our Menu',
                'title' => 'Browse by Category',
                'body' => 'Mains, sides, drinks, and sweets — pick what you are craving',
                'primary_label' => 'Full Menu',
                'primary_url' => '/shop',
                'sort_order' => 3,
            ],
            [
                'key' => HomeSection::KEY_CTA,
                'name' => 'Call to action',
                'eyebrow' => 'Hungry?',
                'title' => 'Skip the wait. Order from CZIN today.',
                'body' => 'Fresh kitchen favourites, clear portions, and reliable delivery across Accra.',
                'primary_label' => 'Order Now',
                'primary_url' => '/shop',
                'secondary_label' => 'See Specials',
                'secondary_url' => '/shop',
                'sort_order' => 4,
            ],
            [
                'key' => HomeSection::KEY_NEW_ARRIVALS,
                'name' => 'New arrivals',
                'eyebrow' => 'Kitchen Fresh',
                'title' => 'Popular Dishes',
                'body' => 'Guest favourites from the CZIN kitchen.',
                'primary_label' => 'Browse Menu',
                'primary_url' => '/shop',
                'sort_order' => 5,
            ],
            [
                'key' => HomeSection::KEY_TESTIMONIALS_HEADER,
                'name' => 'Testimonials header',
                'eyebrow' => 'Reviews',
                'title' => 'What our guests say',
                'sort_order' => 6,
            ],
            [
                'key' => HomeSection::KEY_DELIVERY_NOTICE,
                'name' => 'Delivery notice',
                'title' => 'Delivery Information',
                'body' => 'Delivery fee is paid directly to the dispatch rider upon arrival. Fee varies by location across Accra and surrounding areas.',
                'sort_order' => 7,
            ],
        ];

        foreach ($sections as $section) {
            HomeSection::query()->updateOrCreate(
                ['key' => $section['key']],
                array_merge(['is_active' => true], $section),
            );
        }

        Testimonial::query()->delete();

        $testimonials = [
            [
                'quote' => 'The jollof tastes like home. Portions are generous and delivery was still hot. Ordering from CZIN is my new weekday habit.',
                'author_name' => 'Ama K.',
                'rating' => 5,
                'sort_order' => 1,
            ],
            [
                'quote' => 'Great flavours, clear menu options, and friendly service. The grilled chicken with banku was excellent.',
                'author_name' => 'Kwame B.',
                'rating' => 5,
                'sort_order' => 2,
            ],
            [
                'quote' => 'Best takeaway experience I have had in Accra lately. Food arrived on time and everything was packed carefully.',
                'author_name' => 'Efua S.',
                'rating' => 5,
                'sort_order' => 3,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::query()->create(array_merge(['is_active' => true], $testimonial));
        }
    }
}
