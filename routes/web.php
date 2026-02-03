<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;

// ---------------------------
// RUTAS DE AUTENTICACIÓN
// ---------------------------

// Mostrar formulario de registro
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
// Procesar registro
Route::post('/register', [AuthController::class, 'register']);

// Mostrar formulario de login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
// Procesar login
Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('authsession');

// ---------------------------
// HOME = LISTADO DE MENSAJES
// ---------------------------
Route::get('/', [MessageController::class, 'index'])
    ->middleware('authsession')
    ->name('home');

// ---------------------------
// MENSAJES
// ---------------------------
Route::get('/messages/new', [MessageController::class, 'showNewForm'])
    ->middleware('authsession');

Route::post('/messages', [MessageController::class, 'create'])
    ->middleware('authsession');

Route::post('/messages/{id}/delete', [MessageController::class, 'delete'])
    ->middleware('authsession');

// ---------------------------
// MODERACIÓN (solo profesores)
// ---------------------------
Route::get('/moderation', [MessageController::class, 'moderation'])
    ->middleware(['authsession', 'role:profesor']);

Route::post('/moderation/{id}/approve', [MessageController::class, 'approve'])
    ->middleware(['authsession', 'role:profesor']);

Route::post('/moderation/{id}/reject', [MessageController::class, 'reject'])
    ->middleware(['authsession', 'role:profesor']);

Route::get('/moderation/invalid', [MessageController::class, 'invalid'])
    ->name('moderation.invalid')
    ->middleware(['authsession', 'role:profesor']);

// ---------------------------
// COLOR DE TEMA
// ---------------------------
Route::get('/theme/toggle', [App\Http\Controllers\ThemeController::class, 'toggle'])
    ->middleware('authsession')
    ->name('theme.toggle');