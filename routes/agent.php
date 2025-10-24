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
    Route::view('/profile', 'profile')->name('profile');


    // Gestión de Jugadores
    /*Route::get('/players', PlayersIndex::class)->name('players.index');
    Route::get('/players/{player}', PlayersShow::class)->name('players.show');

    // Gestión de Transacciones
    Route::get('/transactions', TransactionsIndex::class)->name('transactions.index');

    // Profile (implementaremos después)
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile'); */
}); 