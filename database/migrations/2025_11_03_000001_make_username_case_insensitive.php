<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. PRIMERO eliminar índice único para poder hacer la actualización
        Schema::table('players', function (Blueprint $table) {
            $table->dropUnique('players_tenant_username_unique');
        });
        
        // 2. Identificar y marcar duplicados con sufijo temporal
        DB::statement("
            UPDATE players p1
            SET username = username || '_dup_' || id
            WHERE EXISTS (
                SELECT 1 FROM players p2
                WHERE p2.tenant_id = p1.tenant_id
                AND LOWER(p2.username) = LOWER(p1.username)
                AND p2.id < p1.id
                AND p2.username != p1.username
            )
        ");
        
        // 3. Normalizar todos a minúsculas
        DB::statement("UPDATE players SET username = LOWER(username)");
        
        // 4. Crear índice único case-insensitive
        DB::statement('CREATE UNIQUE INDEX players_tenant_username_unique ON players (tenant_id, LOWER(username))');
        
        // 5. Reportar duplicados marcados
        $duplicates = DB::select("SELECT id, tenant_id, username FROM players WHERE username LIKE '%_dup_%'");
        if (!empty($duplicates)) {
            echo "\n⚠️  USUARIOS CON DUPLICADOS (revisar manualmente):\n";
            foreach ($duplicates as $dup) {
                echo "ID: {$dup->id}, Tenant: {$dup->tenant_id}, Username: {$dup->username}\n";
            }
        }
    }

    public function down(): void
    {
        DB::statement('DROP INDEX players_tenant_username_unique');
        
        Schema::table('players', function (Blueprint $table) {
            $table->unique(['tenant_id', 'username'], 'players_tenant_username_unique');
        });
    }
};