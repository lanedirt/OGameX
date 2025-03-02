<?php

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use OGame\Exceptions\Handler;
use OGame\Http\Middleware\Admin;
use OGame\Http\Middleware\GlobalGame;
use OGame\Http\Middleware\Locale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Custom Middleware
        $middleware->alias([
            'globalgame' => GlobalGame::class,
            'locale' => Locale::class,
            'admin' => Admin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        app()->singleton(ExceptionHandlerContract::class, Handler::class);
    })
    ->create();
