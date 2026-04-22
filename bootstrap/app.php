<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // DONE: API route file is now loaded by the app bootstrap.
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // DONE: API rate limit middleware group: enables throttle:api.
        $middleware->throttleApi();

        // DONE: CORS configured via config/cors.php - origins from .env CORS_ALLOWED_ORIGINS
        // change .env only when connecting the front - no code change needed.

        // Keep Sanctum token mode for now (Bearer token).
        // If we switch to cookie-based SPA auth later, enable:
        // $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // DONE: API exceptions are now handled by the app bootstrap.
        /* 422 VALIDATION */
        $exceptions->render(function (ValidationException $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'data' => $e->errors(),
            ], 422);
        });

        /* 401 UNAUTHENTICATED */
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Unauthenticated.',
                'data' => null,
            ], 401);
        });

        /* 403 FORBIDDEN */
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Forbidden.',
                'data' => null,
            ], 403);
        });

        /* 404 NOT FOUND - MODEL */
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Resource not found.',
                'data' => null,
            ], 404);
        });

        /* 404 NOT FOUND - ROUTE */
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Route not found.',
                'data' => null,
            ], 404);
        });

        /* 500 SERVER ERROR - DEBUG */
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            $isDebug = (bool) config('app.debug');

            return response()->json([
                'status' => 'error',
                'message' => 'Server error.',
                'data' => $isDebug ? ['exception' => $e->getMessage()] : null,
            ], 500);
        });
    })->create();
