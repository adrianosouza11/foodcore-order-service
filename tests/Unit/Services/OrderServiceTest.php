<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_can_create_order(): void
    {
        //Arrange
        $orderRepositoryMock = Mockery::mock(OrderRepository::class);

        $data = [
            'order_number' => '123456',
            'items' => [
               ['product_name' => 'Pizza', 'quantity' => 1, 'price' => 57.90],
               ['product_name' => 'Soda', 'quantity' => 2, 'price' => 10]
            ],
            'status' => 'pending',
            'total_amount' => 77.90
        ];

        $orderRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn(new Order([
                'id' => 1,
                'order_number' => '123456',
                'total_amount' => 77.90,
                'status' => 'pending'
            ]));

        //Act
        $orderService = new OrderService($orderRepositoryMock);
        $order = $orderService->createOrder($data);

        //Assert
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('123456', $order->order_number);
        $this->assertEquals(77.90, $order->total_amount);
        $this->assertEquals('pending', $order->status);
    }

    public function test_can_create_order_with_items() : void
    {
        //Arrange
        $orderRepositoryMock = $this->createMock(OrderRepository::class);

        $data = [
            'order_number' => '123456',
            'items' => [
                ['product_name' => 'Pizza', 'quantity' => 1, 'price' => 57.90],
                ['product_name' => 'Soda', 'quantity' => 2, 'price' => 10]
            ],
            'status' => 'pending',
            'total_amount' => 67.90
        ];

        $items = $data['items'];

        $expectedOrder = new Order([
            'id' => 1,
            'order_number' => '123456',
            'total_amount' => 77.90,
            'status' => 'pending'
        ]);

        $expectedOrder->setRelation('items', collect([
            new OrderItem([ 'order_id' => 1, 'product_name' => 'Pizza', 'quantity' => 1, 'price' => 57.90 ]),
            new OrderItem([ 'order_id' => 1, 'product_name' => 'Soda', 'quantity' => 2, 'price' => 10 ])
        ]));

        $orderRepositoryMock
            ->expects($this->once())
            ->method('createWithItems')
            ->with($data, $items)
            ->willReturn($expectedOrder);

        //Act
        $orderService = new OrderService($orderRepositoryMock);
        $order = $orderService->createOrderWithItems($data, $items);

        //Assert
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('123456', $order->order_number);
        $this->assertEquals(77.90, $order->total_amount);
        $this->assertEquals('pending', $order->status);

        $this->assertCount(2, $order->items);
        $this->assertEquals('Pizza', $order->items[0]->product_name);
        $this->assertEquals(1, $order->items[0]->quantity);
        $this->assertEquals(57.90, $order->items[0]->price);
    }
}
