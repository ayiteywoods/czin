<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Adjustment = 'adjustment';
    case Sale = 'sale';
    case Restock = 'restock';
    case Waste = 'waste';

    public function label(): string
    {
        return match ($this) {
            self::Adjustment => 'Adjustment',
            self::Sale => 'Sale',
            self::Restock => 'Restock',
            self::Waste => 'Waste',
        };
    }
}
