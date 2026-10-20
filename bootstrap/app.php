<?php

use App\Http\Middleware\EnsureUserType;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureUserType::class,
            'admin' => EnsureUserType::class.':admin',
            'supervisor' => EnsureUserType::class.':supervisor',
            'intern' => EnsureUserType::class.':intern',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
