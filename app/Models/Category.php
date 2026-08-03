<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'status',
        'show_in_navbar',
        'navbar_sort_order',
        'shop_sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => CategoryStatus::class,
            'show_in_navbar' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('shop_sort_order')->orderBy('name');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    public function isSubcategory(): bool
    {
        return $this->parent_id !== null;
    }

    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeForNavbar(Builder $query): Builder
    {
        return $query
            ->whereNull('parent_id')
            ->where('status', CategoryStatus::Active)
            ->where('show_in_navbar', true)
            ->orderBy('navbar_sort_order')
            ->orderBy('name');
    }

    /**
     * @return Collection<int, Category>
     */
    public static function tree(): Collection
    {
        return static::query()
            ->with(['children' => fn ($query) => $query->orderBy('shop_sort_order')->orderBy('name')])
            ->parents()
            ->orderBy('shop_sort_order')
            ->orderBy('name')
            ->get();
    }

    public function scopeForShopDisplay(Builder $query): Builder
    {
        return $query
            ->where('status', CategoryStatus::Active)
            ->orderBy('shop_sort_order')
            ->orderBy('name');
    }

    /**
     * Category IDs to include when filtering products.
     *
     * @return list<int>
     */
    public function filterableProductCategoryIds(): array
    {
        if ($this->relationLoaded('children') && $this->children->isNotEmpty()) {
            return $this->children
                ->pluck('id')
                ->prepend($this->id)
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if (! $this->relationLoaded('children') && $this->children()->exists()) {
            return $this->children()
                ->pluck('id')
                ->prepend($this->id)
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return [(int) $this->id];
    }

    public function storefrontImageUrl(): ?string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'images/') || str_starts_with($this->image, 'http')) {
                return asset($this->image);
            }

            return asset('storage/'.$this->image);
        }

        return $this->storefrontFallbackImageUrl();
    }

    public function storefrontFallbackImageUrl(): string
    {
        $fallbacks = [
            'mains' => 'images/brand/food-hero-1.jpg',
            'sides' => 'images/brand/food-hero-2.jpg',
            'drinks' => 'images/brand/food-hero-3.jpg',
            'desserts' => 'images/brand/food-hero-4.jpg',
        ];

        $path = $fallbacks[$this->slug]
            ?? $fallbacks[strtolower($this->name)]
            ?? 'images/brand/food-hero-1.jpg';

        return asset($path);
    }

    public function storefrontIcon(): string
    {
        $icons = [
            'mains' => 'bag',
            'sides' => 'tag',
            'drinks' => 'sparkle',
            'desserts' => 'sparkle',
            'specials' => 'sparkle',
            'grills' => 'bag',
            'soups' => 'bag',
        ];

        return $icons[$this->slug] ?? $icons[strtolower($this->name)] ?? 'bag';
    }

    public function storefrontTone(): string
    {
        return match ($this->slug) {
            'mains' => 'mains',
            'sides' => 'sides',
            'drinks' => 'drinks',
            'desserts' => 'desserts',
            default => 'default',
        };
    }
}
