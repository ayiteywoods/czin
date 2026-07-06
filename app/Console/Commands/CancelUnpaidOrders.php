<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\OrderCancellationService;
use App\Services\PaystackPaymentReconciliationService;
use Illuminate\Console\Command;

class CancelUnpaidOrders extends Command
{
    protected $signature = 'orders:cancel-unpaid';

    protected $description = 'Cancel unpaid orders past their payment deadline and release reserved stock';

    public function handle(
        OrderCancellationService $cancellations,
        PaystackPaymentReconciliationService $reconciliation,
    ): int {
        $orders = Order::query()
            ->where('status', OrderStatus::PendingPayment)
            ->where('payment_status', PaymentStatus::Pending)
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<', now())
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No unpaid orders to cancel.');

            return self::SUCCESS;
        }

        $cancelled = 0;

        foreach ($orders as $order) {
            if ($reconciliation->reconcileOrder($order)) {
                $this->line("Order {$order->order_number} was paid on Paystack and marked as paid.");

                continue;
            }

            if ($cancellations->cancelUnpaid($order)) {
                $cancelled++;
            }
        }

        $this->info("Cancelled {$cancelled} unpaid order(s).");

        return self::SUCCESS;
    }
}
