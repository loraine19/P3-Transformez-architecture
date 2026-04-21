<?php
// Tag controller - list and create endpoints
// validates input, delegates to TagService, returns standard JSON

namespace App\Http\Controllers\API;

use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// DONE: Cleaned up dead auth checks (handled by middleware), added validation pattern.

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
    public function store(Request $request): JsonResponse
    {
        // validate input - 422 if missing or malformed
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $tag = $this->tagService->create($validator->validated(), $request->user()->id);

        // 201 created on success
        return $this->success('Tag created successfully.', $tag, 201);
    }
}
