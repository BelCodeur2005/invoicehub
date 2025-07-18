<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Proforma;
use App\Models\Proforma_item;
use App\Models\Invoice;
use App\Models\Invoice_item;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //   User::factory(5)->create();
        // Client::factory(10)->create();
        // Product::factory(15)->create();

        // // ✅ Génère 5 proformas d'un coup MAIS en boucle
        //     $proforma = Proforma::factory()->create();

        //     Proforma_item::factory()->count(3)->create([
        //         'proforma_id' => $proforma->id,
        //     ]);

        //     $proforma->calculateTotals();


        // // ✅ Génère 5 factures d'un coup
        //     $invoice = Invoice::factory()->create();

        //     Invoice_item::factory()->count(3)->create([
        //         'invoice_id' => $invoice->id,
        //     ]);

        //     $invoice->calculateTotals();
    //     User::create([
    //     'name' => 'Admin',
    //     'email' => 'admin@example.com',
    //     'password' => Hash::make('   '), // n'oublie pas de hasher le mot de passe
    //     'role' => User::ROLE_ADMIN,
    //     'is_active' => true,
    // ]);
    //         User::create([
    //     'name' => 'User',
    //     'email' => 'user@example.com',
    //     'password' => Hash::make('motdepasse126'), // n'oublie pas de hasher le mot de passe
    //     'role' => User::ROLE_USER,
    //     'is_active' => true,
    // ]);
            User::create([
        'name' => 'Manager',
        'email' => 'manager@example.com',
        'password' => Hash::make('motdepasse126'), // n'oublie pas de hasher le mot de passe
        'role' => User::ROLE_MANAGER,
        'is_active' => true,
    ]);

    }
}
