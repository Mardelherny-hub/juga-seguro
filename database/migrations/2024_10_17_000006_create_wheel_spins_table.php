<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wheel_spins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->decimal('prize_amount', 10, 2)->default(0);
            $table->enum('prize_type', ['cash', 'bonus', 'free_spin', 'nothing']);
            $table->foreignId('bonus_id')->nullable()->constrained()->nullOnDelete();
            $table->text('prize_description')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'player_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wheel_spins');
    }
};