<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            
            $table->string('account_holder', 255)->comment('Titular de la cuenta');
            $table->string('bank_name', 100)->nullable()->comment('Nombre del banco');
            $table->string('alias', 100)->nullable()->comment('Alias de la cuenta');
            $table->string('cbu', 22)->nullable()->comment('CBU de la cuenta');
            $table->string('cvu', 22)->nullable()->comment('CVU de la cuenta');
            
            $table->text('notes')->nullable()->comment('Notas (ej: el alias puede cambiar)');
            
            $table->boolean('is_active')->default(false)->comment('Cuenta activa visible a players');
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->timestamps();
            
            // Índices
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};