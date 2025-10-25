<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            
            // Tipos: system, player, agent
            $table->enum('sender_type', ['system', 'player', 'agent']);
            $table->foreignId('sender_id')->nullable(); // null si es system, sino user_id o player_id
            
            $table->text('message');
            
            // Categorías para filtrar y organizar
            $table->enum('category', ['transaction', 'account', 'bonus', 'support', 'general'])->default('general');
            
            // Relacionado con una transacción específica (opcional)
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            
            // Control de lectura
            $table->timestamp('read_by_player_at')->nullable();
            $table->timestamp('read_by_agent_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['tenant_id', 'player_id']);
            $table->index(['player_id', 'created_at']);
            $table->index(['read_by_player_at']);
            $table->index(['read_by_agent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_messages');
    }
};