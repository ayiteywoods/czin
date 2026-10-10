<?php

namespace App\Support;

class ShopTax
{
    public static function enabled(): bool
    {
        return (bool) config('shop.tax_enabled', false) && self::rate() > 0;
    }

    public static function rate(): float
    {
        if (! config('shop.tax_enabled', false)) {
            return 0.0;
        }

        return max(0, (float) config('shop.tax_rate', 0));
    }

    public static function baseLabel(): string
    {
        $label = trim((string) config('shop.tax_label', 'Tax'));

        return $label !== '' ? $label : 'Tax';
    }

    /**
     * Label for receipts / invoices, e.g. "VAT (15%)".
     */
    public static function label(): string
    {
        $label = self::baseLabel();
        $rate = self::rate();

        if ($rate <= 0) {
            return $label;
        }

        $percent = rtrim(rtrim(number_format($rate * 100, 2, '.', ''), '0'), '.');

        return $label.' ('.$percent.'%)';
    }

    /**
     * Label when showing a stored order tax amount (rate may have changed since).
     */
    public static function orderLabel(): string
    {
        return self::baseLabel();
    }
}
