<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InventoryAdjustmentRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Support\AdminTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $movements = AdminTable::paginate(
            StockMovement::query()->with(['product', 'productVariant', 'user']),
            $request,
            [
                'type' => 'type',
                'quantity_change' => 'quantity_change',
                'quantity_after' => 'quantity_after',
                'created_at' => 'created_at',
            ],
            'created_at',
            'desc',
        );

        return view('admin.inventory.index', compact('movements'));
    }

    public function create(): View
    {
        $products = Product::query()->orderBy('name')->get(['id', 'name', 'quantity']);

        return view('admin.inventory.create', compact('products'));
    }

    public function store(InventoryAdjustmentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $product = Product::query()->lockForUpdate()->findOrFail($data['product_id']);
            $change = (int) $data['quantity_change'];

            if (! empty($data['product_variant_id'])) {
                $variant = ProductVariant::query()
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->findOrFail($data['product_variant_id']);

                $variant->update([
                    'quantity' => max(0, (int) $variant->quantity + $change),
                ]);

                $quantityAfter = (int) $variant->quantity;
                $product->update([
                    'quantity' => (int) $product->variants()->sum('quantity'),
                ]);
            } else {
                $product->update([
                    'quantity' => max(0, (int) $product->quantity + $change),
                ]);
                $quantityAfter = (int) $product->quantity;
            }

            StockMovement::query()->create([
                'product_id' => $product->id,
                'product_variant_id' => $data['product_variant_id'] ?? null,
                'type' => $data['type'],
                'quantity_change' => $change,
                'quantity_after' => $quantityAfter,
                'reason' => $data['reason'] ?? null,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('admin.inventory.index')
            ->with('success', 'Stock adjustment recorded.');
    }
}
