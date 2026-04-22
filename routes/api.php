<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

// DONE: Protected routes with auth:sanctum middleware - public: register/login, protected: logout/notes/tags.

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Register API routes for the application here.
|
*/

Route::prefix('v1')->group(function (): void {
    // public routes - no token required
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // protected routes - valid Bearer token required, 401 if missing or invalid
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/notes', [NoteController::class, 'index']);
        Route::post('/notes', [NoteController::class, 'store']);
        Route::delete('/notes/{note}', [NoteController::class, 'destroy']);

        Route::get('/tags', [TagController::class, 'index']);
        Route::post('/tags', [TagController::class, 'store']);

        Route::get('/user', [UserController::class, 'show']);
        Route::put('/user/profile', [UserController::class, 'updateProfile']);
        Route::put('/user/password', [UserController::class, 'updatePassword']);
    });
});
