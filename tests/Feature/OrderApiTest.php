<?php

use Tests\TestCase;
use \Illuminate\Foundation\Testing\RefreshDatabase;
use \Illuminate\Testing\Fluent\AssertableJson;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_order_via_api()
    {
        $data = [
            'order_number' => '123456',
            'items' => [
                ['product_name' => 'Pizza', 'quantity' => 1, 'price' => 57.90],
                ['product_name' => 'Soda', 'quantity' => 2, 'price' => 10]
            ],
            'status' => 'pending',
            'total_amount' => 77.90
        ];

        $response = $this->postJson('/api/orders', $data);

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
}
