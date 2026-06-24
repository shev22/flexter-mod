<?php

namespace App\Jobs;

use App\Models\TmdbApiActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LogTmdbApiActivity implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(public array $payload) {}

    public function handle(): void
    {
        TmdbApiActivity::query()->create($this->payload);
    }
}
