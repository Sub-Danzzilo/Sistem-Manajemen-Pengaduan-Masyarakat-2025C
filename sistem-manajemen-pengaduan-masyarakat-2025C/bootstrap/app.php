<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleAccountRoleUrl::class,
            \App\Http\Middleware\DynamicSessionTimeout::class,
        ]);
        $middleware->alias([
            'role' => EnsureUserRole::class,
            'account_role' => \App\Http\Middleware\HandleAccountRoleUrl::class,
            'session_timeout' => \App\Http\Middleware\DynamicSessionTimeout::class,
        ]);
        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard',
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
