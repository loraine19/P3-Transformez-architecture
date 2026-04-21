<?php
// Auth controller - register, login, logout endpoints
// validates input, delegates to AuthService, returns standard JSON

namespace App\Http\Controllers\API;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// DONE: Implemented register/login/logout with validation and correct HTTP codes.

class AuthController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    /* PUBLIC METHOD */
    /* register */
    public function register(Request $request): JsonResponse
    {
        // validate input - 422 if missing or malformed
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $data = $this->authService->register($validator->validated());

        // 201 created on success
        return $this->success('User registered successfully.', $data, 201);
    }

    /* PUBLIC METHOD */
    /* login */
    public function login(Request $request): JsonResponse
    {
        // validate input - 422 if missing fields
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $data = $this->authService->login($validator->validated());

        // 401 if credentials invalid
        if ($data === null) {
            return $this->error('Invalid credentials.', null, 401);
        }

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
