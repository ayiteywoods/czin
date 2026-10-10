<?php

namespace App\Support;

use App\Models\Order;
use App\Models\StoreSetting;
use Throwable;

class ShopTax
{
    public static function enabled(): bool
    {
        return self::rate() > 0;
    }

    /**
     * Effective tax rate as a fraction (0.15 = 15%).
     * Reads live store settings — does not rely on Schema::hasColumn (unreliable on some hosts).
     */
    public static function rate(): float
    {
        [$enabled, $rate] = self::settingsState();

        if ($enabled) {
            return $rate;
        }

        if (! config('shop.tax_enabled', false)) {
            return 0.0;
        }

        return self::normalizeRate((float) config('shop.tax_rate', 0));
    }

    public static function baseLabel(): string
    {
        try {
            $settings = StoreSetting::query()->first();
            $attrs = $settings?->getAttributes() ?? [];

            if ($settings && array_key_exists('tax_label', $attrs)) {
                return $settings->taxLabel();
            }
        } catch (Throwable) {
            //
        }

        $label = trim((string) config('shop.tax_label', 'Tax'));

        return $label !== '' ? $label : 'Tax';
    }

    /**
     * Label for live carts / POS, e.g. "VAT (15%)".
     */
    public static function label(): string
    {
        return self::formatLabel(self::baseLabel(), self::rate());
    }

    /**
     * Label for a stored order tax line (always shown, even when tax is 0).
     */
    public static function orderLabel(?Order $order = null): string
    {
        $label = self::baseLabel();

        if ($order && (float) $order->tax > 0) {
            $taxable = max(0, (float) $order->subtotal - (float) $order->discount_amount);

            if ($taxable > 0) {
                return self::formatLabel($label, (float) $order->tax / $taxable);
            }
        }

        // Show configured rate (including 0%) when no tax was charged on the order.
        return self::formatLabel($label, self::rate());
    }

    public static function amountFor(float $taxableSubtotal): float
    {
        return round(max(0, $taxableSubtotal) * self::rate(), 2);
    }

    /**
     * @return array{0: bool, 1: float}
     */
    private static function settingsState(): array
    {
        try {
            $settings = StoreSetting::query()->first();

            if (! $settings) {
                return [false, 0.0];
            }

            $attrs = $settings->getAttributes();

            // Columns not present on this database yet.
            if (! array_key_exists('tax_enabled', $attrs) && ! array_key_exists('tax_rate', $attrs)) {
                return [false, 0.0];
            }

            $enabled = (bool) (int) ($attrs['tax_enabled'] ?? 0);
            $rate = self::normalizeRate((float) ($attrs['tax_rate'] ?? 0));

            return [$enabled, $rate];
        } catch (Throwable) {
            return [false, 0.0];
        }
    }

    /**
     * Accept either 0.15 or 15 as "15%".
     */
    private static function normalizeRate(float $rate): float
    {
        $rate = max(0, $rate);

        if ($rate > 1) {
            $rate = $rate / 100;
        }

        return min(1, $rate);
    }

    private static function formatLabel(string $label, float $rate): string
    {
        $percent = rtrim(rtrim(number_format(max(0, $rate) * 100, 2, '.', ''), '0'), '.');

        if ($percent === '') {
            $percent = '0';
        }

        return $label.' ('.$percent.'%)';
    }
}
