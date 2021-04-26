<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
			'order_id' => Order::factory(),
			'product_id' => Product::factory(),
			'quantity' => $this->faker->numberBetween(1,99),
			'total_amount' => $this->faker->numberBetween(1,999999),
        ];
    }
}
