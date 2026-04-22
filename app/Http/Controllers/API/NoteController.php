<?php
// Note controller - list, create, delete endpoints
// FormRequest handles validation (422 auto), delegates to NoteService, returns standard JSON

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreNoteRequest;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// DONE: Implemented notes list/create/delete with FormRequest validation, ownership, and HTTP codes.

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
    public function store(StoreNoteRequest $request): JsonResponse
    {
        // FormRequest validated before reaching here - 422 auto-thrown if invalid
        // service throws ModelNotFoundException (-> 404) or AuthorizationException (-> 403)
        $note = $this->noteService->create($request->validated(), $request->user()->id);

        // 201 created on success
        return $this->success('Note created successfully.', $note, 201);
    }

    /* PUBLIC METHOD */
    /* destroy */
    public function destroy(Request $request, int $note): JsonResponse
    {
        // service throws ModelNotFoundException (-> 404) or AuthorizationException (-> 403)
        $this->noteService->delete($note, $request->user()->id);

        return $this->success('Note deleted successfully.', null, 200);
    }
}
