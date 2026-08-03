<?php

namespace App\Services\Messaging;

use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioMessenger
{
    public function isConfigured(): bool
    {
        return filled(config('services.twilio.sid'))
            && filled(config('services.twilio.token'));
    }

    public function sendSms(string $to, string $body): bool
    {
        $from = config('services.twilio.sms_from');

        if (! $this->isConfigured() || ! filled($from)) {
            Log::warning('Twilio SMS skipped: credentials or TWILIO_SMS_FROM missing.');

            return false;
        }

        $recipient = PhoneNumber::normalize($to);

        if (! $recipient) {
            return false;
        }

        return $this->dispatch($from, $recipient, $body);
    }

    public function sendWhatsApp(string $to, string $body): bool
    {
        $from = config('services.twilio.whatsapp_from');

        if (! $this->isConfigured() || ! filled($from)) {
            Log::warning('Twilio WhatsApp skipped: credentials or TWILIO_WHATSAPP_FROM missing.');

            return false;
        }

        $recipient = PhoneNumber::normalize($to);

        if (! $recipient) {
            return false;
        }

        $fromAddress = str_starts_with($from, 'whatsapp:') ? $from : 'whatsapp:'.$from;

        return $this->dispatch($fromAddress, 'whatsapp:'.$recipient, $body);
    }

    protected function dispatch(string $from, string $to, string $body): bool
    {
        $sid = config('services.twilio.sid');
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

        $response = Http::withBasicAuth($sid, config('services.twilio.token'))
            ->asForm()
            ->post($url, [
                'From' => $from,
                'To' => $to,
                'Body' => $body,
            ]);

        if (! $response->successful()) {
            Log::warning('Twilio message failed.', [
                'to' => $to,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return false;
        }

        return true;
    }
}
