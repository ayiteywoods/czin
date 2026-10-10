<?php

namespace App\Support;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ShopTax
{
    public static function enabled(): bool
    {
        return self::rate() > 0;
    }

    /**
     * Effective tax rate as a fraction (0.15 = 15%). Always prefers live store settings.
     */
    public static function rate(): float
    {
        try {
            if (Schema::hasColumn('store_settings', 'tax_enabled')) {
                $settings = StoreSetting::query()->first();

                if ($settings && $settings->taxEnabled()) {
                    return $settings->taxRate();
                }

                if ($settings) {
                    return 0.0;
                }
            }
        } catch (Throwable) {
            // Fall through to config / env.
        }

        if (! config('shop.tax_enabled', false)) {
            return 0.0;
        }

        return max(0, (float) config('shop.tax_rate', 0));
    }

    public static function baseLabel(): string
    {
        try {
            if (Schema::hasColumn('store_settings', 'tax_label')) {
                $settings = StoreSetting::query()->first();

                if ($settings) {
                    return $settings->taxLabel();
                }
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
     * Label for a stored order tax line (uses the rate that was charged when possible).
     */
    public static function orderLabel(?Order $order = null): string
    {
        $label = self::baseLabel();

        if (! $order || (float) $order->tax <= 0) {
            return $label;
        }

        $taxable = max(0, (float) $order->subtotal - (float) $order->discount_amount);

        if ($taxable <= 0) {
            return $label;
        }

        $rate = (float) $order->tax / $taxable;

        return self::formatLabel($label, $rate);
    }

    private static function formatLabel(string $label, float $rate): string
    {
        if ($rate <= 0) {
            return $label;
        }

        $percent = rtrim(rtrim(number_format($rate * 100, 2, '.', ''), '0'), '.');

        return $label.' ('.$percent.'%)';
    }
}
