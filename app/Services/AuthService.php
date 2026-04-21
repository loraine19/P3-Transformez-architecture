<?php

namespace App\Services;

// DONE: Added auth service stubs used by AuthController.

class AuthService
{
    public function register(array $payload): array
    {
        return [
            'user' => [
                'name' => $payload['name'] ?? null,
                'email' => $payload['email'] ?? null,
            ],
            'token' => null,
        ];
    }

    public function login(array $payload): array
    {
        return [
            'email' => $payload['email'] ?? null,
            'token' => null,
        ];
    }

    public function logout(): bool
    {
        return true;
    }
}
