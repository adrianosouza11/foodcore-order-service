<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\Order;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    private array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'order_number' => '123456',
            'items' => [
                ['product_name' => 'Pizza', 'quantity' => 1, 'price' => 57.90],
                ['product_name' => 'Soda', 'quantity' => 2, 'price' => 10]
            ],
            'status' => 'pending',
            'total_amount' => 77.90
        ];
    }

    public function test_can_create_order_via_api()
    {
        $response = $this->postJson('/api/orders', $this->data);

        $expectedOrderItemsStructure = ['items.0', function (AssertableJson $json) {
            $json->has('id');
            $json->has('order_id');
            $json->has('product_name');
            $json->has('quantity');
            $json->has('price');
            $json->has('created_at');
            $json->has('updated_at');
        }];

        $expectedOrderStructure = function(AssertableJson $json) use ($expectedOrderItemsStructure){
            $json->has('status');
            $json->has('message');
            $json->has('data', function (AssertableJson $json) use ($expectedOrderItemsStructure){
                $json->has('id');
                $json->has('order_number');
                $json->has('status');
                $json->has('total_amount');
                $json->has('items', 2);
                $json->has('created_at');
                $json->has('updated_at');
                $json->has(...$expectedOrderItemsStructure);
            });
        };

        $response->assertStatus(201)
            ->assertJson($expectedOrderStructure);
    }

    public function test_should_return_list_orders_via_api()
    {
        Order::factory()->count(3)->withItems(2)->create();

        $response = $this->get('/api/orders');

        $expectedOrderItemsStructure = ['items.0', function (AssertableJson $json) {
            $json->has('id');
            $json->has('order_id');
            $json->has('product_name');
            $json->has('quantity');
            $json->has('price');
            $json->has('created_at');
            $json->has('updated_at');
        }];

        $expectedOrderStructure = function(AssertableJson $json) use ($expectedOrderItemsStructure){
            $json->has('status');
            $json->has('message');
            $json->has('data.0', function (AssertableJson $json) use ($expectedOrderItemsStructure){
                $json->has('id');
                $json->has('order_number');
                $json->has('status');
                $json->has('total_amount');
                $json->has('items', 2);
                $json->has('created_at');
                $json->has('updated_at');
                $json->has('user_id');
                $json->has(...$expectedOrderItemsStructure);
            });
        };

        $response->assertStatus(200)
            ->assertJson($expectedOrderStructure);
    }
}
