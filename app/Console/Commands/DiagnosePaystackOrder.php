<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaystackPaymentReconciliationService;
use App\Services\PaystackService;
use Illuminate\Console\Command;

class DiagnosePaystackOrder extends Command
{
    protected $signature = 'orders:diagnose-paystack {order : Order number or ID}';

    protected $description = 'Show why a Paystack order may still be pending';

    public function handle(
        PaystackService $paystack,
        PaystackPaymentReconciliationService $reconciliation,
    ): int {
        $input = (string) $this->argument('order');

        $order = Order::query()
            ->when(is_numeric($input), fn ($query) => $query->whereKey((int) $input))
            ->when(! is_numeric($input), fn ($query) => $query->where('order_number', $input))
            ->with('payment')
            ->first();

        if (! $order) {
            $this->error('Order not found.');

            return self::FAILURE;
        }

        $this->info("Order {$order->order_number} (ID {$order->id})");
        $this->line("Status: {$order->status->label()}");
        $this->line("Payment status: {$order->payment_status->label()}");
        $this->line('Paystack mode: '.$paystack->mode());
        $this->line('Callback URL: '.$paystack->callbackUrl());
        $this->newLine();

        $payments = Payment::query()
            ->where('order_id', $order->id)
            ->orderByDesc('id')
            ->get();

        if ($payments->isEmpty()) {
            $this->warn('No payment records found for this order.');
        }

        foreach ($payments as $payment) {
            $this->line("Payment #{$payment->id}: {$payment->reference} ({$payment->status->value})");

            try {
                $data = $paystack->verify($payment->reference);
                $this->line('  Paystack status: '.($data['status'] ?? 'unknown'));
            } catch (\Throwable $exception) {
                $this->error('  Verify failed: '.$exception->getMessage());
            }
        }

        $this->newLine();

        if ($order->payment_status !== PaymentStatus::Paid) {
            $this->info('Attempting reconciliation...');

            if ($reconciliation->reconcileOrder($order)) {
                $order->refresh();
                $this->info('Reconciliation succeeded. Payment status is now: '.$order->payment_status->label());
            } else {
                $this->error('Reconciliation failed. Check Paystack mode/keys and webhook URL.');
            }
        }

        return self::SUCCESS;
    }
}
