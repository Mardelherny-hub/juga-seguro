<?php

use App\Livewire\SuperAdmin\Dashboard as SuperAdminDashboard;
use App\Livewire\SuperAdmin\Clients\Index as ClientsIndex;
use App\Livewire\SuperAdmin\Clients\Create as ClientsCreate;
use App\Livewire\SuperAdmin\Clients\Edit as ClientsEdit;
use App\Livewire\SuperAdmin\Clients\Show as ClientsShow;
use App\Livewire\SuperAdmin\AgentTransactionHistory;
use Illuminate\Support\Facades\Route;

Route::prefix('super-admin')
    ->name('super-admin.')
    ->middleware(['auth', 'super.admin'])
    ->group(function () {
        
        // Dashboard Super Admin
        Route::get('/dashboard', SuperAdminDashboard::class)->name('dashboard');
        Route::get('/', SuperAdminDashboard::class);

        // Gestión de Clientes (Tenants)
        Route::prefix('clients')->name('clients.')->group(function () {
            Route::get('/', ClientsIndex::class)->name('index');
            Route::get('/create', ClientsCreate::class)->name('create');
            Route::get('/{tenant}', ClientsShow::class)->name('show');  // NUEVO
            Route::get('/{tenant}/edit', ClientsEdit::class)->name('edit');
        });

        // Historial de transacciones de agentes - AGREGAR ESTAS LÍNEAS
        Route::get('/agents/{agent}/transactions', AgentTransactionHistory::class)
            ->name('agents.transactions');

        // Usuarios Super Admin
        Route::get('/admins', \App\Livewire\SuperAdmin\ManageAdmins::class)->name('admins');

        // Aquí irían más rutas de super admin en el futuro
        // Route::get('/reports', Reports::class)->name('reports');
        // Route::get('/settings', Settings::class)->name('settings');
    });