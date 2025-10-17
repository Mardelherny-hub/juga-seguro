<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎰 Creando tenant de demostración...');

        // Crear tenant de demostración
        $tenant = Tenant::create([
            'name' => 'Casino Royal Demo',
            'slug' => 'demo',
            'domain' => 'demo.casinoredes.test',
            'primary_color' => '#DC2626',
            'secondary_color' => '#F59E0B',
            'whatsapp_number' => '+5492233123456',
            'bank_accounts' => [
                [
                    'bank' => 'Banco Nación',
                    'account_type' => 'Cuenta Corriente',
                    'account_number' => '1234567890',
                    'cbu' => '0110599520000012345678',
                    'alias' => 'CASINO.ROYAL.DEMO',
                ],
            ],
            'is_active' => true,
        ]);

        // Cargar tenant en el contenedor
        app()->instance('tenant', $tenant);

        $this->command->info('✓ Tenant creado: ' . $tenant->name);

        // Crear usuario admin
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Administrador Demo',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->command->info('✓ Admin creado: ' . $admin->email);

        // Crear operador
        $operator = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Operador Demo',
            'email' => 'operador@demo.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'is_active' => true,
        ]);

        $this->command->info('✓ Operador creado: ' . $operator->email);

        // Crear jugadores de prueba
        $players = [
            [
                'name' => 'Juan Pérez',
                'phone' => '+5492233111111',
                'email' => 'juan@example.com',
                'balance' => 5000.00,
                'status' => 'active',
            ],
            [
                'name' => 'María García',
                'phone' => '+5492233222222',
                'email' => 'maria@example.com',
                'balance' => 12500.50,
                'status' => 'active',
            ],
            [
                'name' => 'Carlos Rodríguez',
                'phone' => '+5492233333333',
                'balance' => 750.25,
                'status' => 'active',
            ],
            [
                'name' => 'Ana Martínez',
                'phone' => '+5492233444444',
                'email' => 'ana@example.com',
                'balance' => 3200.00,
                'status' => 'active',
            ],
            [
                'name' => 'Luis Fernández',
                'phone' => '+5492233555555',
                'balance' => 0.00,
                'status' => 'active',
            ],
        ];

        $service = new TransactionService();
        $createdPlayers = [];

        foreach ($players as $playerData) {
            $balance = $playerData['balance'];
            unset($playerData['balance']);

            $player = Player::create([
                'tenant_id' => $tenant->id,
                ...$playerData,
            ]);

            $createdPlayers[] = $player;

            // Crear depósito inicial si tiene balance
            if ($balance > 0) {
                $service->processDeposit($player, $balance, null, $admin);
            }

            $this->command->info("✓ Jugador creado: {$player->name} (\${$player->fresh()->balance})");
        }

        // Crear algunas transacciones de ejemplo
        $this->command->info('');
        $this->command->info('💸 Creando transacciones de ejemplo...');

        // Transacción pendiente
        $service->processWithdrawal($createdPlayers[0], 500);
        $this->command->info('✓ Retiro pendiente creado');

        // Bono de bienvenida
        $service->grantBonus($createdPlayers[4], 500, 'welcome', 'Bono de bienvenida por registrarse');
        $this->command->info('✓ Bono de bienvenida otorgado');

        // Sistema de referidos
        $referrer = $createdPlayers[0];
        $referred = Player::create([
            'tenant_id' => $tenant->id,
            'name' => 'Pedro Referido',
            'phone' => '+5492233666666',
            'balance' => 0,
            'referred_by' => $referrer->id,
            'status' => 'active',
        ]);

        // Bono por referido
        $service->grantBonus($referrer, 200, 'referral', "Bono por referir a {$referred->name}");
        $this->command->info('✓ Sistema de referidos configurado');

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('✅ Tenant Demo creado exitosamente');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📧 Credenciales:');
        $this->command->info('   Admin: admin@demo.com | password');
        $this->command->info('   Operador: operador@demo.com | password');
        $this->command->info('');
        $this->command->info('🌐 URL: http://localhost:8000');
        $this->command->info('');
        $this->command->info('📊 Datos creados:');
        $this->command->info('   • 1 Tenant (Casino Royal Demo)');
        $this->command->info('   • 2 Usuarios (Admin + Operador)');
        $this->command->info('   • 6 Jugadores con saldos');
        $this->command->info('   • 8 Transacciones de ejemplo');
        $this->command->info('   • 2 Bonos otorgados');
        $this->command->info('   • 1 Referido con bono');
        $this->command->info('');
    }
}