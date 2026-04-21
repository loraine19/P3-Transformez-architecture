<?php
// Note controller - list, create, delete endpoints
// validates input, delegates to NoteService, returns standard JSON

namespace App\Http\Controllers\API;

use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// DONE: Implemented notes list/create/delete with validation, ownership, and HTTP codes.

class NoteController extends BaseApiController
{
    public function __construct(private readonly NoteService $noteService)
    {
    }

    /* PUBLIC METHOD */
    /* index */
    public function index(Request $request): JsonResponse
    {
        // list all notes for the authenticated user
        $notes = $this->noteService->listForUser($request->user()->id);

        return $this->success('Notes fetched successfully.', $notes);
    }

    /* PUBLIC METHOD */
    /* store */
    public function store(Request $request): JsonResponse
    {
        // validate input - 422 if missing or malformed
        $validator = Validator::make($request->all(), [
            'text'   => 'required|string',
            'tag_id' => 'required|integer|exists:tags,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $note = $this->noteService->create($validator->validated(), $request->user()->id);

        // 201 created on success
        return $this->success('Note created successfully.', $note, 201);
    }

    /* PUBLIC METHOD */
    /* destroy */
    public function destroy(Request $request, int $note): JsonResponse
    {
        $result = $this->noteService->delete($note, $request->user()->id);

        // map service result to HTTP codes
        if ($result === 'not_found') {
            return $this->error('Note not found.', null, 404);
        }

        if ($result === 'forbidden') {
            return $this->error('You do not own this note.', null, 403);
        }

        return $this->success('Note deleted successfully.', null, 200);
    }
}
