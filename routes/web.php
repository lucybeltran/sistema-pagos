<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BocaminaController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\TrabajoController;
use App\Http\Controllers\AnticipoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReporteController;

// Public routes / Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes (Only logged-in admin users)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [ReporteController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', function() {
        return redirect()->route('dashboard');
    });

    // Bocaminas
    Route::resource('bocaminas', BocaminaController::class)->only(['index', 'store', 'update', 'destroy']);

    // Trabajadores
    Route::resource('trabajadores', TrabajadorController::class)->only(['index', 'store', 'update', 'destroy']);

    // Contratos
    Route::resource('contratos', ContratoController::class);

    // Registro de Trabajos
    // Route::resource('trabajos', TrabajoController::class)->only(['index', 'store', 'update', 'destroy']);

    // Anticipos
    Route::get('/anticipos/{anticipo}/recibo', [App\Http\Controllers\AnticipoController::class, 'recibo'])->name('anticipos.recibo');
    Route::resource('anticipos', AnticipoController::class)->only(['index', 'store', 'update', 'destroy']);

    // Pagos
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/pagos/crear', [PagoController::class, 'create'])->name('pagos.create');
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
    Route::get('/pagos/trabajador-data/{id}', [PagoController::class, 'getTrabajadorData'])->name('pagos.trabajador-data');
    Route::get('/pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
});
