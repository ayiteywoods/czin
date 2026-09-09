<?php

namespace App\Support;

use App\Models\StoreSetting;

class MailBranding
{
    public static function logoUrl(): string
    {
        return StoreSetting::current()->logoUrl();
    }

    public static function storeName(): string
    {
        return (string) config('shop.store_name', config('app.name', 'CZIN'));
    }
}
