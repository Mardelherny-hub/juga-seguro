<?php

use App\Livewire\Agent\Dashboard;
//use App\Livewire\Players\Index as PlayersIndex;
//use App\Livewire\Players\Show as PlayersShow;
//use App\Livewire\Transactions\Index as TransactionsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant.identify'])->group(function () {
    
   // Dashboard del Agente
    Route::view('/', 'agent.dashboard')->name('dashboard');
    Route::view('/dashboard', 'agent.dashboard');

    // Gestión de Jugadores
    Route::view('/dashboard/players', 'agent.players.index')->name('dashboard.players');

    //profile (temporal - reestructurar en próxima sesión)
    Route::view('/profile', 'agent.profile')->name('profile');


    // Gestión de Transacciones
    Route::view('/dashboard/transactions/pending', 'agent.transactions.pending')->name('dashboard.transactions.pending');
    Route::view('/dashboard/transactions/history', 'agent.transactions.history')->name('dashboard.transactions.history');
    Route::view('/dashboard/transactions/monitor', 'agent.transactions.monitor')->name('dashboard.transactions.monitor');

    // Route pane de mensajes del agente
    Route::get('/messages', fn() => view('agent.messages'))->name('messages');

    // Gestión de Bonos
    Route::get('/bonuses', fn() => view('agent.bonuses'))->name('bonuses');

    // Gestión de Cuentas Bancarias
    Route::view('/dashboard/bank-accounts', 'agent.bank-accounts.index')
    ->name('dashboard.bank-accounts');

    //settings
    Route::view('/settings', 'agent.settings')->name('settings');
}); 