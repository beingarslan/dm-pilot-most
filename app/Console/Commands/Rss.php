<?php

namespace App\Console\Commands;

use App\Library\Spintax;
use App\Models\RssItem;
use App\Models\User;
use Feeds;
use Fusonic\OpenGraph\Consumer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Rss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:rss';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse and publish from RSS feeds';

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
        $now = now();

        $users = User::with(['accounts' => function ($q) {
            $q->withoutGlobalScopes();
        }])->whereHas('accounts', function ($q) {
            $q->withoutGlobalScopes();
        })->get();

        if ($users->count()) {

            foreach ($users as $user) {

                if (!$user->subscribed() && !$user->onTrial() && !$user->can('admin')) {
                    continue;
                }

                // Check for free storage space to store media
                $used_space    = $user->getMedia()->sum('size');
                $storage_limit = $user->package->storage_limit * 1024 * 1024;

                if ($used_space >= $storage_limit) {
                    $this->info('Storage limit exceed for user: ' . $user->email);
                    continue;
                }

                foreach ($user->accounts as $account) {

                    $this->info('Getting feed for account: ' . $account->username);

                    $rsses = $account->rss()->active()->withoutGlobalScopes()->get();

                    foreach ($rsses as $rss) {

                        $this->info('Working with RSS: ' . $rss->name);

                        try {

                            $feed       = Feeds::make($rss->url);
                            $feed_items = $feed->get_items();

                            foreach ($feed_items as $feed_item) {

                                $rss_item = RssItem::firstOrCreate(
                                    ['url' => $feed_item->get_permalink(), 'rss_id' => $rss->id],
                                    ['title' => $feed_item->get_title()]
                                );

                                if ($rss_item->wasRecentlyCreated) {

                                    $this->info('Parsing OpenGraph URL: ' . $feed_item->get_permalink());

                                    // Parse Open Graph
                                    $consumer        = new Consumer();
                                    $opengraphObject = $consumer->loadUrl($feed_item->get_permalink());

                                    if (count($opengraphObject->images)) {

                                        $image = reset($opengraphObject->images);

                                        $rss_item->image = $image->url;
                                        $rss_item->save();

                                        // Save media to manager
                                        $media = $user->addMediaFromUrl($rss_item->image)->toMediaCollection();

                                        // Prepare caption
                                        $caption = str_replace(':title', $rss_item->title, $rss->template);
                                        $caption = str_replace(':url', $rss_item->url, $caption);

                                        // Prepare first comment
                                        $first_comment = str_replace(':title', $rss_item->title, $rss->first_comment);
                                        $first_comment = str_replace(':url', $rss_item->url, $first_comment);

                                        // Spintax support
                                        $spintax       = new Spintax();
                                        $caption       = $spintax->process($caption);
                                        $first_comment = $spintax->process($first_comment);

                                        // Schedule post
                                        $account->posts()->create([
                                            'type'         => 'post',
                                            'ig'           => [
                                                'media'         => [
                                                    $media->id,
                                                ],
                                                'location'      => $rss->location,
                                                'first_comment' => $first_comment,
                                            ],
                                            'caption'      => $caption,
                                            'status'       => config('pilot.POST_STATUS_SCHEDULED'),
                                            'scheduled_at' => $now->addMinutes(10),
                                        ]);
                                    }

                                    $this->info(date('H:i:s') . ': Sleeping for a while on line: ' . __LINE__);

                                    // Simulate real human behavior
                                    sleep(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX')));
                                }

                            }

                        } catch (\Exception $e) {

                            $this->info('Error: ' . $e->getMessage());

                            Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());
                        }

                    }

                }
            }
        } else {
            $this->info('No users found');
        }
    }
}
