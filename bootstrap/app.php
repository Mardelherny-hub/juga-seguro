<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IdentifyTenant;
use App\Http\Middleware\SuperAdmin;
use App\Http\Middleware\AuthenticatePlayer;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // NO aplicar globalmente, solo registrar el alias
        $middleware->alias([
            'tenant.identify' => IdentifyTenant::class,
            'super.admin' => SuperAdmin::class,
            'auth.player' => AuthenticatePlayer::class,
            'admin.only' => \App\Http\Middleware\CheckAdminRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();