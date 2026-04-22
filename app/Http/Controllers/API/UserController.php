<?php
// UserController - profile and password endpoints
// FormRequest handles validation (422 auto), UserService handles business logic

namespace App\Http\Controllers\API;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    public function __construct(private UserService $userService) {}

    /* GET /api/v1/user */
    public function show(Request $request): JsonResponse
    {
        return $this->success('User profile retrieved.', $this->userService->getProfile($request->user()));
    }

    /* PUT /api/v1/user/profile */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfile($request->user(), $request->validated());

        return $this->success('Profile updated successfully.', $user);
    }

    /* PUT /api/v1/user/password */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $this->userService->updatePassword($request->user(), $request->validated()['password']);

        return $this->success('Password updated successfully.');
    }
}
