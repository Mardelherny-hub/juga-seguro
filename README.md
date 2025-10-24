# 🎰 Next Level - Sistema Multi-Tenant para Clientes

Sistema SaaS multi-tenant para gestión automatizada de clientes online con integración WhatsApp Business API.

## 🚀 Stack Tecnológico

- **Backend:** Laravel 12
- **Frontend:** Livewire 3 + Tailwind CSS + Alpine.js
- **Base de Datos:** PostgreSQL 15+
- **Cache/Queues:** Redis
- **Auth:** Laravel Breeze (Livewire stack)
- **Multi-Tenancy:** Columna tenant_id con Global Scopes

## 📋 Requisitos

- PHP 8.3+
- PostgreSQL 15+
- Redis 7+
- Composer 2+
- Node.js 20+ (LTS)
- NPM 10+

## 🛠️ Instalación

### 1. Clonar repositorio
```bash
git clone https://github.com/TU_USUARIO/gestion-redes.git
cd gestion-redes
```

### 2. Instalar dependencias
```bash
composer install
npm install
```

### 3. Instalar paquetes adicionales
```bash
composer require spatie/laravel-activitylog
composer require spatie/laravel-backup
composer require twilio/sdk

# Publicar configuraciones
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

### 4. Configurar entorno
```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con tus credenciales:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gestion_redes
DB_USERNAME=postgres
DB_PASSWORD=tu_password

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# WhatsApp/Twilio (opcional para desarrollo)
TWILIO_SID=tu_account_sid
TWILIO_AUTH_TOKEN=tu_auth_token
TWILIO_WHATSAPP_NUMBER=whatsapp:+14155238886
```

### 5. Migrar base de datos
```bash
php artisan migrate
```

### 6. Crear datos de prueba
```bash
php artisan db:seed --class=DemoTenantSeeder
```

### 7. Compilar assets
```bash
npm run build
# O para desarrollo:
npm run dev
```

### 8. Iniciar servidor
```bash
php artisan serve
```

Acceder a: http://localhost:8000

## 🔑 Credenciales de Prueba

- **Admin:** admin@demo.com | password
- **Operador:** operador@demo.com | password

## 📁 Estructura del Proyecto
```
gestion_redes/
├── app/
│   ├── Http/
│   │   └── Middleware/
│   │       └── IdentifyTenant.php
│   ├── Models/
│   │   ├── Tenant.php
│   │   ├── Player.php
│   │   ├── Transaction.php
│   │   ├── Bonus.php
│   │   └── User.php
│   ├── Services/
│   │   └── TransactionService.php
│   └── Traits/
│       └── BelongsToTenant.php
├── database/
│   ├── migrations/
│   │   ├── 2024_10_17_000001_create_tenants_table.php
│   │   ├── 2024_10_17_000002_add_tenant_to_users.php
│   │   ├── 2024_10_17_000003_create_players_table.php
│   │   ├── 2024_10_17_000004_create_transactions_table.php
│   │   ├── 2024_10_17_000005_create_bonuses_table.php
│   │   ├── 2024_10_17_000006_create_wheel_spins_table.php
│   │   └── 2024_10_17_000007_create_whatsapp_messages_table.php
│   └── seeders/
│       └── DemoTenantSeeder.php
└── routes/
    └── web.php
```

## 🎯 Funcionalidades Implementadas (Fase 1)

- ✅ Sistema multi-tenant con columna tenant_id
- ✅ Global Scopes automáticos
- ✅ Middleware de identificación de tenant
- ✅ Sistema de transacciones con database locks
- ✅ Gestión de jugadores con sistema de referidos
- ✅ Sistema de bonos
- ✅ Sistema de ruleta (estructura)
- ✅ Auditoría completa (Spatie Activity Log)
- ✅ Marca blanca (logo, colores personalizables)

## 🔄 Funcionalidades Pendientes (Próximas Fases)

- [ ] Panel de administración (Livewire)
- [ ] CRUD de jugadores con búsqueda
- [ ] Gestión de transacciones (aprobar/rechazar)
- [ ] Bot WhatsApp automatizado
- [ ] Sistema de autenticación multi-tenant
- [ ] Sistema de reportes y estadísticas
- [ ] Sistema de backups automáticos

## 🧪 Testing
```bash
# Probar sistema de transacciones
php artisan tinker

# Crear depósito
$player = App\Models\Player::first();
$user = App\Models\User::first();
$service = new App\Services\TransactionService();
$transaction = $service->processDeposit($player, 1000, null, $user);
$player->fresh()->balance; // Verificar saldo actualizado
```

## 👥 Equipo

- **Desarrollador:** Victor Alcalde
- **Cliente:** MGA
- **Plazo:** 12 días hábiles
- **Presupuesto:** $5,400 USD

## 📄 Licencia

Propietario - Todos los derechos reservados

## 📞 Contacto

- Email: alcaldevictor1@gmail.com
- Web: www.estudioalcalde.net.ar