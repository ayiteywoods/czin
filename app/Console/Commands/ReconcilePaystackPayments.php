<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaystackPaymentReconciliationService;
use App\Support\OrderLookup;
use Illuminate\Console\Command;

class ReconcilePaystackPayments extends Command
{
    protected $signature = 'orders:reconcile-paystack
                            {--days=30 : How far back to check}
                            {--order= : Reconcile a specific order number}
                            {--reference= : Reconcile using a Paystack reference from the dashboard}
                            {--details : Show details for each check}';

    protected $description = 'Verify pending Paystack payments and mark matching orders as paid';

    public function handle(PaystackPaymentReconciliationService $reconciliation): int
    {
        if ($reference = $this->option('reference')) {
            if ($reconciliation->reconcileByReference((string) $reference)) {
                $this->info("Reconciled payment reference {$reference}.");

                return self::SUCCESS;
            }

            $this->error("Could not reconcile payment reference {$reference}.");

            return self::FAILURE;
        }

        if ($orderNumber = $this->option('order')) {
            $order = OrderLookup::findByNumberOrId((string) $orderNumber);

            if (! $order) {
                $this->error("Order {$orderNumber} not found.");

                return self::FAILURE;
            }

            if ($reconciliation->reconcileOrder($order)) {
                $this->info("Order {$orderNumber} marked as paid.");

                return self::SUCCESS;
            }

            $this->error("No successful Paystack payment found for order {$orderNumber}.");

            return self::FAILURE;
        }

        $days = max(1, (int) $this->option('days'));
        $verbose = (bool) $this->option('details');
        $reconciled = 0;
        $checkedPayments = 0;
        $checkedOrders = 0;

        $payments = Payment::query()
            ->where('provider', 'paystack')
            ->where('created_at', '>=', now()->subDays($days))
            ->whereHas('order', fn ($query) => $query->where('payment_status', '!=', PaymentStatus::Paid))
            ->orderByDesc('id')
            ->get();

        foreach ($payments as $payment) {
            $checkedPayments++;
            $result = $reconciliation->reconcilePayment($payment);

            if ($verbose) {
                $this->line("Payment {$payment->reference} (order {$payment->order?->order_number}): {$result['reason']}");
            }

            if ($result['reconciled'] && $payment->order?->fresh()->payment_status === PaymentStatus::Paid) {
                $reconciled++;
                $this->line("Marked order #{$payment->order?->order_number} as paid ({$payment->reference}).");
            }
        }

        $orders = Order::query()
            ->where('payment_status', '!=', PaymentStatus::Paid)
            ->whereIn('status', [OrderStatus::PendingPayment, OrderStatus::Cancelled])
            ->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('id')
            ->get();

        foreach ($orders as $order) {
            if ($order->payment_status === PaymentStatus::Paid) {
                continue;
            }

            $checkedOrders++;
            $before = $order->payment_status;

            if ($reconciliation->reconcileOrder($order)) {
                $order->refresh();

                if ($order->payment_status === PaymentStatus::Paid && $before !== PaymentStatus::Paid) {
                    $reconciled++;
                    $this->line("Marked order #{$order->order_number} as paid via Paystack search.");
                }
            } elseif ($verbose) {
                $this->line("Order {$order->order_number}: no successful Paystack payment found");
            }
        }

        $this->info("Checked {$checkedPayments} payment(s) and {$checkedOrders} unpaid order(s).");
        $this->info("Reconciled {$reconciled} payment(s).");

        if ($reconciled === 0 && ! $verbose) {
            $this->comment('Run with --details to see why each check failed, or --order=ORDER_NUMBER for one order.');
        }

        return self::SUCCESS;
    }
}
