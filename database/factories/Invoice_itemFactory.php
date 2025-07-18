<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Invoice_item;
use App\Models\Invoice;
use App\Models\Product;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice_item>
 */
class Invoice_itemFactory extends Factory
{
        protected $model = Invoice_item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       // ⚡ Sélectionne un produit déjà existant
        $product = Product::inRandomOrder()->first();

        // Sélectionne une facture déjà existante
        $invoice = Invoice::inRandomOrder()->first();

        // Si aucun produit ou aucune facture en base, ça t'évite une erreur
        if (!$product || !$invoice) {
            throw new \Exception('Assure-toi d\'avoir au moins un produit et une facture en base.');
        }

        $quantity = $this->faker->numberBetween(1, 10);
        $price = $product->price;
        $tax_rate = $product->tax_rate;

        $subtotal = $quantity * $price;
        $tax_amount = $subtotal * ($tax_rate / 100);
        $total = $subtotal + $tax_amount;

        return [
            'invoice_id'  => $invoice->id,
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
