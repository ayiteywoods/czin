<?php

namespace App\Enums;

enum PromotionType: string
{
    case Percent = 'percent';
    case Fixed = 'fixed';
    case Bogo = 'bogo';

    public function label(): string
    {
        return match ($this) {
            self::Percent => 'Percentage',
            self::Fixed => 'Fixed amount',
            self::Bogo => 'Buy one get one',
        };
    }
}
