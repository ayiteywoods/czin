@extends('layouts.admin')

@section('heading', 'Store settings')
@section('subheading', 'Contact details, footer copy, product delivery info, about page, and contact page')

@section('content')
    <form
        method="POST"
        action="{{ route('admin.store-settings.update') }}"
        enctype="multipart/form-data"
        class="grid max-w-5xl gap-6 lg:grid-cols-2"
    >
        @csrf
        @method('PUT')

        <div class="card space-y-4 p-6">
            <div>
                <h2 class="font-semibold">Store contact</h2>
                <p class="mt-1 text-sm text-brand-muted">Used on invoices, order emails, the about page, and footer. Separate from the public contact page support email.</p>
            </div>

            <div>
                <x-form-label for="store_name" :required="true">Store name</x-form-label>
                <input id="store_name" type="text" name="store_name" value="{{ old('store_name', $settings->store_name) }}" required class="input-field">
                @error('store_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-form-label for="contact_email" :required="true">Store email</x-form-label>
                <input id="contact_email" type="email" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}" required class="input-field" placeholder="hello@czin.com">
                <p class="mt-1 text-xs text-brand-muted">Transactional email shown on invoices and order notifications.</p>
                @error('contact_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-form-label for="contact_phone" :required="true">Phone</x-form-label>
                    <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}" required class="input-field">
                    @error('contact_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="contact_phone_alt" class="block text-sm font-medium">Phone (alternate)</label>
                    <input id="contact_phone_alt" type="text" name="contact_phone_alt" value="{{ old('contact_phone_alt', $settings->contact_phone_alt) }}" class="input-field" placeholder="{{ config('shop.contact_phone_alt') }}">
                    <p class="mt-1 text-xs text-brand-muted">Leave blank to use the default shown in invoices when no alternate number is saved.</p>
                    @error('contact_phone_alt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <x-form-label for="contact_address" :required="true">Store address</x-form-label>
                <textarea id="contact_address" name="contact_address" rows="3" required class="input-field">{{ old('contact_address', $settings->contact_address) }}</textarea>
                @error('contact_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="contact_website" class="block text-sm font-medium">Website URL</label>
                <input id="contact_website" type="url" name="contact_website" value="{{ old('contact_website', $settings->contact_website) }}" class="input-field" placeholder="{{ config('app.url') }}">
                @error('contact_website')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="border-t border-neutral-200 pt-4">
                <h3 class="font-semibold">Footer</h3>
                <p class="mt-1 text-sm text-brand-muted">Short description shown below the logo in the website footer.</p>

                <div class="mt-4 space-y-4">
                    <div>
                        <x-form-label for="footer_tagline" :required="true">Tagline</x-form-label>
                        <input
                            id="footer_tagline"
                            type="text"
                            name="footer_tagline"
                            value="{{ old('footer_tagline', $settings->footer_tagline ?: $settings->footerTagline()) }}"
                            required
                            class="input-field"
                            placeholder="Fresh meals made to order."
                        >
                        @error('footer_tagline')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-form-label for="footer_subline" :required="true">Subline</x-form-label>
                        <input
                            id="footer_subline"
                            type="text"
                            name="footer_subline"
                            value="{{ old('footer_subline', $settings->footer_subline ?: $settings->footerSubline()) }}"
                            required
                            class="input-field"
                            placeholder="Hot food delivered across Accra."
                        >
                        @error('footer_subline')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-neutral-200 pt-4">
                <h3 class="font-semibold">Social media</h3>
                <p class="mt-1 text-sm text-brand-muted">Links appear as icons in the website footer. Leave blank to hide a platform.</p>

                <div class="mt-4 space-y-4">
                    <div>
                        <label for="social_instagram" class="block text-sm font-medium">Instagram</label>
                        <input id="social_instagram" type="text" name="social_instagram" value="{{ old('social_instagram', $settings->social_instagram) }}" class="input-field" placeholder="https://instagram.com/yourpage">
                        @error('social_instagram')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="social_facebook" class="block text-sm font-medium">Facebook</label>
                        <input id="social_facebook" type="text" name="social_facebook" value="{{ old('social_facebook', $settings->social_facebook) }}" class="input-field" placeholder="https://facebook.com/yourpage">
                        @error('social_facebook')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="social_tiktok" class="block text-sm font-medium">TikTok</label>
                        <input id="social_tiktok" type="text" name="social_tiktok" value="{{ old('social_tiktok', $settings->social_tiktok) }}" class="input-field" placeholder="https://tiktok.com/@yourpage">
                        @error('social_tiktok')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="social_x" class="block text-sm font-medium">X (Twitter)</label>
                        <input id="social_x" type="text" name="social_x" value="{{ old('social_x', $settings->social_x) }}" class="input-field" placeholder="https://x.com/yourpage">
                        @error('social_x')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="social_youtube" class="block text-sm font-medium">YouTube</label>
                        <input id="social_youtube" type="text" name="social_youtube" value="{{ old('social_youtube', $settings->social_youtube) }}" class="input-field" placeholder="https://youtube.com/@yourchannel">
                        @error('social_youtube')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="social_whatsapp" class="block text-sm font-medium">WhatsApp</label>
                        <input id="social_whatsapp" type="text" name="social_whatsapp" value="{{ old('social_whatsapp', $settings->social_whatsapp) }}" class="input-field" placeholder="233530668945">
                        <p class="mt-1 text-xs text-brand-muted">Enter phone number with country code (no +). Example: 233530668945</p>
                        @error('social_whatsapp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="card space-y-4 p-6">
                <div>
                    <h2 class="font-semibold">Kitchen alerts</h2>
                    <p class="mt-1 text-sm text-brand-muted">Notify kitchen staff by SMS or WhatsApp when a paid order arrives. Requires Twilio credentials in your <code>.env</code> file.</p>
                </div>

                <div class="space-y-4">
                    <label class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            name="kitchen_sms_enabled"
                            value="1"
                            class="mt-1 rounded border-neutral-300"
                            @checked(old('kitchen_sms_enabled', $settings->kitchen_sms_enabled))
                        >
                        <span>
                            <span class="block text-sm font-medium">Send SMS alerts</span>
                            <span class="block text-xs text-brand-muted">Texts the kitchen phone when an order is paid (online or POS).</span>
                        </span>
                    </label>

                    <div>
                        <label for="kitchen_sms_phone" class="block text-sm font-medium">Kitchen SMS phone</label>
                        <input id="kitchen_sms_phone" type="text" name="kitchen_sms_phone" value="{{ old('kitchen_sms_phone', $settings->kitchen_sms_phone) }}" class="input-field" placeholder="{{ $settings->contact_phone }}">
                        <p class="mt-1 text-xs text-brand-muted">Leave blank to use the store phone.</p>
                        @error('kitchen_sms_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <label class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            name="kitchen_whatsapp_enabled"
                            value="1"
                            class="mt-1 rounded border-neutral-300"
                            @checked(old('kitchen_whatsapp_enabled', $settings->kitchen_whatsapp_enabled))
                        >
                        <span>
                            <span class="block text-sm font-medium">Send WhatsApp alerts</span>
                            <span class="block text-xs text-brand-muted">WhatsApp message to the kitchen line via Twilio.</span>
                        </span>
                    </label>

                    <div>
                        <label for="kitchen_whatsapp_phone" class="block text-sm font-medium">Kitchen WhatsApp phone</label>
                        <input id="kitchen_whatsapp_phone" type="text" name="kitchen_whatsapp_phone" value="{{ old('kitchen_whatsapp_phone', $settings->kitchen_whatsapp_phone) }}" class="input-field" placeholder="{{ $settings->social_whatsapp ?: $settings->contact_phone }}">
                        <p class="mt-1 text-xs text-brand-muted">Leave blank to use the store WhatsApp or phone number.</p>
                        @error('kitchen_whatsapp_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="card space-y-4 p-6">
                <div>
                    <h2 class="font-semibold">Restaurant operations</h2>
                    <p class="mt-1 text-sm text-brand-muted">Stock alerts, cart upsells, ordering availability, and a simple hours note.</p>
                </div>

                <div>
                    <label for="low_stock_threshold" class="block text-sm font-medium">Low stock threshold</label>
                    <input
                        id="low_stock_threshold"
                        type="number"
                        name="low_stock_threshold"
                        min="0"
                        max="10000"
                        value="{{ old('low_stock_threshold', $settings->low_stock_threshold ?? 10) }}"
                        class="input-field"
                    >
                    <p class="mt-1 text-xs text-brand-muted">Products below this quantity appear as low stock on the dashboard.</p>
                    @error('low_stock_threshold')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="upsell_category_slugs" class="block text-sm font-medium">Upsell category slugs</label>
                    @php
                        $upsellSlugsValue = old(
                            'upsell_category_slugs',
                            is_array($settings->upsell_category_slugs)
                                ? implode(', ', $settings->upsell_category_slugs)
                                : implode(', ', $settings->upsellCategorySlugs())
                        );
                        if (is_array($upsellSlugsValue)) {
                            $upsellSlugsValue = implode(', ', $upsellSlugsValue);
                        }
                    @endphp
                    <input
                        id="upsell_category_slugs"
                        type="text"
                        name="upsell_category_slugs"
                        value="{{ $upsellSlugsValue }}"
                        class="input-field"
                        placeholder="sides, drinks, desserts"
                    >
                    <p class="mt-1 text-xs text-brand-muted">Comma-separated category slugs suggested after add-to-cart.</p>
                    @error('upsell_category_slugs')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('upsell_category_slugs.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="business_hours_note" class="block text-sm font-medium">Business hours note</label>
                    <input
                        id="business_hours_note"
                        type="text"
                        name="business_hours_note"
                        value="{{ old('business_hours_note', data_get($settings->business_hours, 'note')) }}"
                        class="input-field"
                        placeholder="Open daily 10:00 AM – 10:00 PM"
                    >
                    <p class="mt-1 text-xs text-brand-muted">Optional note for internal reference. Public hours still use the Contact page fields.</p>
                    @error('business_hours_note')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('business_hours.note')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-start gap-3">
                    <input type="hidden" name="online_ordering_enabled" value="0">
                    <input
                        type="checkbox"
                        name="online_ordering_enabled"
                        value="1"
                        class="mt-1 rounded border-neutral-300"
                        @checked(old('online_ordering_enabled', $settings->online_ordering_enabled ?? true))
                    >
                    <span>
                        <span class="block text-sm font-medium">Enable online ordering</span>
                        <span class="block text-xs text-brand-muted">When unchecked, customers cannot place new online orders.</span>
                    </span>
                </label>
                @error('online_ordering_enabled')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="space-y-6">
            <div class="card space-y-4 p-6">
                <div>
                    <h2 class="font-semibold">About page</h2>
                    <p class="mt-1 text-sm text-brand-muted">Hero description and banner image on the about page.</p>
                </div>

                <div>
                    <label for="about_hero_description" class="block text-sm font-medium">Hero description</label>
                    <textarea id="about_hero_description" name="about_hero_description" rows="4" class="input-field">{{ old('about_hero_description', $settings->about_hero_description) }}</textarea>
                    @error('about_hero_description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="about_image" class="block text-sm font-medium">Banner image</label>
                    <input
                        id="about_image"
                        type="file"
                        name="about_image"
                        accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,image/avif,image/heic,image/heif,.jpg,.jpeg,.png,.gif,.webp,.bmp,.avif,.heic,.heif"
                        class="mt-1 block w-full text-sm file:mr-4 file:rounded-md file:border-0 file:bg-brand-red file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-red-dark"
                    >
                    <p class="mt-1 text-xs text-brand-muted">Recommended wide image (about 21:9). Large uploads are compressed to {{ \App\Support\ImageUpload::targetLabel(5120) }}.</p>

                    @php
                        $aboutBannerUrl = $settings->aboutImageUrl();
                        $hasCustomAboutBanner = filled($settings->about_image_path);
                    @endphp

                    <div class="mt-3 overflow-hidden border border-neutral-200 bg-neutral-50">
                        <img
                            src="{{ $aboutBannerUrl }}"
                            alt="About page banner"
                            class="aspect-[21/9] w-full object-cover"
                            onerror="this.classList.add('hidden'); const fallback = this.nextElementSibling; if (fallback) { fallback.classList.remove('hidden'); fallback.classList.add('flex'); }"
                        >
                        <div class="hidden aspect-[21/9] w-full items-center justify-center px-4 text-center text-sm text-brand-muted">
                            Banner preview unavailable. Choose a new image and save settings.
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-brand-muted">
                        {{ $hasCustomAboutBanner ? 'Custom banner is currently set.' : 'Showing the default banner until you upload a custom image.' }}
                    </p>
                    @error('about_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                @if ($aboutPage)
                    <a href="{{ route('admin.pages.edit', $aboutPage) }}" class="btn-outline inline-flex w-full justify-center">
                        Edit about page story
                    </a>
                    <a href="{{ route('about') }}" target="_blank" rel="noopener noreferrer" class="btn-outline inline-flex w-full justify-center">
                        Preview about page
                    </a>
                @endif
            </div>

            <div class="card space-y-4 p-6">
                <div>
                    <h2 class="font-semibold">Contact page</h2>
                    <p class="mt-1 text-sm text-brand-muted">Details shown on the public contact page. Use a dedicated support email if it differs from your store email.</p>
                </div>

                <div>
                    <x-form-label for="contact_page_email" :required="true">Support email</x-form-label>
                    <input
                        id="contact_page_email"
                        type="email"
                        name="contact_page_email"
                        value="{{ old('contact_page_email', $settings->contact_page_email ?: $settings->contactPageEmail()) }}"
                        required
                        class="input-field"
                        placeholder="support@czin.com"
                    >
                    @error('contact_page_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="contact_page_phone" class="block text-sm font-medium">Phone</label>
                        <input
                            id="contact_page_phone"
                            type="text"
                            name="contact_page_phone"
                            value="{{ old('contact_page_phone', $settings->contact_page_phone) }}"
                            class="input-field"
                            placeholder="{{ $settings->contact_phone ?: config('shop.contact_phone') }}"
                        >
                        <p class="mt-1 text-xs text-brand-muted">Leave blank to use the store phone above.</p>
                        @error('contact_page_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact_page_phone_alt" class="block text-sm font-medium">Phone (alternate)</label>
                        <input
                            id="contact_page_phone_alt"
                            type="text"
                            name="contact_page_phone_alt"
                            value="{{ old('contact_page_phone_alt', $settings->contact_page_phone_alt) }}"
                            class="input-field"
                            placeholder="{{ $settings->contact_phone_alt ?: config('shop.contact_phone_alt') }}"
                        >
                        <p class="mt-1 text-xs text-brand-muted">Leave blank to use the store alternate phone.</p>
                        @error('contact_page_phone_alt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="contact_page_address" class="block text-sm font-medium">Store address</label>
                    <textarea
                        id="contact_page_address"
                        name="contact_page_address"
                        rows="3"
                        class="input-field"
                        placeholder="{{ $settings->contact_address ?: config('shop.contact_address') }}"
                    >{{ old('contact_page_address', $settings->contact_page_address) }}</textarea>
                    <p class="mt-1 text-xs text-brand-muted">Leave blank to use the store address above.</p>
                    @error('contact_page_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="contact_page_hours_days" class="block text-sm font-medium">Business hours (days)</label>
                        <input
                            id="contact_page_hours_days"
                            type="text"
                            name="contact_page_hours_days"
                            value="{{ old('contact_page_hours_days', $settings->contact_page_hours_days ?: $settings->contactPageHoursDays()) }}"
                            class="input-field"
                            placeholder="Monday – Saturday"
                        >
                        @error('contact_page_hours_days')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact_page_hours_time" class="block text-sm font-medium">Business hours (time)</label>
                        <input
                            id="contact_page_hours_time"
                            type="text"
                            name="contact_page_hours_time"
                            value="{{ old('contact_page_hours_time', $settings->contact_page_hours_time ?: $settings->contactPageHoursTime()) }}"
                            class="input-field"
                            placeholder="9:00 AM – 6:00 PM"
                        >
                        @error('contact_page_hours_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="contact_page_hours_note" class="block text-sm font-medium">Business hours note</label>
                    <input
                        id="contact_page_hours_note"
                        type="text"
                        name="contact_page_hours_note"
                        value="{{ old('contact_page_hours_note', $settings->contact_page_hours_note ?: $settings->contactPageHoursNote()) }}"
                        class="input-field"
                        placeholder="Closed on Sundays and public holidays."
                    >
                    @error('contact_page_hours_note')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                @if ($contactPage)
                    <a href="{{ route('admin.pages.edit', $contactPage) }}" class="btn-outline inline-flex w-full justify-center">
                        Edit contact page intro text
                    </a>
                    <a href="{{ route('pages.show', $contactPage) }}" target="_blank" rel="noopener noreferrer" class="btn-outline inline-flex w-full justify-center">
                        Preview contact page
                    </a>
                @endif
            </div>

            <div class="card space-y-4 p-6">
                <div>
                    <h2 class="font-semibold">Product delivery info</h2>
                    <p class="mt-1 text-sm text-brand-muted">Shown on product pages below the add-to-cart section.</p>
                </div>

                <div>
                    <x-form-label for="delivery_shipping_note" :required="true">Shipping note</x-form-label>
                    <input
                        id="delivery_shipping_note"
                        type="text"
                        name="delivery_shipping_note"
                        value="{{ old('delivery_shipping_note', $settings->delivery_shipping_note) }}"
                        required
                        class="input-field"
                        placeholder="Shipping calculated at checkout."
                    >
                    <p class="mt-1 text-xs text-brand-muted">If the note starts with “Shipping”, that word is underlined on the product page.</p>
                    @error('delivery_shipping_note')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <x-form-label for="delivery_info_accra" :required="true">Delivery message</x-form-label>
                    <textarea id="delivery_info_accra" name="delivery_info_accra" rows="3" required class="input-field">{{ old('delivery_info_accra', $settings->delivery_info_accra) }}</textarea>
                    @error('delivery_info_accra')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="btn-primary">Save settings</button>
                <a href="{{ route('admin.dashboard') }}" class="btn-outline px-4 py-2.5">Back to dashboard</a>
            </div>
        </div>
    </form>
@endsection
