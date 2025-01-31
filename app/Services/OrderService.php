<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    protected OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function createOrder(array $data) : Order
    {
        return $this->orderRepository->create($data);
    }

    public function createOrderWithItems(array $data, array $items) : Order
    {
        return $this->orderRepository->createWithItems($data, $items);
    }

    /**
     * @return Collection
     */
    public function listOrders() : Collection
    {
        return $this->orderRepository->getAll();
    }
}
