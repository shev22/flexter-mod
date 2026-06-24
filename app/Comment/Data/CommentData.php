<?php

namespace App\Comment\Data;

use App\Comment\Models\Comment;
use App\Models\User;

final class CommentData
{
    /**
     * @param  list<CommentData>  $replies
     */
    public function __construct(
        public int $id,
        public string $body,
        public bool $is_spoiler,
        public ?string $edited_at,
        public string $created_at,
        public string $created_at_human,
        public array $user,
        public int $likes_count,
        public bool $liked_by_me,
        public bool $can_edit,
        public bool $can_delete,
        public ?array $reply_to,
        public array $replies = [],
    ) {}

    public static function fromModel(
        Comment $comment,
        ?User $viewer = null,
        int $likesCount = 0,
        bool $likedByMe = false,
        array $replies = [],
        ?Comment $parentComment = null,
    ): self {
        $isOwner = $viewer && $comment->user_id === $viewer->id;
        $isAdmin = $viewer?->isAdmin() ?? false;

        return new self(
            id: $comment->id,
            body: $comment->body,
            is_spoiler: (bool) $comment->is_spoiler,
            edited_at: $comment->edited_at?->toIso8601String(),
            created_at: $comment->created_at->toIso8601String(),
            created_at_human: $comment->created_at->diffForHumans(),
            user: [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'initials' => self::initials($comment->user->name),
            ],
            likes_count: $likesCount,
            liked_by_me: $likedByMe,
            can_edit: $isOwner,
            can_delete: $isOwner || $isAdmin,
            reply_to: $parentComment ? [
                'id' => $parentComment->id,
                'name' => $parentComment->user->name,
            ] : null,
            replies: $replies,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'is_spoiler' => $this->is_spoiler,
            'edited_at' => $this->edited_at,
            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at_human,
            'user' => $this->user,
            'likes_count' => $this->likes_count,
            'liked_by_me' => $this->liked_by_me,
            'can_edit' => $this->can_edit,
            'can_delete' => $this->can_delete,
            'reply_to' => $this->reply_to,
            'replies' => array_map(fn (self $r) => $r->toArray(), $this->replies),
        ];
    }

    private static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];

        return strtoupper(collect($parts)->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
    }
}
