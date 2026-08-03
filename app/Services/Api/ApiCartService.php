<?php

namespace App\Services\Api;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;

class ApiCartService
{
    public function __construct(
        protected Request $request,
    ) {}

    public function resolve(?User $user = null): Cart
    {
        $user ??= $this->request->user();

        if ($user) {
            return Cart::query()->firstOrCreate([
                'user_id' => $user->id,
            ]);
        }

        $deviceId = $this->deviceId();

        return Cart::query()->firstOrCreate([
            'session_id' => 'device:'.$deviceId,
        ]);
    }

    public function deviceId(): string
    {
        $deviceId = trim((string) $this->request->header('X-Device-Id'));

        if ($deviceId === '') {
            $deviceId = trim((string) $this->request->input('device_id'));
        }

        if ($deviceId === '') {
            abort(response()->json([
                'message' => 'X-Device-Id header is required for guest cart operations.',
            ], 422));
        }

        if (strlen($deviceId) > 120) {
            abort(response()->json([
                'message' => 'X-Device-Id is too long.',
            ], 422));
        }

        return $deviceId;
    }

    public function mergeGuestCartIntoUser(User $user): void
    {
        $deviceId = trim((string) $this->request->header('X-Device-Id'));

        if ($deviceId === '') {
            return;
        }

        $guestCart = Cart::query()
            ->with('items')
            ->where('session_id', 'device:'.$deviceId)
            ->whereNull('user_id')
            ->first();

        if (! $guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = Cart::query()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->where(function ($query) use ($guestItem) {
                    if (filled($guestItem->special_request)) {
                        $query->where('special_request', $guestItem->special_request);
                    } else {
                        $query->whereNull('special_request');
                    }
                })
                ->first();

            if ($existing) {
                $existing->update([
                    'quantity' => $existing->quantity + $guestItem->quantity,
                    'unit_price' => $guestItem->unit_price,
                ]);
            } else {
                $userCart->items()->create([
                    'product_id' => $guestItem->product_id,
                    'product_variant_id' => $guestItem->product_variant_id,
                    'quantity' => $guestItem->quantity,
                    'unit_price' => $guestItem->unit_price,
                    'special_request' => $guestItem->special_request,
                ]);
            }
        }

        $guestCart->items()->delete();
        $guestCart->delete();
    }
}
