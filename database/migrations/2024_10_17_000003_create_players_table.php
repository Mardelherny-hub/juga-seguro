<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('referral_code')->unique();
            $table->foreignId('referred_by')->nullable()->constrained('players')->nullOnDelete();
            $table->enum('status', ['active', 'suspended', 'blocked'])->default('active');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['tenant_id', 'phone']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};