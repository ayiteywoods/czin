<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name',
        'contact_email',
        'contact_phone',
        'contact_phone_alt',
        'contact_address',
        'contact_website',
        'contact_page_email',
        'contact_page_phone',
        'contact_page_phone_alt',
        'contact_page_address',
        'contact_page_hours_days',
        'contact_page_hours_time',
        'contact_page_hours_note',
        'about_image_path',
        'logo_path',
        'logo_text_path',
        'logo_text_on_light_path',
        'footer_logo_path',
        'about_hero_description',
        'footer_tagline',
        'footer_subline',
        'delivery_shipping_note',
        'delivery_info_accra',
        'social_facebook',
        'social_instagram',
        'social_tiktok',
        'social_x',
        'social_youtube',
        'social_whatsapp',
        'kitchen_sms_enabled',
        'kitchen_sms_phone',
        'kitchen_whatsapp_enabled',
        'kitchen_whatsapp_phone',
        'low_stock_threshold',
        'upsell_category_slugs',
        'business_hours',
        'online_ordering_enabled',
        'tax_enabled',
        'tax_rate',
        'tax_label',
        'maintenance_mode',
        'maintenance_message',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_mode' => 'boolean',
            'kitchen_sms_enabled' => 'boolean',
            'kitchen_whatsapp_enabled' => 'boolean',
            'low_stock_threshold' => 'integer',
            'upsell_category_slugs' => 'array',
            'business_hours' => 'array',
            'online_ordering_enabled' => 'boolean',
            'tax_enabled' => 'boolean',
            'tax_rate' => 'float',
        ];
    }

    public function taxEnabled(): bool
    {
        return (bool) ($this->tax_enabled ?? false);
    }

    public function taxRate(): float
    {
        $rate = max(0, (float) ($this->tax_rate ?? 0));

        // Accept either 0.15 or 15 as "15%".
        if ($rate > 1) {
            $rate = $rate / 100;
        }

        return min(1, $rate);
    }

    public function taxLabel(): string
    {
        $label = trim((string) ($this->tax_label ?? ''));

        return $label !== '' ? $label : 'Tax';
    }

    /**
     * Percentage form value (e.g. 15 for 15%).
     */
    public function taxRatePercent(): float
    {
        return round($this->taxRate() * 100, 4);
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function aboutImageUrl(): string
    {
        return $this->resolveBrandImageUrl($this->about_image_path, 'images/brand/hero2.jpeg');
    }

    public function logoUrl(): string
    {
        return $this->resolveBrandImageUrl($this->logo_path, 'images/brand/clogo.png');
    }

    public function logoTextUrl(bool $onLight = false): string
    {
        if ($onLight) {
            return $this->resolveBrandImageUrl(
                $this->logo_text_on_light_path,
                'images/brand/ctext-on-light.png',
            );
        }

        return $this->resolveBrandImageUrl($this->logo_text_path, 'images/brand/ctext.png');
    }

    public function footerLogoUrl(): ?string
    {
        if (! filled($this->footer_logo_path)) {
            return null;
        }

        return $this->resolveBrandImageUrl($this->footer_logo_path, '');
    }

    public function hasCustomFooterLogo(): bool
    {
        return filled($this->footer_logo_path);
    }

    /**
     * Absolute filesystem path for invoice embedding, or null when unavailable.
     */
    public function logoAbsolutePath(): ?string
    {
        return $this->resolveBrandImageAbsolutePath($this->logo_path, 'images/brand/clogo.png');
    }

    protected function resolveBrandImageUrl(?string $path, string $default): string
    {
        if (! filled($path)) {
            return $default !== '' ? asset($default) : '';
        }

        if (str_starts_with($path, 'images/') || str_starts_with($path, 'http')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    protected function resolveBrandImageAbsolutePath(?string $path, string $default): ?string
    {
        if (! filled($path)) {
            $fallback = public_path($default);

            return is_file($fallback) ? $fallback : null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }

        if (str_starts_with($path, 'images/')) {
            $absolute = public_path($path);

            return is_file($absolute) ? $absolute : null;
        }

        $absolute = Storage::disk('public')->path($path);

        return is_file($absolute) ? $absolute : null;
    }

    public function contactPhoneAlt(): ?string
    {
        return filled($this->contact_phone_alt) ? $this->contact_phone_alt : null;
    }

    /**
     * @return list<string>
     */
    public function invoiceContactPhones(): array
    {
        $primary = filled($this->contact_phone)
            ? $this->contact_phone
            : (string) config('shop.contact_phone');

        $phones = [];

        if (filled($primary)) {
            $phones[] = trim($primary);
        }

        $alt = $this->contactPhoneAlt();

        if ($alt && self::normalizePhoneDigits($alt) !== self::normalizePhoneDigits($primary)) {
            $phones[] = trim($alt);
        }

        return $phones;
    }

    public function contactPageEmail(): string
    {
        return filled($this->contact_page_email)
            ? $this->contact_page_email
            : (string) config('shop.contact_page_email', 'support@czin.com');
    }

    public function contactPageAddress(): string
    {
        if (filled($this->contact_page_address)) {
            return $this->contact_page_address;
        }

        return filled($this->contact_address)
            ? $this->contact_address
            : (string) config('shop.contact_address');
    }

    public function contactPagePhoneAlt(): ?string
    {
        if (filled($this->contact_page_phone_alt)) {
            return $this->contact_page_phone_alt;
        }

        return $this->contactPhoneAlt();
    }

    /**
     * @return list<string>
     */
    public function contactPagePhones(): array
    {
        $primary = filled($this->contact_page_phone)
            ? $this->contact_page_phone
            : (filled($this->contact_phone)
                ? $this->contact_phone
                : (string) config('shop.contact_phone'));

        $phones = [];

        if (filled($primary)) {
            $phones[] = trim($primary);
        }

        $alt = $this->contactPagePhoneAlt();

        if ($alt && self::normalizePhoneDigits($alt) !== self::normalizePhoneDigits($primary)) {
            $phones[] = trim($alt);
        }

        return $phones;
    }

    public function contactPageHoursDays(): string
    {
        return $this->contact_page_hours_days ?: 'Monday – Saturday';
    }

    public function contactPageHoursTime(): string
    {
        return $this->contact_page_hours_time ?: '9:00 AM – 6:00 PM';
    }

    public function contactPageHoursNote(): string
    {
        return $this->contact_page_hours_note ?: 'Closed on Sundays and public holidays.';
    }

    public static function normalizePhoneDigits(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }

    public function phoneTelUri(string $phone): string
    {
        $digits = self::normalizePhoneDigits($phone);

        if ($digits === '') {
            return '#';
        }

        if (str_starts_with($digits, '233')) {
            return 'tel:+'.$digits;
        }

        if (str_starts_with($digits, '0')) {
            return 'tel:+233'.substr($digits, 1);
        }

        return 'tel:+'.$digits;
    }

    public function aboutHeroDescription(): string
    {
        return $this->about_hero_description
            ?: config('shop.store_name').' is a Ghanaian restaurant kitchen serving fresh meals, sides, and drinks — made to order for pickup and delivery across Accra.';
    }

    public function footerTagline(): string
    {
        return $this->footer_tagline ?: 'Fresh meals made to order.';
    }

    public function footerSubline(): string
    {
        return $this->footer_subline ?: 'Hot food delivered across Accra.';
    }

    public function isMaintenanceModeEnabled(): bool
    {
        return (bool) $this->maintenance_mode;
    }

    public function maintenanceMessage(): string
    {
        return $this->maintenance_message
            ?: 'We are currently performing scheduled maintenance. Please check back soon.';
    }

    public function deliveryShippingNote(): string
    {
        return $this->delivery_shipping_note
            ?: config('shop.delivery_info.shipping_note', 'Shipping calculated at checkout.');
    }

    /**
     * @return list<array{icon: string, text: string}>
     */
    public function deliveryInfoItems(): array
    {
        $defaults = config('shop.delivery_info.items', []);

        $items = [];

        $accra = $this->delivery_info_accra ?: ($defaults[0]['text'] ?? '');
        if (filled($accra)) {
            $items[] = [
                'icon' => $defaults[0]['icon'] ?? 'truck',
                'text' => $accra,
            ];
        }

        return $items;
    }

    /**
     * @return list<array{platform: string, label: string, url: string}>
     */
    public function socialLinks(): array
    {
        $platforms = [
            'facebook' => ['column' => 'social_facebook', 'label' => 'Facebook'],
            'instagram' => ['column' => 'social_instagram', 'label' => 'Instagram'],
            'tiktok' => ['column' => 'social_tiktok', 'label' => 'TikTok'],
            'x' => ['column' => 'social_x', 'label' => 'X'],
            'youtube' => ['column' => 'social_youtube', 'label' => 'YouTube'],
            'whatsapp' => ['column' => 'social_whatsapp', 'label' => 'WhatsApp'],
        ];

        $links = [];

        foreach ($platforms as $platform => $meta) {
            $value = trim((string) $this->{$meta['column']});

            if ($value === '') {
                continue;
            }

            $links[] = [
                'platform' => $platform,
                'label' => $meta['label'],
                'url' => $this->normalizeSocialUrl($platform, $value),
            ];
        }

        return $links;
    }

    protected function normalizeSocialUrl(string $platform, string $value): string
    {
        if ($platform === 'whatsapp') {
            $digits = preg_replace('/\D+/', '', $value) ?? '';

            if ($digits !== '') {
                return 'https://wa.me/'.$digits;
            }
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return 'https://'.ltrim($value, '/');
    }

    public function kitchenSmsPhone(): ?string
    {
        return filled($this->kitchen_sms_phone)
            ? $this->kitchen_sms_phone
            : $this->contact_phone;
    }

    public function kitchenWhatsappPhone(): ?string
    {
        if (filled($this->kitchen_whatsapp_phone)) {
            return $this->kitchen_whatsapp_phone;
        }

        return filled($this->social_whatsapp)
            ? $this->social_whatsapp
            : $this->contact_phone;
    }

    /**
     * @return list<string>
     */
    public function upsellCategorySlugs(): array
    {
        $slugs = $this->upsell_category_slugs;

        if (! is_array($slugs) || $slugs === []) {
            return ['sides', 'drinks', 'desserts'];
        }

        return array_values(array_filter(array_map(
            fn ($slug) => is_string($slug) ? trim($slug) : '',
            $slugs,
        )));
    }

    public function lowStockThreshold(): int
    {
        return max(0, (int) ($this->low_stock_threshold ?? 10));
    }

    public function isOnlineOrderingEnabled(): bool
    {
        return (bool) ($this->online_ordering_enabled ?? true);
    }
}
