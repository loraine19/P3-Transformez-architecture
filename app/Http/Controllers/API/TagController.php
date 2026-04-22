<?php
// Tag controller - list and create endpoints
// FormRequest handles validation (422 auto), delegates to TagService, returns standard JSON

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreTagRequest;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// DONE: Cleaned up dead auth checks (handled by middleware), FormRequest validation pattern.

class TagController extends BaseApiController
{
    public function __construct(private readonly TagService $tagService)
    {
    }

    /* PUBLIC METHOD */
    /* index */
    public function index(Request $request): JsonResponse
    {
        // list all tags belonging to the authenticated user
        $tags = $this->tagService->listForUser($request->user()->id);

        return $this->success('Tags fetched successfully.', $tags);
    }

    /* PUBLIC METHOD */
    /* store */
    public function store(StoreTagRequest $request): JsonResponse
    {
        // FormRequest validated before reaching here - 422 auto-thrown if invalid
        $tag = $this->tagService->create($request->validated(), $request->user()->id);

        // 201 created on success
        return $this->success('Tag created successfully.', $tag, 201);
    }
}
