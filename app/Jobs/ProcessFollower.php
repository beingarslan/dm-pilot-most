<?php

namespace App\Jobs;

use App\Library\Spintax;
use App\Models\Account;
use App\Models\Autopilot;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessFollower implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 1; // The number of times the job may be attempted.
    public $timeout = 10; // If meesage don't sent in 10 seconds - reject
    protected $account_id;
    protected $action;
    protected $pk;
    protected $username;
    protected $fullname;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($options = [])
    {
        $this->account_id = $options['account_id'];
        $this->action     = $options['action'];
        $this->pk         = $options['pk'];
        $this->username   = $options['username'];
        $this->fullname   = $options['fullname'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = now();

        // Lookup for matching account
        $account = Account::withoutGlobalScopes()->find($this->account_id);

        if ($account) {

            $autopilot = Autopilot::where('account_id', $this->account_id)
                ->where('action', $this->action)
                ->with(['lists' => function ($q) {
                    $q->ofType('messages')->withoutGlobalScopes();
                }])
                ->whereRaw("NOW() BETWEEN COALESCE(`starts_at`, '1900-01-01') AND COALESCE(`ends_at`, NOW())")
                ->get();

            if ($autopilot->count()) {

                foreach ($autopilot as $AP) {

                    // Use message from list if specified
                    if ($AP->lists) {
                        $message = $AP->lists->getText();
                    } else {
                        $message = $AP->text;
                    }

                    // Spintax
                    $spintax = new Spintax();
                    $message = $spintax->process($message);

                    $options = [
                        'to'      => [
                            'users' => [
                                [
                                    'pk'       => $this->pk,
                                    'username' => $this->username,
                                    'fullname' => $this->fullname,
                                ],
                            ],
                        ],
                        'message' => $message,
                    ];

                    Message::create([
                        'user_id'      => $account->user_id,
                        'account_id'   => $account->id,
                        'message_type' => config('pilot.MESSAGE_TYPE_TEXT'),
                        'options'      => $options,
                        'status'       => config('pilot.MESSAGE_STATUS_ON_QUEUE'),
                        'send_at'      => $now->addSeconds(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX'))),
                    ]);

                }
            }
        }
    }
}
