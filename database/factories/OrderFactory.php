<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @inheritDoc
     */
    public function definition(): array
    {
        return [
            'order_number' => $this->faker->bothify('###???'),
            'total_amount' => $this->faker->randomFloat(2, 10, 500),
            'status' => $this->faker->randomElement(['pending', 'done']),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    public function withItems($count = 3): OrderFactory
    {
        return $this->afterCreating(function (Order $order) use ($count) {
            OrderItem::factory()->count($count)->create([
               'order_id' => $order->id
            ]);
        });
    }
}
