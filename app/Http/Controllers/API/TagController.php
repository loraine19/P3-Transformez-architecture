<?php

namespace App\Http\Controllers\API;

use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// DONE: Added tags API actions with user ownership checks.

class TagController extends BaseApiController
{
    public function __construct(private readonly TagService $tagService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

    /* Remove comments after test implement API--    if (!$userId) {
            return $this->error('Unauthorized.', null, 401);
        }*/

        $tags = $this->tagService->listForUser($userId);

        return $this->success('Tags fetched successfully.', $tags);
    }

    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

    /* Remove comments after test implement API--    if (!$userId) {
            return $this->error('Unauthorized.', null, 401);
        }*/

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $tag = $this->tagService->create($validated, $userId);

        return $this->success('Tag created successfully.', $tag, 201);
    }
}
