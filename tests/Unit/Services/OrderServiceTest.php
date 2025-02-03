<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private $orderRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderRepositoryMock = Mockery::mock(OrderRepository::class);
    }

    /**
     * A basic feature test example.
     */
    public function test_can_create_order(): void
    {
        //Arrange
        $data = [
            'order_number' => '123456',
            'items' => [
               ['product_name' => 'Pizza', 'quantity' => 1, 'price' => 57.90],
               ['product_name' => 'Soda', 'quantity' => 2, 'price' => 10]
            ],
            'status' => 'pending',
            'total_amount' => 77.90
        ];

        $this->orderRepositoryMock
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
        $orderService = new OrderService($this->orderRepositoryMock);
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

        $this->orderRepositoryMock
            ->shouldReceive('createWithItems')
            ->once()
            ->with($data, $items)
            ->andReturn($expectedOrder);

        //Act
        $orderService = new OrderService($this->orderRepositoryMock);
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

    public function test_should_return_list_orders() : void
    {
        //Arrange
        $expectedOrderList = Collection::make([
            (new Order([ 'id' => 1,'order_number' => '123456', 'total_amount' => 77.90, 'status' => 'pending']))
                ->setRelation('items',collect([
                        new OrderItem([ 'order_id' => 1, 'product_name' => 'Pizza', 'quantity' => 1, 'price' => 57.90 ]),
                        new OrderItem([ 'order_id' => 1, 'product_name' => 'Soda', 'quantity' => 2, 'price' => 10 ])
                    ])
                ),
            (new Order([ 'id' => 2,'order_number' => '987654', 'total_amount' => 25, 'status' => 'done']))
                ->setRelation('items', collect([
                    new OrderItem([ 'order_id' => 2, 'product_name' => 'Chop Suey', 'quantity' => 1, 'price' => 25 ])
                ])),
            (new Order([ 'id' => 3,'order_number' => '534210', 'total_amount' => 90, 'status' => 'pending']))
                ->setRelation('items', collect([
                    new OrderItem([ 'order_id' => 3, 'product_name' => 'X-BURG', 'quantity' => 2, 'price' => 90 ])
                ]))
        ]);

        $this->orderRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($expectedOrderList);

        //Act
        $orderService = new OrderService($this->orderRepositoryMock);
        $orderList = $orderService->listOrders();

        //Assert
        $this->assertCount(3, $orderList);

        $this->assertInstanceOf(Order::class, $orderList[0]);
        $this->assertEquals('123456', $orderList[0]->order_number);
        $this->assertEquals(77.90, $orderList[0]->total_amount);
        $this->assertEquals('pending', $orderList[0]->status);

        $this->assertCount(2, $orderList[0]->items);
        $this->assertEquals('Pizza', $orderList[0]->items[0]->product_name);
        $this->assertEquals(1, $orderList[0]->items[0]->quantity);
        $this->assertEquals(57.90, $orderList[0]->items[0]->price);
    }

    public function test_should_update_order_status()
    {
        //Arrange
        $status = 'done';

        $expectedOrder = new Order([
            'id' => 1,
            'order_number' => '123456',
            'total_amount' => 77.90,
            'status' => 'done'
        ]);

       $this->orderRepositoryMock
           ->shouldReceive('updateByOrderNumber')
           ->once()
           ->with('123456', [ 'status' => $status])
           ->andReturn($expectedOrder);

       //Act
       $orderService = new OrderService($this->orderRepositoryMock);
       $updated = $orderService->updateStatusByOrderNumber('123456', $status);

       //Assert
       $this->assertInstanceOf(Order::class, $updated);
       $this->assertEquals('123456', $updated->order_number);
       $this->assertEquals('done', $updated->status);
       $this->assertEquals(77.90, $updated->total_amount);
    }
}
