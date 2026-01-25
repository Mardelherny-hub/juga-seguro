<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('welcome_bonus_is_percentage')->default(false)->after('welcome_bonus_amount');
            $table->decimal('welcome_bonus_max', 10, 2)->nullable()->after('welcome_bonus_is_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['welcome_bonus_is_percentage', 'welcome_bonus_max']);
        });
    }
};