<?php

namespace App\Console;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work --queue=mail,autopilot --sleep=3 --tries=1 --stop-when-empty')
            ->everyMinute()
            ->name('queue')
            ->withoutOverlapping(5);

        $schedule->command('pilot:activity')
            ->everyMinute()
            ->name('activity')
            ->withoutOverlapping(5);

        $schedule->command('pilot:send-messages')
            ->everyMinute()
            ->name('send-messages')
            ->withoutOverlapping(5);

        $schedule->command('pilot:publish-posts')
            ->everyMinute()
            ->name('publish-posts')
            ->withoutOverlapping(5);

        $schedule->command('pilot:get-follower followers')
            ->everyTenMinutes()
            ->name('followers')
            ->withoutOverlapping(20);

        $schedule->command('pilot:get-follower following')
            ->everyTenMinutes()
            ->name('following')
            ->withoutOverlapping(20);

        $schedule->command('pilot:proxy')
            ->hourly()
            ->name('proxy')
            ->withoutOverlapping(60);

        $schedule->command('pilot:expired')
            ->everyMinute()
            ->name('expired')
            ->withoutOverlapping(5);

        $schedule->command('pilot:billing')
            ->everyMinute()
            ->name('billing')
            ->withoutOverlapping(5);

        $schedule->command('pilot:rss')
            ->everyTenMinutes()
            ->name('rss')
            ->withoutOverlapping(10);

        $schedule->command('pilot:license')
            ->daily()
            ->name('license')
            ->withoutOverlapping(60);

        $schedule->command('queue:retry all')
            ->hourly()
            ->withoutOverlapping(5);

        $users = User::with(['accounts' => function ($q) {
            $q->withoutGlobalScopes();
        }])->whereHas('accounts', function ($q) {
            $q->withoutGlobalScopes();
        })->each(function ($user) use ($schedule) {

            if (!$user->subscribed() && !$user->onTrial() && !$user->can('admin')) {
                return false;
            }

            foreach ($user->accounts as $account) {

                $schedule->command('pilot:bot ' . $account->id)
                    ->everyMinute()
                    ->name('bot_' . $account->id)
                    ->withoutOverlapping()
                    ->runInBackground();

            }
        });

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return config('app.timezone');
    }
}
