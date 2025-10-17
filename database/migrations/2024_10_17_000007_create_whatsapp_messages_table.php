<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_number');
            $table->string('to_number');
            $table->enum('direction', ['inbound', 'outbound']);
            $table->text('body');
            $table->string('media_url')->nullable();
            $table->string('twilio_sid')->nullable()->unique();
            $table->enum('status', ['sent', 'delivered', 'failed', 'received'])->default('sent');
            $table->timestamps();
            
            $table->index(['tenant_id', 'player_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};