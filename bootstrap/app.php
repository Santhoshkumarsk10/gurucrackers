<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $trustedProxies = env('TRUSTED_PROXIES');
        if (!empty($trustedProxies)) {
            $middleware->trustProxies(at: array_map('trim', explode(',', $trustedProxies)));
        } else {
            $middleware->trustProxies(at: [
                '127.0.0.1',
                '::1',
            ]);
        }
        $middleware->redirectTo(
            guests: '/admin/login',
            users: '/admin/orders'
        );
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->web(append: [
            \App\Http\Middleware\TrackStoreVisit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
