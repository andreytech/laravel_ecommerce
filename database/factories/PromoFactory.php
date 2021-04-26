<?php

namespace Database\Factories;

use App\Models\Promo;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Promo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        	'name' => $this->faker->title,
            'type' => $this->faker->randomElement(['amount_off' ,'percent_off']),
			'value' => $this->faker->numberBetween(1,100),
			'code' => $this->faker->regexify('[A-Z0-9]{'.rand(1,20).'}'),
        ];
    }
}
