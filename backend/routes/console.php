<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command("app:send-reminder")
    ->daily()
    ->appendOutputTo(storage_path('logs/scheduler-tasks.log'))
    ->onFailure(function () {
        Log::error('Scheduled task [app:send-reminder] failed.');
    });

Schedule::command('model:prune')
    ->daily()
    ->appendOutputTo(storage_path('logs/scheduler-tasks.log'))
    ->onFailure(function () {
        Log::error('Scheduled task [model:prune] failed.');
    });