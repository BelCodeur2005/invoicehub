<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'           => $this->faker->company,
            'email'          => $this->faker->unique()->companyEmail,
            'phone'          => $this->faker->phoneNumber,
            'address'        => $this->faker->address,
            'niu'            => strtoupper($this->faker->bothify('NIU-#######')),
            'rccm'           => strtoupper($this->faker->bothify('RCCM-#####')),
            'bp'             => $this->faker->bothify('BP ###'),
            'account_number' => $this->faker->bankAccountNumber,
            'bank'           => $this->faker->company . ' Bank',
            'country'        => $this->faker->country,
            'street'         => $this->faker->streetAddress,
            'city'           => $this->faker->city,
            ];
    }
}
