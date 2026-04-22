<?php
// User service - handles profile and password business logic
// called by UserController, follows same pattern as AuthService / NoteService / TagService

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /* PUBLIC METHOD */
    /* getProfile */
    public function getProfile(User $user): User
    {
        return $user;
    }

    /* PUBLIC METHOD */
    /* updateProfile */
    public function updateProfile(User $user, array $payload): User
    {
        // reset email verification if email changed
        if ($user->email !== $payload['email']) {
            $user->email_verified_at = null;
        }

        $user->fill($payload)->save();

        return $user;
    }

    /* PUBLIC METHOD */
    /* updatePassword */
    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update(['password' => Hash::make($newPassword)]);
    }
}
