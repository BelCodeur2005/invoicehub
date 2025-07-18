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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('address');
            $table->string('language', 2)->default('fr')->after('avatar');
            $table->string('timezone')->default('Africa/Douala')->after('language');
            $table->boolean('email_notifications')->default(true)->after('timezone');
            $table->boolean('invoice_notifications')->default(true)->after('email_notifications');
            $table->enum('theme', ['light', 'dark', 'system'])->default('system')->after('invoice_notifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'avatar',
                'language',
                'timezone',
                'email_notifications',
                'invoice_notifications',
                'payment_notifications',
                'theme'
            ]);
        });
    }
};
