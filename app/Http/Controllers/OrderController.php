<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\OrderService;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService(new OrderRepository());
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $list = $this->orderService->listOrders();

        return response()->json([
            'status' => 200,
            'message' => 'List orders',
            'data' => $list
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $items = $request->post('items');

        $created = $this->orderService->createOrderWithItems($request->validated(), $items);

        return response()->json([
            'message' => 'Create an order',
            'status' => 201,
            'data' => [
                ...$created->getAttributes(),
               'status' => $created->status,
               'items' => $created->items
            ]
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(string $order_number,UpdateOrderStatusRequest $request)
    {
        $updated = $this->orderService->updateStatusByOrderNumber($order_number, $request->validated('status'));

        return response()->json([
            'status' => 200,
            'message' => 'Updated order',
            'data' => $updated
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
