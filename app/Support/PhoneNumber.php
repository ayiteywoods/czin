<?php

namespace App\Support;

class PhoneNumber
{
    public static function normalize(?string $phone, string $defaultCountryCode = '233'): ?string
    {
        if (! filled($phone)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = $defaultCountryCode.substr($digits, 1);
        }

        if (! str_starts_with($digits, $defaultCountryCode) && strlen($digits) <= 10) {
            $digits = $defaultCountryCode.$digits;
        }

        return '+'.$digits;
    }
}
