<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('pc', function () {

    $paths = [
        'storage/',
        'bootstrap/cache/',
        'public/',
        'packages/workdo/',
        'uploads/',
        'resources/lang/',
        '.env'
    ];

    foreach ($paths as $path) {
        $output = [];
        $resultCode = 0;

        exec("sudo chmod -R 777 $path", $output, $resultCode);

        if ($resultCode !== 0) {
            $this->error("Failed to change permissions for $path. Output: " . implode("\n", $output));
        } else {
            $this->info("Permissions changed successfully for $path");
        }
    }

    // Clear various caches
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('optimize:clear');

    $this->info('All caches cleared and permissions set!');
})->describe('Clear all types of caches and set file permissions');

app()->singleton(Schedule::class, function ($app) {
    return tap(new Schedule, function ($schedule) {
        $schedule->call(function () {
           Artisan::call('app:automate-task-scheduler');
            Artisan::call('app:remove-automate-task-scheduler');
        })->everyMinute();
        
          // Send welcome email to users after 7 days of joining
        $schedule->command('app:send-welcome-email-after-7days')
            ->dailyAt('02:18') // Run daily at 2:18 AM
            ->withoutOverlapping()
            ->onOneServer();
            
             // Send welcome email to users after 30 days of joining
        $schedule->command('app:send-welcome-email-after-30days')
            ->dailyAt('02:35') // Run daily at 9:30 AM
            ->withoutOverlapping()
            ->onOneServer();
            
    });
   
    });

