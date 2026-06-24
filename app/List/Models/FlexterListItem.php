<?php

namespace App\List\Models;

use App\Movie\Models\Movie;
use App\Tv\Models\Tv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlexterListItem extends Model
{
    protected $fillable = [
        'flexter_list_id',
        'media_type',
        'media_id',
        'sort_order',
    ];

    public function list(): BelongsTo
    {
        return $this->belongsTo(FlexterList::class, 'flexter_list_id');
    }

    public function resolveMedia(): Movie|Tv|null
    {
        return match ($this->media_type) {
            'tv' => Tv::query()->find($this->media_id),
            default => Movie::query()->find($this->media_id),
        };
    }
}
