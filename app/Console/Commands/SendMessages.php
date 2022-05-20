<?php

namespace App\Console\Commands;

use App\Library\SendMessage;
use App\Models\Message;
use Illuminate\Console\Command;

class SendMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:send-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send messages from the queue';

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
        $messages = Message::withoutGlobalScopes()->onQueue()->where('send_at', '<=', now())->get();

        foreach ($messages as $message) {
            new SendMessage($message);
        }
    }
}
