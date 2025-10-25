<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Player\Dashboard;
use Illuminate\Support\Facades\Route;

// Rutas PÚBLICAS del jugador (sin autenticación, pero con tenant)
Route::middleware(['tenant.identify'])->group(function () {
    
    Route::middleware('guest:player')->group(function () {
        Route::get('/player/login', Login::class)->name('player.login');
        Route::get('/register', Register::class)->name('player.register');
    });
});

// Rutas PROTEGIDAS del jugador (requieren autenticación + tenant)
Route::prefix('player')
    ->name('player.')
    ->middleware(['auth.player', 'tenant.identify'])
    ->group(function () {
        
        // Dashboard del Jugador
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        Route::get('/', Dashboard::class);

        // Perfil
        Route::view('/profile', 'player.profile')->name('profile');

        // Transacciones
        Route::get('/transactions', \App\Livewire\Player\Transactions::class)->name('transactions');

        // Bonos
        Route::view('/bonuses', 'player.bonuses')->name('bonuses');

        // Referidos
        Route::view('/referrals', 'player.referrals')->name('referrals');

        //Route::get('/chat', fn() => view('player.chat'))->name('chat');

        // Logout
        Route::post('/logout', function () {
            auth()->guard('player')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('player.login');
        })->name('logout');
    });