<?php

namespace App\Services;

use App\Models\Tag;

// DONE: Added user-scoped tag logic used by TagController.

class TagService
{
    public function listForUser(?int $userId = null): array
    {
        if (empty($userId)) {
            return [];
        }

        return Tag::query()
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get(['id', 'name', 'user_id'])
            ->toArray();
    }

    public function create(array $payload, ?int $userId = null): array
    {
        if (empty($userId)) {
            return [];
        }

        $tag = Tag::query()->create([
            'user_id' => $userId,
            'name' => $payload['name'] ?? '',
        ]);

        return $tag->only(['id', 'name', 'user_id']);
    }
}
