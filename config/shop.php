<?php

return [

    /*
  |--------------------------------------------------------------------------
  | Store Currency
  |--------------------------------------------------------------------------
  |
  | Default currency for product prices across the storefront.
  |
  */

    'currency' => env('SHOP_CURRENCY', 'GHS'),

    'currency_symbol' => env('SHOP_CURRENCY_SYMBOL', '₵'),

    'delivery_fee' => (float) env('SHOP_DELIVERY_FEE', 30),

    'free_delivery_threshold' => (float) env('SHOP_FREE_DELIVERY_THRESHOLD', 500),

    'tax_enabled' => filter_var(env('SHOP_TAX_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'tax_rate' => (float) env('SHOP_TAX_RATE', 0),

    'tax_label' => env('SHOP_TAX_LABEL', 'Tax'),

    'default_country' => env('SHOP_DEFAULT_COUNTRY', 'Ghana'),

    'cart_reservation_minutes' => (int) env('SHOP_CART_RESERVATION_MINUTES', 60), // legacy, no longer used

    'order_payment_timeout_hours' => (int) env('SHOP_ORDER_PAYMENT_TIMEOUT_HOURS', 24),

    'store_name' => env('SHOP_STORE_NAME', "CZIN"),

    'logo' => 'images/brand/clogo.png',
    'logo_text' => 'images/brand/ctext.png',
    'logo_text_on_light' => 'images/brand/ctext-on-light.png',
    'footer_logo' => null,

    'contact_email' => env('SHOP_CONTACT_EMAIL', 'hello@czin.com'),

    'contact_page_email' => env('SHOP_CONTACT_PAGE_EMAIL', 'support@czin.com'),

    'contact_phone' => env('SHOP_CONTACT_PHONE', '+233 530 668 945'),

    'contact_phone_alt' => env('SHOP_CONTACT_PHONE_ALT', '233 530 668 945'),

    'contact_address' => env('SHOP_CONTACT_ADDRESS', 'Dansoman, Dansoman, Greater Accra, Ghana.'),

    'contact_website' => env('SHOP_CONTACT_WEBSITE', env('APP_URL', 'http://localhost')),

    'order_number_padding' => (int) env('SHOP_ORDER_NUMBER_PADDING', 4),

    'order_number_start' => (int) env('SHOP_ORDER_NUMBER_START', 1000),

    'payment_method_label' => env('SHOP_PAYMENT_METHOD_LABEL', 'Mobile Money Or Debit/Credit Cards'),

    'invoice_accra_shipping_note' => 'DELIVERY WITHIN ACCRA, PAY RIDER ON DELIVERY.',

    'maintenance_mode' => false,

    'maintenance_message' => 'We are currently performing scheduled maintenance. Please check back soon.',

    'online_ordering_enabled' => true,

    // Mapped to ProductVariant.size (portion / pack size on the menu).
    'product_sizes' => ['Regular', 'Large', 'Family'],

    // Mapped to ProductVariant.color (prep / spice / style option).
    'product_colors' => ['Standard', 'Mild', 'Spicy', 'Extra Spicy'],

    /*
    | Maps option names (lowercase keys) to CSS color values for the storefront picker.
    | Food options like Mild/Spicy fall back to neutral accents when not listed.
    */
    'product_color_map' => [
        'standard' => '#737373',
        'mild' => '#22c55e',
        'spicy' => '#e10600',
        'extra spicy' => '#b80500',
        'black' => '#1a1a0a',
        'white' => '#ffffff',
        'red' => '#c41e3a',
        'gold' => '#d4af37',
        'cream' => '#fffdd0',
    ],

    // Optional third option (ProductVariant.heel_length) — extras for menu items.
    'product_heel_lengths' => [],
    'product_extras' => ['Extra meat', 'Extra sauce', 'No onion', 'No pepper', 'Side salad'],

    'delivery_info' => [
        'shipping_note' => 'Delivery fee calculated at checkout.',
        'items' => [
            [
                'icon' => 'truck',
                'text' => 'Hot meals delivered across Accra, typically within 45–90 minutes. Delivery fee is paid to the rider on arrival where applicable.',
            ],
        ],
    ],
];
