<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Models\User;
use App\Models\Proforma;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profoma>
 */
class ProformaFactory extends Factory
{
        protected $model = Proforma::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       // Récupérer un client existant
        $client = Client::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();

        // Sécurité : éviter de lancer la factory si tu n’as pas de clients ou users
        if (!$client || !$user) {
            throw new \Exception('Il faut au moins un client et un user dans la base.');
        }

        $date = $this->faker->dateTimeThisYear();
        $valid_until = (clone $date)->modify('+30 days');

        $subtotal = $this->faker->randomFloat(2, 100, 5000);
        $tax_amount = $this->faker->randomFloat(2, 20, 500);
        $total = $subtotal + $tax_amount;

        return [
            'client_id'         => $client->id,
            'user_id'           => $user->id,
            'number'            => Proforma::generateNumber(),
            'date'              => $date,
            'valid_until'       => $valid_until,
            'subtotal'          => $subtotal,
            'tax_amount'        => $tax_amount,
            'total'             => $total,
            'status'            => $this->faker->randomElement(['draft', 'sent', 'paid', 'cancelled']),
            'notes'             => $this->faker->sentence(),
            'conditionPaiement' => $this->faker->sentence(),
            'delaiDeploiment'   => $this->faker->sentence(),
            'garantieMateriel'  => $this->faker->sentence(),
        ];
    }
}
