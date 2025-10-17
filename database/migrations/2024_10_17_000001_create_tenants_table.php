<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Casino Royal"
            $table->string('slug')->unique(); // casino-royal
            $table->string('domain')->unique()->nullable();
            $table->string('logo_url')->nullable();
            $table->string('primary_color')->default('#3B82F6');
            $table->string('secondary_color')->default('#10B981');
            $table->text('whatsapp_token')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->json('settings')->nullable();
            $table->json('bank_accounts')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};