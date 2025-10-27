<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Tipo de suscripción
            $table->enum('subscription_type', ['monthly', 'prepaid'])
                  ->default('prepaid')
                  ->after('is_active')
                  ->comment('Tipo: monthly (abono) o prepaid (fichas)');
            
            // Para suscripción MONTHLY
            $table->decimal('monthly_fee', 10, 2)
                  ->nullable()
                  ->after('subscription_type')
                  ->comment('Cuota mensual (solo para tipo monthly)');
            
            $table->date('last_payment_date')
                  ->nullable()
                  ->after('monthly_fee')
                  ->comment('Fecha del último pago mensual');
            
            $table->date('next_payment_date')
                  ->nullable()
                  ->after('last_payment_date')
                  ->comment('Fecha del próximo pago mensual');
            
            // Para suscripción PREPAID
            $table->integer('chips_balance')
                  ->default(0)
                  ->after('next_payment_date')
                  ->comment('Saldo de fichas disponibles (solo para tipo prepaid)');
            
            $table->decimal('chip_price', 10, 2)
                  ->default(100.00)
                  ->after('chips_balance')
                  ->comment('Precio por ficha para este tenant');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_type',
                'monthly_fee',
                'last_payment_date',
                'next_payment_date',
                'chips_balance',
                'chip_price'
            ]);
        });
    }
};