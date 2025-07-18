<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Proforma_item;
use App\Models\Proforma;
use App\Models\Product;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proforma_item>
 */
class Proforma_itemFactory extends Factory
{
        protected $model = Proforma_item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Sélectionner un produit déjà existant
        $product = Product::inRandomOrder()->first();

        // Sélectionner une proforma déjà existante
        $proforma = Proforma::inRandomOrder()->first();

        if (!$product || !$proforma) {
            throw new \Exception('Assure-toi d\'avoir au moins un produit et une proforma dans ta base.');
        }

        $quantity = $this->faker->numberBetween(1, 10);
        $price = $product->price;
        $tax_rate = $product->tax_rate;

        $subtotal = $quantity * $price;
        $tax_amount = $subtotal * ($tax_rate / 100);
        $total = $subtotal + $tax_amount;

        return [
            'proforma_id' => $proforma->id,
            'product_id'  => $product->id,
            'quantity'    => $quantity,
            'price'       => $price,
            'tax_rate'    => $tax_rate,
            'subtotal'    => $subtotal,
            'tax_amount'  => $tax_amount,
            'total'       => $total,
        ];
    }
}
