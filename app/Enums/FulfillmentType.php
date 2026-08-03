<?php

namespace App\Enums;

enum FulfillmentType: string
{
    case DineIn = 'dine_in';
    case Takeaway = 'takeaway';
    case Delivery = 'delivery';

    public function label(): string
    {
        return match ($this) {
            self::DineIn => 'Dine in',
            self::Takeaway => 'Takeaway',
            self::Delivery => 'Delivery',
        };
    }
}
