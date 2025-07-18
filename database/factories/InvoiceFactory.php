<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\User;
use App\Models\Proforma;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
        protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $client = Client::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();

        // Si tu veux, tu peux lier une proforma existante (optionnel)
        $proforma = Proforma::inRandomOrder()->first();

        if (!$client || !$user) {
            throw new \Exception('Il faut au moins un client et un user dans la base pour créer une facture.');
        }

        $date = $this->faker->dateTimeThisYear();
        $due_date = (clone $date)->modify('+30 days');

        $subtotal = $this->faker->randomFloat(2, 100, 5000);
        $tax_amount = $this->faker->randomFloat(2, 20, 500);
        $total = $subtotal + $tax_amount;

        return [
            'client_id'   => $client->id,
            'user_id'     => $user->id,
            'proforma_id' => null, // facultatif : peut être null
            'number'      => Invoice::generateNumber(),
            'date'        => $date,
            'due_date'    => $due_date,
            'subtotal'    => $subtotal,
            'tax_amount'  => $tax_amount,
            'total'       => $total,
            'status'      => $this->faker->randomElement(['draft', 'sent', 'paid', 'cancelled']),
            'notes'       => $this->faker->sentence(),
        ];
    }
}
