<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Console Schedules
|--------------------------------------------------------------------------
*/

Schedule::command('appointment:send-daily-summary')
    ->dailyAt('07:30')
    ->timezone('Asia/Jakarta');

Schedule::command('appointment:send-reminders')
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta');

Schedule::command('sam:purge-old-data')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone('Asia/Jakarta');

Schedule::command('backup:clean')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta');

Schedule::command('backup:run')
    ->dailyAt('01:30')
    ->timezone('Asia/Jakarta');
