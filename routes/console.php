<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
|
| The state of the tasks and of the internships used to be refreshed as a
| side effect of opening a dashboard. It is now done here so it happens
| even when nobody is looking at the application.
|
*/

Schedule::command('tasks:update-states')->dailyAt('00:05');
Schedule::command('internships:close-finished')->dailyAt('00:10');
