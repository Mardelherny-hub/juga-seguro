<?php

use App\Livewire\Dashboard;
//use App\Livewire\Players\Index as PlayersIndex;
//use App\Livewire\Players\Show as PlayersShow;
//use App\Livewire\Transactions\Index as TransactionsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant.identify'])->group(function () {
    
    // Dashboard del Cliente
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class);

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