<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * @inheritDoc
     */
    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'product_name' => $this->faker->word(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'created_at' => now(),
            'updated_at' =>  now()
        ];
    }
}
