<?php

namespace App\Console\Commands;

use App\Library\PublishPost;
use App\Models\Post;
use Illuminate\Console\Command;

class PublishPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:publish-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts';

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
        $posts = Post::scheduled()->where('scheduled_at', '<=', now())->get();

        foreach ($posts as $post) {
            new PublishPost($post);
        }

// Timezone version

//        $posts = Post::scheduled()->all();
//
//        foreach ($posts as $post) {
//
//            $timezone = $post->account->user->timezone;
//
//            if ($post->scheduled_at->setTimezone($timezone)->isPast()) {
//                new PublishPost($post);
//            }
//        }
    }
}
