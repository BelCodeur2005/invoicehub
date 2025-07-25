<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->randomFloat(2, 10, 1000),
            'type'        => $this->faker->randomElement(['service', 'product']),
            'tax_rate'    => $this->faker->randomFloat(2, 5, 20),
            'is_active'   => $this->faker->boolean(90),
            ];
    }
}
