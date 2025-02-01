<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    public function create(array $data) : Order
    {
        return Order::create($data);
    }

    public function createWithItems(array $data, array $items) : Order
    {
        DB::beginTransaction();

        $order = Order::create($data);

        foreach ($items as $item)
            $order->items()->create($item);

        DB::commit();

        return $order;
    }

    public function getAll(): Collection
    {
        return Order::with('items')->get();
    }

    /**
     * @param string $orderNumber
     * @param array $params
     * @return Order
     */
    public function updateByOrderNumber(string $orderNumber, array $params) : Order
    {
        return Order::where('order_number', $orderNumber)->update($params);
    }
}
