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
    
    // Página de descarga (pública)
    Route::get('/descargar', function () {
        return view('player.descargar');
    })->name('player.descargar');
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

        // Cuentas de Retiro - NUEVA RUTA
        Route::view('/withdrawal-accounts', 'player.withdrawal-accounts')->name('withdrawal-accounts');
        
        // Ruleta de la Suerte
        Route::get('/wheel', fn() => view('player.wheel'))->name('wheel');

        // Logout
        Route::post('/logout', function () {
            auth()->guard('player')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('player.login');
        })->name('logout');

        // Push Notifications
        Route::post('/push/subscribe', [App\Http\Controllers\PushSubscriptionController::class, 'subscribe'])->name('player.push.subscribe');
        Route::post('/push/unsubscribe', [App\Http\Controllers\PushSubscriptionController::class, 'unsubscribe'])->name('player.push.unsubscribe');
        Route::get('/push/vapid-key', [App\Http\Controllers\PushSubscriptionController::class, 'getVapidPublicKey'])->name('player.push.vapid');
        
    });