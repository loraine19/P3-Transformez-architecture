<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\TagController;
use Illuminate\Support\Facades\Route;

// DONE: Added v1 API stub endpoints for auth, notes, and tags.

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Register API routes for the application here.
|
*/

Route::prefix('v1')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/notes', [NoteController::class, 'index']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);

    Route::get('/tags', [TagController::class, 'index']);
    Route::post('/tags', [TagController::class, 'store']);
});
