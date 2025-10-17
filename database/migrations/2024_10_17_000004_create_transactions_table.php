<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['deposit', 'withdrawal', 'bonus', 'bet_win', 'bet_loss', 'referral_bonus']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->enum('status', ['pending', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->string('proof_url')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('transaction_hash')->unique();
            $table->timestamps();
            
            $table->index(['tenant_id', 'player_id', 'created_at']);
            $table->index(['tenant_id', 'type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};