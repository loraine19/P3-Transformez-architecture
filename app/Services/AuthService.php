<?php
// Auth service - handles register, login, logout business logic
// called by AuthController, returns data arrays or null on failure

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// DONE: Implemented real register/login/logout logic with Sanctum tokens.

class AuthService
{
    /* PUBLIC METHOD */
    /* register */
    public function register(array $payload): array
    {
        // create user - model cast handles password hashing automatically
        $user = User::create([
            'name'     => $payload['name'],
            'email'    => $payload['email'],
            'password' => $payload['password'],
        ]);

        // issue a Bearer token for the new user
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user'  => ['name' => $user->name, 'email' => $user->email],
            'token' => $token,
        ];
    }

    /* PUBLIC METHOD */
    /* login */
    public function login(array $payload): ?array
    {
        // check credentials - returns null if invalid
        if (!Auth::attempt(['email' => $payload['email'], 'password' => $payload['password']])) {
            return null;
        }

        $user = Auth::user();

        // revoke old tokens before issuing a new one - one active session at a time
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user'  => ['name' => $user->name, 'email' => $user->email],
            'token' => $token,
        ];
    }

    /* PUBLIC METHOD */
    /* logout */
    public function logout(User $user): void
    {
        // revoke the current token used for this request
        $user->currentAccessToken()->delete();
    }
}
