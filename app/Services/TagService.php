<?php
// Tag service - list and create tags scoped to authenticated user
// called by TagController, returns data arrays

namespace App\Services;

use App\Models\Tag;

// DONE: Added user-scoped tag logic used by TagController.

class TagService
{
    /* PUBLIC METHOD */
    /* listForUser */
    public function listForUser(int $userId): array
    {
        return Tag::query()
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get(['id', 'name', 'user_id'])
            ->toArray();
    }

    /* PUBLIC METHOD */
    /* create */
    public function create(array $payload, int $userId): array
    {
        // name is guaranteed present - validated by StoreTagRequest before reaching here
        $tag = Tag::query()->create([
            'user_id' => $userId,
            'name'    => $payload['name'],
        ]);

        return $tag->only(['id', 'name', 'user_id']);
    }
}
