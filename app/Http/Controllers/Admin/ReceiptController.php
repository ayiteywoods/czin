<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function print(Order $order): View
    {
        $order->load(['items', 'diningTable', 'payment']);

        return view('admin.receipts.print', [
            'order' => $order,
            'store' => StoreSetting::current(),
            'currency' => (string) config('shop.currency_symbol', 'GHS'),
            'autoPrint' => request()->boolean('autoprint', true),
        ]);
    }
}
