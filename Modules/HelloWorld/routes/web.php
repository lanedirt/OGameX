<?php

use Illuminate\Support\Facades\Route;
use Modules\HelloWorld\Http\Controllers\HelloWorldController;

Route::middleware(['auth', 'banned', 'globalgame', 'locale', 'firstlogin', 'admin'])
    ->prefix('admin/hello-world')
    ->name('helloworld.')
    ->group(function (): void {
        Route::get('/', [HelloWorldController::class, 'index'])->name('index');
    });
