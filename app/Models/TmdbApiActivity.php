<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $operation
 * @property string|null $media_type
 * @property string|null $category
 * @property int $request_count
 * @property int $items_fetched
 * @property string $source
 * @property string $status
 * @property string|null $error_message
 * @property array<string, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon $created_at
 */
class TmdbApiActivity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'operation',
        'media_type',
        'category',
        'request_count',
        'items_fetched',
        'source',
        'status',
        'error_message',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
