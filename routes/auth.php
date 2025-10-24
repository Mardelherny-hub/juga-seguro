<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

// Login - aplica middleware tenant.identify
Route::middleware(['guest', 'tenant.identify'])->group(function () {
    Route::get('login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});