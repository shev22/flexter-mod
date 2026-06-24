<?php

namespace App\Comment\Services\Interfaces;

use App\Comment\Models\Comment;
use App\Models\User;

interface CommentServiceInterface
{
    /** @return array{threads: list<array<string, mixed>>, total: int} */
    public function forMedia(string $mediaType, int $mediaId, ?User $viewer = null, string $sort = 'newest'): array;

    /** @param  array{media_type: string, media_id: int, body: string, is_spoiler?: bool, parent_id?: int|null}  $data */
    public function store(User $user, array $data): Comment;

    /** @param  array{body: string, is_spoiler?: bool}  $data */
    public function update(User $user, Comment $comment, array $data): Comment;

    public function delete(User $user, Comment $comment): void;

    /** @return array{liked: bool, likes_count: int} */
    public function toggleLike(User $user, Comment $comment): array;

    public function flag(Comment $comment): void;

    public function unflag(Comment $comment): void;

    public function block(Comment $comment): void;

    public function unblock(Comment $comment): void;

    public function adminDelete(Comment $comment): void;
}
