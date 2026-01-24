<?php

use App\Livewire\Agent\Dashboard;
//use App\Livewire\Players\Index as PlayersIndex;
//use App\Livewire\Players\Show as PlayersShow;
//use App\Livewire\Transactions\Index as TransactionsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant.identify'])->group(function () {

    // Dashboard del Agente (todos pueden acceder)
    Route::view('/', 'agent.dashboard')->name('dashboard');
    Route::view('/dashboard', 'agent.dashboard');

    // Gestión de Jugadores (todos pueden acceder)
    Route::view('/dashboard/players', 'agent.players.index')->name('dashboard.players');

    //profile (todos pueden acceder)
    Route::view('/profile', 'agent.profile')->name('profile');


    // Gestión de Transacciones (todos pueden acceder)
    Route::view('/dashboard/transactions/pending', 'agent.transactions.pending')->name('dashboard.transactions.pending');
    Route::view('/dashboard/transactions/history', 'agent.transactions.history')->name('dashboard.transactions.history');
    Route::view('/dashboard/transactions/monitor', 'agent.transactions.monitor')->name('dashboard.transactions.monitor');

    // Route panel de mensajes del agente
    Route::get('/messages', fn() => view('agent.messages'))->name('messages');

    // Push Notifications
    Route::post('/push/subscribe', [App\Http\Controllers\PushSubscriptionController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [App\Http\Controllers\PushSubscriptionController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::get('/push/vapid-key', [App\Http\Controllers\PushSubscriptionController::class, 'getVapidPublicKey'])->name('push.vapid');
    

     // RUTAS SOLO PARA ADMIN
    Route::middleware(['admin.only'])->group(function () {
        // Gestión de Bonos (solo admin)
        Route::get('/bonuses', fn() => view('agent.bonuses'))->name('bonuses');

        // Gestión de Cuentas Bancarias (solo admin)
        Route::view('/dashboard/bank-accounts', 'agent.bank-accounts.index')
            ->name('dashboard.bank-accounts');

        // Settings/Configuración (solo admin)
        Route::view('/settings', 'agent.settings')->name('settings');

        // Configuración de Ruleta (solo admin)
        Route::view('/wheel-config', 'agent.wheel-config')->name('wheel-config');
    });
}); 