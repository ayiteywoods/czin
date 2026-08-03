<?php

namespace App\Enums;

enum TableStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Reserved = 'reserved';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Occupied => 'Occupied',
            self::Reserved => 'Reserved',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::Occupied => 'warning',
            self::Reserved => 'info',
        };
    }
}
