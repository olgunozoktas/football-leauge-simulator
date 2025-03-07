<?php

use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::get('/dashboard', [SimulationController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


require __DIR__ . '/auth.php';
