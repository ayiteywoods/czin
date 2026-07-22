<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class OrderLookup
{
    public static function findByNumberOrId(string $input): ?Order
    {
        $input = trim($input);

        if ($input === '') {
            return null;
        }

        $order = Order::query()->where('order_number', $input)->first();

        if ($order) {
            return $order;
        }

        if (str_starts_with(strtolower($input), 'id:')) {
            return Order::query()->find((int) substr($input, 3));
        }

        if (! is_numeric($input)) {
            return null;
        }

        $numeric = (int) $input;

        $padded = OrderNumberGenerator::format($numeric);

        if ($padded !== $input) {
            $order = Order::query()->where('order_number', $padded)->first();

            if ($order) {
                return $order;
            }
        }

        $order = self::numericOrderNumberQuery($numeric)->first();

        if ($order) {
            return $order;
        }

        return Order::query()->find($numeric);
    }

    /**
     * @return Builder<Order>
     */
    public static function numericOrderNumberQuery(int $numeric): Builder
    {
        $driver = Order::query()->getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return Order::query()->whereRaw('CAST(order_number AS INTEGER) = ?', [$numeric]);
        }

        return Order::query()->whereRaw('CAST(order_number AS UNSIGNED) = ?', [$numeric]);
    }
}
