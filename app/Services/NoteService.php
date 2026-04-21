<?php

namespace App\Services;

// DONE: Added notes service stubs used by NoteController.

class NoteService
{
    public function listForUser(?int $userId = null): array
    {
        return [];
    }

    public function create(array $payload, ?int $userId = null): array
    {
        return [
            'title' => $payload['title'] ?? null,
            'content' => $payload['content'] ?? null,
        ];
    }

    public function delete(int $noteId, ?int $userId = null): bool
    {
        return true;
    }
}
