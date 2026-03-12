<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'driver' => \App\Http\Middleware\DriverMiddleware::class,
        ]);
        
        // Add cache control middleware globally
        $middleware->append(\App\Http\Middleware\CacheControl::class);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Send departure reminders every 10 minutes to catch trips departing in 1 hour
        $schedule->command('reminders:send-departure')
            ->everyTenMinutes()
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
