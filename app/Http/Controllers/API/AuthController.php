<?php

namespace App\Http\Controllers\API;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// DONE: Added auth API stub actions (register, login, logout).

class AuthController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    /* REGISTER */
    public function register(Request $request): JsonResponse
    {
         $data = $this->authService->register($request->all());

        return $this->success('Register stub ready.', $data, 201);
    }

    /* LOGIN */
    public function login(Request $request): JsonResponse
    {
        $data = $this->authService->login($request->all());

        return $this->success('Login stub ready.', $data);
    }

    /* LOGOUT */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->success('Logout stub ready.', null, 200);
    }
}
