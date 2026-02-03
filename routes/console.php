<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule cleanup tasks
Schedule::command('clean:attachments')
    ->daily()
    ->at('00:00')
    ->description('Clean up old attachments based on retention period (preserves logos)');

Schedule::command('clean:logs')
    ->daily()
    ->at('00:00')
    ->description('Clean up old logs based on retention period');

Schedule::command('clean:resolved-issues')
    ->daily()
    ->at('00:00')
    ->description('Clean up old resolved issues based on retention period');

Schedule::command('clean:soft-deleted')
    ->daily()
    ->at('00:00')
    ->description('Hard delete soft-deleted records older than retention period (cascades to disinfection slips)');

// Backup tasks
Schedule::command('backup:run')
    ->dailyAt('19:00')
    ->description('Run database backup daily after working hours');

Schedule::command('backup:clean')
    ->dailyAt('05:00')
    ->description('Clean up old backups daily');

Schedule::command('backup:monitor')
    ->daily()
    ->description('Monitor backup health daily');