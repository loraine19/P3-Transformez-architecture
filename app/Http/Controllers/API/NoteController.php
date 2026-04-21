<?php

namespace App\Http\Controllers\API;

use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// DONE: Added notes API stub actions (list, create, delete).

class NoteController extends BaseApiController
{
    public function __construct(private readonly NoteService $noteService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $notes = $this->noteService->listForUser($request->user()?->id);

        return $this->success('Notes list stub ready.', $notes);
    }

    public function store(Request $request): JsonResponse
    {
        $note = $this->noteService->create($request->all(), $request->user()?->id);

        return $this->success('Note creation stub ready.', $note, 201);
    }

    public function destroy(Request $request, int $note): JsonResponse
    {
        $this->noteService->delete($note, $request->user()?->id);

        return $this->success('Note deletion stub ready.', null, 200);
    }
}
