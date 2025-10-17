<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['welcome', 'referral', 'spin_wheel', 'custom', 'birthday', 'loyalty']);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('related_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'player_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};