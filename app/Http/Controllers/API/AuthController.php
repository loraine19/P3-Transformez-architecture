<?php
// Auth controller - register, login, logout endpoints
// FormRequest handles validation (422 auto), delegates to AuthService, returns standard JSON

namespace App\Http\Controllers\API;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// DONE: Implemented register/login/logout with FormRequest validation and correct HTTP codes.

class AuthController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    /* PUBLIC METHOD */
    /* register */
    public function register(RegisterRequest $request): JsonResponse
    {
        // FormRequest validated before reaching here - 422 auto-thrown if invalid
        $data = $this->authService->register($request->validated());

        return $this->success('User registered successfully.', $data, 201);
    }

    /* PUBLIC METHOD */
    /* login */
    public function login(LoginRequest $request): JsonResponse
    {
        // FormRequest validated before reaching here - 422 auto-thrown if invalid
        // service throws AuthenticationException (-> 401) if credentials are wrong
        $data = $this->authService->login($request->validated());

        return $this->success('Login successful.', $data);
    }

    /* PUBLIC METHOD */
    /* logout */
    public function logout(Request $request): JsonResponse
    {
        // revoke the current Bearer token
        $this->authService->logout($request->user());

        return $this->success('Logout successful.', null, 200);
    }
}
