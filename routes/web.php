<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Ruta de bienvenida (pública)
Route::get('/', function () {
    return redirect('/login');
});

// Rutas de autenticación
require __DIR__.'/auth.php';

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard principal (ahora mostrará las reservas)
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    
    // Perfil de usuario (existente)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // CRUD de Reservas
    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/', [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::get('/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
        Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    });
    
    // Rutas para admin (si las necesitas)
    Route::middleware('can:admin')->group(function () {
        // Aquí puedes agregar rutas específicas para administradores
    });
});