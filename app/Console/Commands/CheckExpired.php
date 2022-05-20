<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\SubscriptionExpired;
use App\Notifications\TrialExpired;
use Illuminate\Console\Command;

class CheckExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification about expired subscriptions / trial';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::whereNotNull('package_ends_at')
            ->where('package_ends_at', '<=', now()->addHour()) // We add additional hour to wait for recurring payment applied
            ->get();

        foreach ($users as $user) {
            $user->notify((new SubscriptionExpired())->onQueue('mail'));
            $user->package_ends_at = null;
            $user->save();

            $this->info('Subscription expired for: ' . $user->name);
        }

        $users = User::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', now()->addHour()) // We add additional hour to wait for recurring payment applied
            ->get();

        foreach ($users as $user) {
            $user->notify((new TrialExpired())->onQueue('mail'));
            $user->trial_ends_at = null;
            $user->save();

            $this->info('Trial expired for: ' . $user->name);
        }

    }
}
