<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use OGame\Http\Middleware\Admin;
use OGame\Http\Middleware\CheckFirstLogin;
use OGame\Http\Middleware\GlobalGame;
use OGame\Http\Middleware\Locale;
use OGame\Http\Middleware\ServerTiming;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(ServerTiming::class);
        $middleware->alias([
            'globalgame' => GlobalGame::class,
            'locale' => Locale::class,
            'admin' => Admin::class,
            'firstlogin' => CheckFirstLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })
    ->create();
