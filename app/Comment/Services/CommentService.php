<?php

namespace App\Comment\Services;

use App\Comment\Data\CommentData;
use App\Comment\Models\Comment;
use App\Comment\Models\CommentLike;
use App\Comment\Services\Interfaces\CommentServiceInterface;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CommentService implements CommentServiceInterface
{
    private const MAX_TOP_LEVEL = 50;

    public function forMedia(string $mediaType, int $mediaId, ?User $viewer = null, string $sort = 'newest'): array
    {
        $comments = Comment::query()
            ->with(['user:id,name'])
            ->withCount('likes')
            ->where('media_type', $mediaType)
            ->where('media_id', $mediaId)
            ->orderBy('created_at')
            ->get();

        $likedIds = $this->likedCommentIds($viewer, $comments);
        $byParent = $comments->groupBy(fn (Comment $c) => $c->parent_id ?? 0);
        $lookup = $comments->keyBy('id');

        $topLevel = $comments->whereNull('parent_id');

        if ($sort === 'top') {
            $topLevel = $topLevel->sortByDesc(fn (Comment $c) => [$c->likes_count, $c->created_at->timestamp]);
        } else {
            $topLevel = $topLevel->sortByDesc(fn (Comment $c) => $c->created_at->timestamp);
        }

        $threads = $topLevel
            ->take(self::MAX_TOP_LEVEL)
            ->map(fn (Comment $comment) => $this->buildNode(
                $comment,
                $byParent,
                $lookup,
                $viewer,
                $likedIds,
            )->toArray())
            ->values()
            ->all();

        return [
            'threads' => $threads,
            'total' => $comments->whereNull('parent_id')->count(),
        ];
    }

    public function store(User $user, array $data): Comment
    {
        $parent = null;

        if (! empty($data['parent_id'])) {
            $parent = Comment::query()->findOrFail($data['parent_id']);
            $this->assertSameMedia($parent, $data['media_type'], (int) $data['media_id']);
        }

        return Comment::create([
            'user_id' => $user->id,
            'media_type' => $data['media_type'],
            'media_id' => $data['media_id'],
            'parent_id' => $parent?->id,
            'body' => trim($data['body']),
            'is_spoiler' => (bool) ($data['is_spoiler'] ?? false),
        ])->load('user:id,name');
    }

    public function update(User $user, Comment $comment, array $data): Comment
    {
        if ($comment->user_id !== $user->id) {
            abort(403);
        }

        $comment->update([
            'body' => trim($data['body']),
            'is_spoiler' => (bool) ($data['is_spoiler'] ?? false),
            'edited_at' => now(),
        ]);

        return $comment->fresh(['user:id,name']);
    }

    public function delete(User $user, Comment $comment): void
    {
        if ($comment->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $comment->delete();
    }

    public function toggleLike(User $user, Comment $comment): array
    {
        $existing = CommentLike::query()
            ->where('user_id', $user->id)
            ->where('comment_id', $comment->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            CommentLike::create([
                'user_id' => $user->id,
                'comment_id' => $comment->id,
            ]);
            $liked = true;
        }

        return [
            'liked' => $liked,
            'likes_count' => $comment->likes()->count(),
        ];
    }

    /** @param  Collection<int|string, Collection<int, Comment>>  $byParent */
    private function buildNode(
        Comment $comment,
        Collection $byParent,
        Collection $lookup,
        ?User $viewer,
        Collection $likedIds,
    ): CommentData {
        $children = ($byParent->get($comment->id) ?? collect())
            ->sortBy(fn (Comment $c) => $c->created_at->timestamp)
            ->map(fn (Comment $child) => $this->buildNode(
                $child,
                $byParent,
                $lookup,
                $viewer,
                $likedIds,
            ))
            ->values()
            ->all();

        $parent = $comment->parent_id ? $lookup->get($comment->parent_id) : null;

        return CommentData::fromModel(
            $comment,
            $viewer,
            (int) $comment->likes_count,
            $likedIds->contains($comment->id),
            $children,
            $parent,
        );
    }

    /** @return Collection<int, int> */
    private function likedCommentIds(?User $viewer, Collection $comments): Collection
    {
        if (! $viewer || $comments->isEmpty()) {
            return collect();
        }

        return CommentLike::query()
            ->where('user_id', $viewer->id)
            ->whereIn('comment_id', $comments->pluck('id'))
            ->pluck('comment_id');
    }

    private function assertSameMedia(Comment $parent, string $mediaType, int $mediaId): void
    {
        if ($parent->media_type !== $mediaType || (int) $parent->media_id !== $mediaId) {
            throw ValidationException::withMessages([
                'parent_id' => ['This reply does not belong to the same title.'],
            ]);
        }
    }
}
