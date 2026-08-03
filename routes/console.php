<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('orders:reconcile-paystack')->everyFiveMinutes();
Schedule::command('orders:cancel-unpaid')->everyFiveMinutes();
Schedule::command('sanctum:prune-expired --hours=2160')->daily(); // prune tokens older than 90 days
