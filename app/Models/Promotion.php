<?php

namespace App\Models;

use App\Enums\PromotionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'type',
        'value',
        'starts_at',
        'ends_at',
        'category_id',
        'product_id',
        'category_ids',
        'product_ids',
        'is_active',
        'days_of_week',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'type' => PromotionType::class,
            'value' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'days_of_week' => 'array',
            'category_id' => 'integer',
            'product_id' => 'integer',
            'category_ids' => 'array',
            'product_ids' => 'array',
        ];
    }

    /**
     * @return list<int>
     */
    public function targetCategoryIds(): array
    {
        $raw = $this->attributes['category_ids'] ?? null;
        $ids = collect($this->decodeIdList($raw))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->values();

        if ($ids->isEmpty() && $this->category_id) {
            $ids->push((int) $this->category_id);
        }

        return $ids->unique()->values()->all();
    }

    /**
     * @return list<int>
     */
    public function targetProductIds(): array
    {
        $raw = $this->attributes['product_ids'] ?? null;
        $ids = collect($this->decodeIdList($raw))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->values();

        if ($ids->isEmpty() && $this->product_id) {
            $ids->push((int) $this->product_id);
        }

        return $ids->unique()->values()->all();
    }

    /**
     * @return list<int|string>
     */
    private function decodeIdList(mixed $raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }

        if (! is_string($raw) || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function appliesToLabel(): string
    {
        $parts = [];

        $categoryIds = $this->targetCategoryIds();
        $productIds = $this->targetProductIds();

        if ($categoryIds !== []) {
            $names = Category::query()
                ->whereIn('id', $categoryIds)
                ->orderBy('name')
                ->pluck('name');

            $parts[] = $names->isNotEmpty()
                ? 'Categories: '.$names->implode(', ')
                : 'Categories: #'.implode(', #', $categoryIds);
        }

        if ($productIds !== []) {
            $names = Product::query()
                ->whereIn('id', $productIds)
                ->orderBy('name')
                ->pluck('name');

            $parts[] = $names->isNotEmpty()
                ? 'Products: '.$names->implode(', ')
                : 'Products: #'.implode(', #', $productIds);
        }

        return $parts !== [] ? implode(' · ', $parts) : 'Not targeted';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Keep legacy single FK columns in sync with the first selected target.
     *
     * @param  list<int|string>|null  $categoryIds
     * @param  list<int|string>|null  $productIds
     * @return array{category_ids: list<int>|null, product_ids: list<int>|null, category_id: int|null, product_id: int|null}
     */
    public static function normalizeTargets(?array $categoryIds, ?array $productIds): array
    {
        $categories = collect($categoryIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $products = collect($productIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        return [
            'category_ids' => $categories === [] ? null : $categories,
            'product_ids' => $products === [] ? null : $products,
            'category_id' => $categories[0] ?? null,
            'product_id' => $products[0] ?? null,
        ];
    }
}
