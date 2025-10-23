<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

// Login CON tenant (para admins de clientes desde subdominio)
Route::middleware(['guest', 'tenant.identify'])->group(function () {
    Route::get('login', Login::class)->name('login');
    // Aquí irían register, forgot-password, etc. si las necesitas
});

Route::middleware('auth')->group(function () {
    Route::post('logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});