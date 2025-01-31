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
        return Order::all();
    }
}
