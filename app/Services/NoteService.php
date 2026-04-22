<?php
// Note service - list, create, delete notes scoped to authenticated user
// ownership enforced on delete - 403 if user does not own the note

namespace App\Services;

use App\Models\Note;
use Illuminate\Auth\Access\AuthorizationException;

// DONE: Implemented real note logic with user ownership enforcement.

class NoteService
{
    /* PUBLIC METHOD */
    /* listForUser */
    public function listForUser(int $userId): array
    {
        // fetch only notes belonging to this user - eager load tag to avoid N+1
        return Note::query()
            ->where('user_id', $userId)
            ->with('tag')
            ->orderByDesc('created_at')
            ->get(['id', 'user_id', 'tag_id', 'text', 'created_at'])
            ->toArray();
    }

    /* PUBLIC METHOD */
    /* create */
    public function create(array $payload, int $userId): array
    {
        // attach user_id automatically - user cannot set it manually
        $note = Note::create([
            'user_id' => $userId,
            'tag_id'  => $payload['tag_id'],
            'text'    => $payload['text'],
        ]);

        return $note->only(['id', 'user_id', 'tag_id', 'text', 'created_at']);
    }

    /* PUBLIC METHOD */
    /* delete */
    public function delete(int $noteId, int $userId): void
    {
        // 404 if note does not exist
        $note = Note::query()->findOrFail($noteId);

        // 403 if note belongs to another user - ownership check
        if ($note->user_id !== $userId) {
            throw new AuthorizationException('You do not own this note.');
        }

        $note->delete();
    }
}
