<?php

use Illuminate\Support\Facades\Route;

Route::prefix('contabil')->group(function () {
    Route::get('/dashboard', fn() => 'Dashboard Client');
    Route::get('/profile', fn() => 'Profile Client');
});