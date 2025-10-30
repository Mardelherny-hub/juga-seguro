<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar nuevos valores al check constraint
        DB::statement("
            ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_type_check;
        ");
        
        DB::statement("
            ALTER TABLE transactions ADD CONSTRAINT transactions_type_check 
            CHECK (type IN ('deposit', 'withdrawal', 'bonus', 'bet_win', 'bet_loss', 'referral_bonus', 'account_creation', 'account_unlock', 'password_reset'))
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_type_check;
        ");
        
        DB::statement("
            ALTER TABLE transactions ADD CONSTRAINT transactions_type_check 
            CHECK (type IN ('deposit', 'withdrawal', 'bonus', 'bet_win', 'bet_loss', 'referral_bonus'))
        ");
    }
};