<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Services\Messaging\TwilioMessenger;
use Illuminate\Support\Facades\Log;

class KitchenAlertService
{
    public function __construct(
        protected TwilioMessenger $twilio,
    ) {}

    public function notify(Order $order): void
    {
        $order->refresh();

        if ($order->kitchen_alert_sent_at !== null) {
            return;
        }

        if ($order->payment_status !== PaymentStatus::Paid) {
            return;
        }

        $order->loadMissing(['items', 'diningTable']);
        $settings = StoreSetting::current();
        $message = $this->buildMessage($order);

        $smsSent = false;
        $whatsappSent = false;

        if ($settings->kitchen_sms_enabled && filled($settings->kitchenSmsPhone())) {
            $smsSent = $this->twilio->sendSms($settings->kitchenSmsPhone(), $message);
        }

        if ($settings->kitchen_whatsapp_enabled && filled($settings->kitchenWhatsappPhone())) {
            $whatsappSent = $this->twilio->sendWhatsApp($settings->kitchenWhatsappPhone(), $message);
        }

        if (! $settings->kitchen_sms_enabled && ! $settings->kitchen_whatsapp_enabled) {
            Log::info('Kitchen alert skipped: SMS and WhatsApp alerts are disabled in store settings.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

            return;
        }

        if ($smsSent || $whatsappSent) {
            $order->update(['kitchen_alert_sent_at' => now()]);

            return;
        }

        if (! $this->twilio->isConfigured()) {
            Log::info('Kitchen alert logged (Twilio not configured).', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'message' => $message,
            ]);
        }
    }

    protected function buildMessage(Order $order): string
    {
        $lines = [
            'NEW KITCHEN ORDER #'.$order->order_number,
            $order->kitchenFulfillmentLabel().' · '.$order->kitchenSourceLabel(),
        ];

        if ($order->diningTable) {
            $lines[] = 'Table '.$order->diningTable->code.' · '.$order->diningTable->name;
        }

        if ($order->billing_full_name && $order->billing_full_name !== 'Walk-in Customer') {
            $lines[] = 'Customer: '.$order->billing_full_name;
        }

        foreach ($order->items as $item) {
            $line = $item->quantity.'x '.$item->product_name;

            if (is_array($item->variant_options)) {
                $options = collect($item->variant_options)
                    ->filter()
                    ->values()
                    ->implode(', ');

                if ($options !== '') {
                    $line .= ' ('.$options.')';
                }
            }

            $lines[] = $line;
        }

        if (filled($order->customer_comment)) {
            $lines[] = 'Note: '.$order->customer_comment;
        }

        $lines[] = 'View: '.route('admin.kitchen.index');

        return implode("\n", $lines);
    }
}
