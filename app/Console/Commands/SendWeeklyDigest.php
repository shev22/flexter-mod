<?php

namespace App\Console\Commands;

use App\Mail\WeeklyDigestMail;
use App\Models\User;
use App\WatchHistory\Services\Interfaces\WatchHistoryServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyDigest extends Command
{
    protected $signature = 'flexter:send-weekly-digest';

    protected $description = 'Send weekly email digests to users who opted in';

    public function handle(WatchHistoryServiceInterface $history): int
    {
        $sent = 0;

        User::query()
            ->with('settings')
            ->chunkById(100, function ($users) use ($history, &$sent) {
                foreach ($users as $user) {
                    if (! $user->hasVerifiedEmail()) {
                        continue;
                    }

                    $wantsEmail = $user->settings?->email_notifications ?? true;

                    if (! $wantsEmail) {
                        continue;
                    }

                    $stats = $history->stats($user);
                    $continue = $history->continueWatching($user, 5);

                    if ($stats['total'] === 0 && $continue === []) {
                        continue;
                    }

                    Mail::to($user)->send(new WeeklyDigestMail($user, [
                        'stats' => $stats,
                        'continue' => $continue,
                    ]));

                    $sent++;
                }
            });

        $this->info("Sent {$sent} weekly digest(s).");

        return self::SUCCESS;
    }
}
