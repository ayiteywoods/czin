<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => Page::SLUG_ABOUT,
                'title' => 'About Us',
                'footer_group' => null,
                'sort_order' => 0,
                'body' => <<<'TEXT'
CZIN started with a simple idea: serve honest, flavourful meals that feel like home — ready when you are.

What began as a love for Ghanaian kitchen classics has grown into a full menu of mains, sides, drinks, and desserts, prepared fresh for dine-in, pickup, and delivery.

## Our mission

Great food should arrive hot, taste memorable, and feel worth every bite. That is why we cook with care, keep our menu clear, and focus on a smooth ordering experience from browse to delivery.

## What we stand for

- **Fresh first** — meals prepared to order, not sitting under a lamp
- **Guest care** — responsive support before and after your order
- **Honest portions** — clear sizes and fair pricing
- **Local focus** — built for Accra and surrounding communities

Whether you need a quick weekday lunch or a family dinner delivered, CZIN is here to feed you well.
TEXT,
            ],
            [
                'slug' => Page::SLUG_DELIVERY,
                'title' => 'Delivery Info',
                'footer_group' => Page::FOOTER_CUSTOMER_CARE,
                'sort_order' => 1,
                'body' => <<<'TEXT'
We deliver hot meals across Accra and nearby areas through trusted dispatch riders.

Standard Accra delivery: typically 45–90 minutes depending on kitchen volume and location.
Outer areas: timing may vary — we will confirm at checkout where possible.

You will receive updates once your order is out for delivery. Please ensure your phone number and delivery address are correct at checkout.

For large or timed orders, contact our team before placing your order.
TEXT,
            ],
            [
                'slug' => Page::SLUG_RETURNS,
                'title' => 'Returns Policy',
                'footer_group' => null,
                'sort_order' => 2,
                'body' => <<<'TEXT'
Food orders are prepared fresh and generally cannot be returned once delivered in good condition.

If something is wrong — missing items, incorrect order, or a quality issue — contact us within 2 hours of delivery with your order number and photos where helpful.

Where we confirm an error on our side, we will offer a replacement, credit, or refund as appropriate.

Refunds, when approved, are processed to the original payment method within 5–10 business days.
TEXT,
            ],
            [
                'slug' => Page::SLUG_CONTACT,
                'title' => 'Contact Us',
                'footer_group' => Page::FOOTER_CUSTOMER_CARE,
                'sort_order' => 3,
                'body' => <<<'TEXT'
We are here to help with orders, menu questions, delivery updates, and catering enquiries.

Send us a message and our team will respond as soon as possible — typically within one business day.
TEXT,
            ],
            [
                'slug' => Page::SLUG_PRIVACY,
                'title' => 'Privacy Policy',
                'footer_group' => Page::FOOTER_LEGAL,
                'sort_order' => 1,
                'body' => <<<'TEXT'
At **CZIN**, we respect your privacy and are committed to protecting your personal information. This Privacy Policy explains how we collect, use, and safeguard the information you provide when ordering with us.

## Information We Collect

When you place an order or contact us, we may collect the following information:

- **Full name**
- **Phone number**
- **Delivery address**
- **Email address** (if provided)
- **Payment information** necessary to process your order

## How We Use Your Information

We use your personal information to:

- **Process and confirm** your orders.
- **Arrange and complete** deliveries.
- **Contact you** regarding your order, delivery, or customer support requests.
- **Improve** our menu and services.
- **Send promotional offers or updates**, only if you have agreed to receive them.

## Sharing Your Information

**We value your trust and do not sell, rent, or trade your personal information.**

Your information may only be shared with:

- **Delivery partners** for the purpose of completing your order.
- **Payment service providers** to securely process payments.
- **Authorities** where required by law.

## Data Security

We take reasonable administrative and technical measures to protect your personal information against **unauthorized access, loss, misuse, or disclosure**.

While we strive to keep your information secure, no method of electronic storage or transmission over the internet is completely secure.

## Data Retention

We keep your personal information only for as long as necessary to:

- Process your orders
- Provide customer support
- Comply with legal obligations
- Resolve disputes

## Your Rights

You have the right to:

- **Request access** to the personal information we hold about you.
- **Request correction** of inaccurate or incomplete information.
- **Request deletion** of your personal information where applicable by law.

To make any of these requests, please contact us using the details below.

## Cookies and Online Services

If you visit our website or use our online services, we may use **cookies or similar technologies** to improve your browsing experience and understand how our services are used.

## Changes to This Privacy Policy

**CZIN** may update this Privacy Policy from time to time. Any changes will be posted on our platforms with the updated effective date.

## Contact Us

If you have any questions about this Privacy Policy or how your personal information is handled, please contact us:

**CZIN**

- **Phone:** +233 530 668 945
- **Email:** support@czin.com
- **Social media:** See the links in our website footer for our current Instagram, Facebook, and other profiles.
TEXT,
            ],
            [
                'slug' => Page::SLUG_TERMS,
                'title' => 'Terms & Conditions',
                'footer_group' => Page::FOOTER_LEGAL,
                'sort_order' => 2,
                'body' => <<<'TEXT'
Welcome to **CZIN**. By ordering with us, you agree to the following **Terms & Conditions**. Please read them carefully before placing your order.

## General

**CZIN** is a restaurant offering **prepared meals, sides, drinks, and desserts** for pickup and delivery.

We reserve the right to update **prices, menu availability, and policies** without prior notice.

## Orders & Payment

- Orders are confirmed only after **successful payment** or agreement with our designated payment method.
- We reserve the right to **cancel any order** due to stock unavailability, kitchen capacity, or payment issues.

## Shipping & Delivery

- **Accra deliveries:** typically 45–90 minutes.
- **Nearby areas:** timing may vary with distance and demand.
- Delivery times may vary during **peak hours, public holidays**, or due to unforeseen circumstances.
- Customers are required to provide **accurate delivery details**. CZIN will not be liable for failed deliveries caused by incorrect information.

## Food Quality & Issues

- Please inspect your order on arrival.
- Report missing items, wrong dishes, or quality concerns **promptly** (ideally within 2 hours) with your order number.
- Because meals are prepared fresh, **returns of consumed or correctly delivered food are not accepted**.

## Refunds

- Refunds are **not guaranteed** and will only be considered where we confirm an error on our side or cannot fulfill a suitable replacement.
- **Delivery fees are non-refundable** once a rider has been dispatched, except where we cancel the order.

## Liability

**CZIN** will not be held responsible for issues arising after a correctly delivered order has been accepted, including improper storage or reheating by the customer.
TEXT,
            ],
        ];

        foreach ($pages as $page) {
            Page::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'body' => $page['body'],
                    'footer_group' => $page['footer_group'],
                    'sort_order' => $page['sort_order'],
                    'is_active' => true,
                ],
            );
        }
    }
}
