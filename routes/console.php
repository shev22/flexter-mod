<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keep the home carousel fresh: re-sync trending status against TMDB daily.
Schedule::command('flexter:trending')->dailyAt('05:00')->withoutOverlapping();

Schedule::command('flexter:send-weekly-digest')
    ->weeklyOn(1, '09:00')
    ->timezone('UTC')
    ->withoutOverlapping();
