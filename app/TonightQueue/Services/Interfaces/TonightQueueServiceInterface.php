<?php

namespace App\TonightQueue\Services\Interfaces;

use App\Models\User;

interface TonightQueueServiceInterface
{
    /** @return array{items: array<int, array<string, mixed>>, started_at: int|null} */
    public function forUser(User $user): array;

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function mergeGuestQueue(User $user, array $items, ?int $startedAt = null): void;

    /**
     * @param  array<string, mixed>  $item
     * @return array<int, array<string, mixed>>
     */
    public function add(User $user, array $item): array;

    /** @return array<int, array<string, mixed>> */
    public function remove(User $user, string $type, int $id): array;

    /** @return array<int, array<string, mixed>> */
    public function clear(User $user): array;

    /**
     * @param  array<string, mixed>  $item
     * @return array{added: bool, items: array<int, array<string, mixed>>}
     */
    public function toggle(User $user, array $item): array;
}
