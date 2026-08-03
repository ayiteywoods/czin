<?php

namespace App\Http\Requests\Admin;

use App\Support\ImageUpload;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('upsell_category_slugs') && is_string($this->input('upsell_category_slugs'))) {
            $slugs = collect(explode(',', (string) $this->input('upsell_category_slugs')))
                ->map(fn (string $slug) => Str::slug(trim($slug)))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $this->merge(['upsell_category_slugs' => $slugs]);
        }

        if ($this->filled('business_hours_note')) {
            $existing = is_array($this->input('business_hours')) ? $this->input('business_hours') : [];
            $this->merge([
                'business_hours' => array_merge($existing, [
                    'note' => trim((string) $this->input('business_hours_note')),
                ]),
            ]);
        } elseif ($this->has('business_hours_note') && ! filled($this->input('business_hours_note'))) {
            $this->merge(['business_hours' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:30'],
            'contact_phone_alt' => ['nullable', 'string', 'max:30'],
            'contact_address' => ['required', 'string', 'max:500'],
            'contact_website' => ['nullable', 'string', 'max:255'],
            'contact_page_email' => ['required', 'email', 'max:255'],
            'contact_page_phone' => ['nullable', 'string', 'max:30'],
            'contact_page_phone_alt' => ['nullable', 'string', 'max:30'],
            'contact_page_address' => ['nullable', 'string', 'max:500'],
            'contact_page_hours_days' => ['nullable', 'string', 'max:100'],
            'contact_page_hours_time' => ['nullable', 'string', 'max:100'],
            'contact_page_hours_note' => ['nullable', 'string', 'max:255'],
            'about_hero_description' => ['nullable', 'string', 'max:1000'],
            'footer_tagline' => ['required', 'string', 'max:255'],
            'footer_subline' => ['required', 'string', 'max:255'],
            'delivery_shipping_note' => ['required', 'string', 'max:255'],
            'delivery_info_accra' => ['required', 'string', 'max:1000'],
            'about_image' => ImageUpload::rules(5120),
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'social_instagram' => ['nullable', 'string', 'max:255'],
            'social_tiktok' => ['nullable', 'string', 'max:255'],
            'social_x' => ['nullable', 'string', 'max:255'],
            'social_youtube' => ['nullable', 'string', 'max:255'],
            'social_whatsapp' => ['nullable', 'string', 'max:30'],
            'kitchen_sms_enabled' => ['sometimes', 'boolean'],
            'kitchen_sms_phone' => ['nullable', 'string', 'max:30'],
            'kitchen_whatsapp_enabled' => ['sometimes', 'boolean'],
            'kitchen_whatsapp_phone' => ['nullable', 'string', 'max:30'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'upsell_category_slugs' => ['nullable', 'array'],
            'upsell_category_slugs.*' => ['string', 'max:100'],
            'business_hours' => ['nullable', 'array'],
            'business_hours.note' => ['nullable', 'string', 'max:500'],
            'business_hours_note' => ['nullable', 'string', 'max:500'],
            'online_ordering_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
