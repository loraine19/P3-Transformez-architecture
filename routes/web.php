<?php

use Illuminate\Support\Facades\Route;

/* API ONLY — no web routes */
Route::get('/', fn () => response()->json([
    'status'  => 'success',
    'message' => 'Renote API — see /api/v1/*',
    'data'    => null,
]));
