<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PartidaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'mostrarLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.validar');

Route::get('/registro', [AuthController::class, 'mostrarRegistro'])
    ->name('registro');

Route::post('/registro', [AuthController::class, 'registrar'])
    ->name('registro.guardar');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Panel del administrador
Route::middleware(['auth', 'rol:Administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/usuarios/crear', [AdminController::class, 'crearUsuario'])->name('usuarios.crear');
    Route::post('/usuarios', [AdminController::class, 'guardarUsuario'])->name('usuarios.guardar');
    Route::get('/usuarios/{usuario}/editar', [AdminController::class, 'editarUsuario'])->name('usuarios.editar');
    Route::put('/usuarios/{usuario}', [AdminController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
    Route::delete('/usuarios/{usuario}', [AdminController::class, 'eliminarUsuario'])->name('usuarios.eliminar');
    Route::get('/partidas/{partida}', [AdminController::class, 'verPartida'])->name('partidas.ver');
    Route::post('/partidas/{partida}/cancelar', [AdminController::class, 'cancelarPartida'])->name('partidas.cancelar');
});

Route::get('/partidas', [PartidaController::class, 'index'])
    ->middleware(['auth', 'rol:Jugador'])
    ->name('partidas.index');

Route::post('/partidas', [PartidaController::class, 'crear'])
    ->middleware(['auth', 'rol:Jugador'])
    ->name('partidas.crear');

Route::post('/partidas/{id}/unirse', [PartidaController::class, 'unirse'])
    ->middleware(['auth', 'rol:Jugador'])
    ->name('partidas.unirse');

Route::get('/partidas/{id}', [PartidaController::class, 'mostrar'])
    ->middleware(['auth', 'rol:Jugador'])
    ->name('partidas.mostrar');

Route::post('/partidas/{id}/mover', [PartidaController::class, 'mover'])
    ->middleware(['auth', 'rol:Jugador'])
    ->name('partidas.mover');

Route::post('/partidas/{id}/abandonar', [PartidaController::class, 'abandonar'])
    ->middleware(['auth', 'rol:Jugador'])
    ->name('partidas.abandonar');

Route::get('/partidas/{id}/estado', [PartidaController::class, 'estado'])
    ->middleware(['auth', 'rol:Jugador'])
    ->name('partidas.estado');