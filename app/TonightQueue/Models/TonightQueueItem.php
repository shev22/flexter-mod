<?php

namespace App\TonightQueue\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TonightQueueItem extends Model
{
    protected $fillable = [
        'user_id',
        'media_type',
        'media_id',
        'title',
        'poster_path',
        'position',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
