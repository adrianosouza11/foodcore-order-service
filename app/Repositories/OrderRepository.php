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
     * @param string $order_number
     * @return Order|null
     */
    public function findByOrderNumber(string $order_number) : ?Order
    {
        return Order::where(['order_number' => $order_number])->first();
    }

    /**
     * @param string $order_number
     * @param array $params
     * @return Order|null
     */
    public function updateByOrderNumber(string $order_number, array $params) : ?Order
    {
        $order = $this->findByOrderNumber($order_number);

        if(!$order)
            return null;

        $order->fill($params);
        $order->save();

        return $order;
    }
}
