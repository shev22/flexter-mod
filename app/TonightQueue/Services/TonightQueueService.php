<?php

namespace App\TonightQueue\Services;

use App\Models\User;
use App\Settings\Services\Interfaces\SettingsServiceInterface;
use App\TonightQueue\Models\TonightQueueItem;
use App\TonightQueue\Services\Interfaces\TonightQueueServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TonightQueueService implements TonightQueueServiceInterface
{
    private const TTL_HOURS = 24;

    public function __construct(
        private readonly SettingsServiceInterface $settingsService,
    ) {}

    public function forUser(User $user): array
    {
        $this->expireIfNeeded($user);

        $items = TonightQueueItem::query()
            ->where('user_id', $user->id)
            ->orderBy('position')
            ->orderBy('id')
            ->get()
            ->map(fn (TonightQueueItem $item) => $this->present($item))
            ->values()
            ->all();

        $setting = $this->settingsService->forUser($user);

        return [
            'items' => $items,
            'started_at' => $setting->tonight_queue_started_at?->getTimestamp() * 1000,
        ];
    }

    public function mergeGuestQueue(User $user, array $items, ?int $startedAt = null): void
    {
        if ($items === []) {
            return;
        }

        $existing = $this->forUser($user);

        if ($existing['items'] !== []) {
            return;
        }

        DB::transaction(function () use ($user, $items, $startedAt) {
            $setting = $this->settingsService->forUser($user);

            if ($setting->tonight_queue_started_at === null) {
                $setting->tonight_queue_started_at = $startedAt
                    ? Carbon::createFromTimestamp((int) ($startedAt / 1000))
                    : now();
                $setting->save();
            }

            foreach (array_values($items) as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }

                $type = (string) ($item['type'] ?? '');
                $id = (int) ($item['id'] ?? 0);

                if (! in_array($type, ['movie', 'tv'], true) || $id <= 0) {
                    continue;
                }

                TonightQueueItem::query()->firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'media_type' => $type,
                        'media_id' => $id,
                    ],
                    [
                        'title' => (string) ($item['title'] ?? 'Untitled'),
                        'poster_path' => $item['poster'] ?? null,
                        'position' => $index,
                    ],
                );
            }
        });
    }

    public function add(User $user, array $item): array
    {
        $type = (string) ($item['type'] ?? '');
        $id = (int) ($item['id'] ?? 0);

        if (! in_array($type, ['movie', 'tv'], true) || $id <= 0) {
            return $this->forUser($user)['items'];
        }

        $exists = TonightQueueItem::query()
            ->where('user_id', $user->id)
            ->where('media_type', $type)
            ->where('media_id', $id)
            ->exists();

        if ($exists) {
            return $this->forUser($user)['items'];
        }

        $setting = $this->settingsService->forUser($user);

        if ($setting->tonight_queue_started_at === null) {
            $setting->tonight_queue_started_at = now();
            $setting->save();
        }

        $position = (int) TonightQueueItem::query()->where('user_id', $user->id)->max('position');

        TonightQueueItem::query()->create([
            'user_id' => $user->id,
            'media_type' => $type,
            'media_id' => $id,
            'title' => (string) ($item['title'] ?? 'Untitled'),
            'poster_path' => $item['poster'] ?? null,
            'position' => $position + 1,
        ]);

        return $this->forUser($user)['items'];
    }

    public function remove(User $user, string $type, int $id): array
    {
        TonightQueueItem::query()
            ->where('user_id', $user->id)
            ->where('media_type', $type)
            ->where('media_id', $id)
            ->delete();

        if (! TonightQueueItem::query()->where('user_id', $user->id)->exists()) {
            $setting = $this->settingsService->forUser($user);
            $setting->tonight_queue_started_at = null;
            $setting->save();
        }

        return $this->forUser($user)['items'];
    }

    public function clear(User $user): array
    {
        TonightQueueItem::query()->where('user_id', $user->id)->delete();

        $setting = $this->settingsService->forUser($user);
        $setting->tonight_queue_started_at = null;
        $setting->save();

        return [];
    }

    public function toggle(User $user, array $item): array
    {
        $type = (string) ($item['type'] ?? '');
        $id = (int) ($item['id'] ?? 0);

        $exists = TonightQueueItem::query()
            ->where('user_id', $user->id)
            ->where('media_type', $type)
            ->where('media_id', $id)
            ->exists();

        if ($exists) {
            return [
                'added' => false,
                'items' => $this->remove($user, $type, $id),
            ];
        }

        return [
            'added' => true,
            'items' => $this->add($user, $item),
        ];
    }

    private function expireIfNeeded(User $user): void
    {
        $setting = $this->settingsService->forUser($user);
        $startedAt = $setting->tonight_queue_started_at;

        if ($startedAt === null) {
            return;
        }

        if ($startedAt->diffInHours(now()) < self::TTL_HOURS) {
            return;
        }

        TonightQueueItem::query()->where('user_id', $user->id)->delete();
        $setting->tonight_queue_started_at = null;
        $setting->save();
    }

    /** @return array<string, mixed> */
    private function present(TonightQueueItem $item): array
    {
        return [
            'type' => $item->media_type,
            'id' => (int) $item->media_id,
            'title' => $item->title,
            'poster' => $item->poster_path,
        ];
    }
}
