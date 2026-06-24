<?php

namespace App\List\Models;

use App\Shared\Support\AppCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlexterList extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'icon',
        'genre_ids',
        'media_type',
        'item_limit',
        'min_rating',
        'min_year',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'genre_ids' => 'array',
            'item_limit' => 'integer',
            'min_rating' => 'float',
            'min_year' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(FlexterListItem::class)->orderBy('sort_order');
    }

    protected static function booted(): void
    {
        $bust = fn () => AppCache::bustLists();

        static::saved($bust);
        static::deleted($bust);
    }
}
