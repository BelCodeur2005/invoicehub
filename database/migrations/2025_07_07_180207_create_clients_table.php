<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable(); // nullable au cas où pas obligatoire
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            // Ajouts spécifiques
            $table->string('niu')->nullable(); // NIU
            $table->string('rccm')->nullable(); // RCCM
            $table->string('bp')->nullable();   // BP
            $table->string('account_number')->nullable(); // numeroCompteBancaire
            $table->string('bank')->nullable(); // Bank
            $table->string('country')->nullable(); // pays
            $table->string('street')->nullable();  // Rue
            $table->string('city')->nullable();    // ville

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
