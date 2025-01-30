<?php

use Tests\TestCase;
use \Illuminate\Foundation\Testing\RefreshDatabase;

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

        $response = $this->postJson('/api/orders', $data, ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 201,
                'message' => 'Create an order',
                'data' => [
                    'id' => 1,
                    'order_number' => '123456',
                    'items' => [
                        ['product_name' => 'Pizza', 'quantity' => 1, 'price' => 57.90],
                        ['product_name' => 'Soda', 'quantity' => 2, 'price' => 10]
                    ],
                    'status' => 'pending',
                    'total_amount' => 77.90
                ]
        ]);
    }
}
