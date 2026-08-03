<?php

namespace App\Support;

class MailBranding
{
    public static function logoUrl(): string
    {
        return asset(config('shop.logo'));
    }

    public static function storeName(): string
    {
        return (string) config('shop.store_name', config('app.name', 'CZIN'));
    }
}
