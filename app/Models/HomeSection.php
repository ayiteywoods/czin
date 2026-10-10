<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeSection extends Model
{
    public const KEY_HERO = 'hero';

    public const KEY_FREE_DELIVERY = 'free_delivery_banner';

    public const KEY_SHOP_CATEGORY = 'shop_by_category';

    public const KEY_CTA = 'cta';

    public const KEY_NEW_ARRIVALS = 'new_arrivals';

    public const KEY_TESTIMONIALS_HEADER = 'testimonials_header';

    public const KEY_DELIVERY_NOTICE = 'delivery_notice';

    protected $fillable = [
        'key',
        'name',
        'eyebrow',
        'title',
        'title_highlight',
        'body',
        'primary_label',
        'primary_url',
        'secondary_label',
        'secondary_url',
        'image_path',
        'carousel_paths',
        'is_active',
        'sort_order',
    ];

    /** @return list<string> */
    public static function defaultHeroCarouselPaths(): array
    {
        return [
            'images/brand/food-hero-1.jpg',
            'images/brand/food-hero-2.jpg',
            'images/brand/food-hero-3.jpg',
            'images/brand/food-hero-4.jpg',
        ];
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'carousel_paths' => 'array',
        ];
    }

    public function resolvePathUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'images/') || str_starts_with($path, 'http')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    public function imageUrl(): ?string
    {
        return $this->resolvePathUrl($this->image_path);
    }

    /**
     * @return list<string>
     */
    public function carouselUrls(): array
    {
        $urls = collect($this->carousel_paths ?? [])
            ->filter()
            ->map(fn (string $path) => $this->resolvePathUrl($path))
            ->filter()
            ->values();

        if ($urls->isEmpty() && $this->imageUrl()) {
            $urls->push($this->imageUrl());
        }

        if ($urls->isEmpty()) {
            return collect(self::defaultHeroCarouselPaths())
                ->map(fn (string $path) => $this->resolvePathUrl($path))
                ->filter()
                ->values()
                ->all();
        }

        return $urls->unique()->values()->all();
    }

    public function isStoredUploadPath(?string $path): bool
    {
        return $path
            && ! str_starts_with($path, 'images/')
            && ! str_starts_with($path, 'http');
    }

    public function resolvedUrl(?string $url): string
    {
        if (! $url) {
            return route('shop.index');
        }

        if (Str::startsWith($url, ['http://', 'https://', '/'])) {
            return $url;
        }

        return '/'.ltrim($url, '/');
    }
}
