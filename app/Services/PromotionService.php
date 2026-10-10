<?php

namespace App\Services;

use App\Enums\PromotionType;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class PromotionService
{
    /** @var Collection<int, Promotion>|null */
    private static ?Collection $sharedActivePromotions = null;

    /** @var array<int, list<int>> */
    private static array $sharedCategoryIds = [];

    public function sellingPriceFor(Product $product): float
    {
        $candidates = [(float) $product->price];

        if ($product->discount_price !== null) {
            $candidates[] = (float) $product->discount_price;
        }

        $promotion = $this->bestPromotionFor($product);

        if ($promotion) {
            $candidates[] = $this->applyPromotion((float) $product->price, $promotion);
        }

        return round(max(0, min($candidates)), 2);
    }

    public function compareAtPriceFor(Product $product): ?float
    {
        $base = (float) $product->price;
        $selling = $this->sellingPriceFor($product);

        return $selling < ($base - 0.0001) ? $base : null;
    }

    public function bestPromotionFor(Product $product): ?Promotion
    {
        $matches = $this->activePromotions()
            ->filter(fn (Promotion $promotion) => $this->matchesProduct($promotion, $product));

        if ($matches->isEmpty()) {
            return null;
        }

        // Prefer the promotion that gives the lowest unit price (BOGO keeps full price).
        return $matches
            ->sortBy(fn (Promotion $promotion) => $this->applyPromotion((float) $product->price, $promotion))
            ->first();
    }

    public function promotionBadge(Product $product): ?string
    {
        $promotion = $this->bestPromotionFor($product);

        if ($promotion) {
            return match ($promotion->type) {
                PromotionType::Percent => rtrim(rtrim(number_format((float) $promotion->value, 2, '.', ''), '0'), '.').'% off',
                PromotionType::Fixed => 'Save '.config('shop.currency_symbol').' '.number_format((float) $promotion->value, 2),
                PromotionType::Bogo => 'BOGO',
            };
        }

        if ($product->discount_price !== null && (float) $product->discount_price < (float) $product->price) {
            return 'Sale';
        }

        return null;
    }

    /**
     * @return array{name: string, type: string, badge: string}|null
     */
    public function promotionPayload(Product $product): ?array
    {
        $promotion = $this->bestPromotionFor($product);

        if (! $promotion) {
            return null;
        }

        return [
            'id' => $promotion->id,
            'name' => $promotion->name,
            'type' => $promotion->type->value,
            'badge' => $this->promotionBadge($product) ?? $promotion->name,
        ];
    }

    /**
     * @return Collection<int, Promotion>
     */
    public function activePromotions(): Collection
    {
        if (self::$sharedActivePromotions !== null) {
            return self::$sharedActivePromotions;
        }

        if (! Schema::hasTable('promotions')) {
            return self::$sharedActivePromotions = collect();
        }

        $now = now();

        self::$sharedActivePromotions = Promotion::query()
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->with('category')
            ->orderByDesc('id')
            ->get()
            ->filter(fn (Promotion $promotion) => $this->isCurrentlyScheduled($promotion, $now))
            ->values();

        return self::$sharedActivePromotions;
    }

    public function applyPromotion(float $basePrice, Promotion $promotion): float
    {
        $value = (float) $promotion->value;

        return match ($promotion->type) {
            PromotionType::Percent => round(max(0, $basePrice * (1 - ($value / 100))), 2),
            PromotionType::Fixed => round(max(0, $basePrice - $value), 2),
            // BOGO is fulfilled at cart level later; unit price stays full for now.
            PromotionType::Bogo => round($basePrice, 2),
        };
    }

    private function matchesProduct(Promotion $promotion, Product $product): bool
    {
        $productIds = $promotion->targetProductIds();
        $categoryIds = $promotion->targetCategoryIds();

        if ($productIds === [] && $categoryIds === []) {
            return false;
        }

        // Explicit product targets.
        if ($productIds !== [] && in_array((int) $product->id, $productIds, true)) {
            return true;
        }

        // Category targets (including subcategories of each selected category).
        if ($categoryIds !== []) {
            $productCategoryId = $this->positiveId($product->category_id);

            if ($productCategoryId === null) {
                return false;
            }

            $allowedCategoryIds = [];

            foreach ($categoryIds as $categoryId) {
                foreach ($this->expandCategoryIds($categoryId) as $expandedId) {
                    $allowedCategoryIds[$expandedId] = true;
                }
            }

            return isset($allowedCategoryIds[$productCategoryId]);
        }

        return false;
    }

    private function positiveId(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        $id = (int) $value;

        return $id > 0 ? $id : null;
    }

    /**
     * @return list<int>
     */
    private function expandCategoryIds(int $categoryId): array
    {
        if (isset(self::$sharedCategoryIds[$categoryId])) {
            return self::$sharedCategoryIds[$categoryId];
        }

        $category = Category::query()->with('children:id,parent_id')->find($categoryId);

        if (! $category) {
            return self::$sharedCategoryIds[$categoryId] = [$categoryId];
        }

        return self::$sharedCategoryIds[$categoryId] = $category->filterableProductCategoryIds();
    }

    private function isCurrentlyScheduled(Promotion $promotion, Carbon $now): bool
    {
        $days = $promotion->days_of_week;

        if (is_array($days) && $days !== []) {
            $today = (int) $now->dayOfWeek; // 0 = Sunday
            $normalized = collect($days)
                ->map(fn ($day) => (int) $day)
                ->all();

            if (! in_array($today, $normalized, true)) {
                return false;
            }
        }

        $startTime = $this->normalizeTime($promotion->start_time);
        $endTime = $this->normalizeTime($promotion->end_time);

        if ($startTime === null && $endTime === null) {
            return true;
        }

        $current = $now->format('H:i:s');

        if ($startTime !== null && $endTime !== null) {
            if ($startTime <= $endTime) {
                return $current >= $startTime && $current <= $endTime;
            }

            // Overnight window, e.g. 22:00–02:00
            return $current >= $startTime || $current <= $endTime;
        }

        if ($startTime !== null) {
            return $current >= $startTime;
        }

        return $current <= $endTime;
    }

    private function normalizeTime(mixed $time): ?string
    {
        if ($time === null || $time === '') {
            return null;
        }

        $value = (string) $time;

        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            return $value.':00';
        }

        if (preg_match('/^\d{2}:\d{2}:\d{2}/', $value)) {
            return substr($value, 0, 8);
        }

        return null;
    }
}
