<?php

use Illuminate\Support\Facades\Route;

Route::get('/check-session', [App\Http\Controllers\Auth\LoginController::class, 'checkSession'])
    ->middleware('web');

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')
    ->group(function () {
        Route::view('/', 'welcome')->name('home');
    });

Route::middleware('guest')
    ->group(function () {
        Route::view('login', 'auth.login')->name('login');
        Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    });
