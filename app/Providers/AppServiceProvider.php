<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Transaction;
use App\Models\Player;
use App\Observers\TransactionObserver;
use App\Observers\PlayerObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observers
        Transaction::observe(TransactionObserver::class);
        Player::observe(PlayerObserver::class);
    }
}