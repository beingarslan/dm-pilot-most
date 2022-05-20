<?php

namespace App\Models;

use App\Casts\CryptPassword;
use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;
use InstagramAPI\Instagram;

class Account extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'followers_sync_at',
        'following_sync_at',
    ];

    protected $fillable = [
        'user_id',
        'proxy_id',
        'platform',
        'username',
        'password',
        'followers_count',
        'following_count',
        'posts_count',
        'followers_sync_at',
        'following_sync_at',
        'is_active',
    ];

    protected $appends = [
        'has_bot',
        'has_rss',
    ];

    protected $casts = [
        'password'  => CryptPassword::class,
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OwnerScope);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function proxy()
    {
        return $this->belongsTo('App\Models\Proxy');
    }

    public function messages_on_queue()
    {
        return $this->hasMany('App\Models\Message')->onQueue();
    }

    public function messages_sent()
    {
        return $this->hasMany('App\Models\Message')->success();
    }

    public function messages_failed()
    {
        return $this->hasMany('App\Models\Message')->failed();
    }

    public function followers()
    {
        return $this->hasMany('App\Models\Follower');
    }

    public function autopilot()
    {
        return $this->hasMany('App\Models\Autopilot');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }

    public function statistic()
    {
        return $this->hasMany('App\Models\Statistic');
    }

    public function rss()
    {
        return $this->hasMany('App\Models\Rss');
    }

    public function bot()
    {
        return $this->hasOne('App\Models\Bot')->withDefault();
    }

    public function getHasBotAttribute()
    {
        return $this->hasOne('App\Models\Bot')->count() ? true : false;
    }

    public function getHasRssAttribute()
    {
        return $this->rss()->count() ? true : false;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAllThreads()
    {
        $maxExecutionTime = ini_get("max_execution_time") - 3;

        if (!$maxExecutionTime) {
            $maxExecutionTime = 27;
        }

        $startedAt = microtime(true);
        $threads   = [];

        $instagram = new Instagram(
            config('pilot.debug'),
            config('pilot.truncatedDebug'),
            config('pilot.storageConfig')
        );

        if ($this->proxy) {
            $instagram->setProxy($this->proxy->server);
        }

        $instagram->setPlatform($this->platform);

        try {

            $instagram->login($this->username, $this->password);

            $cursorId = null;

            do {

                $response = $instagram->direct->getInbox($cursorId, 20);

                if ($inbox = $response->getInbox()) {

                    foreach ($inbox->getThreads() as $thread) {

                        $threads[] = [
                            'thread_id' => $thread->getThreadId(),
                        ];

                    }

                    $cursorId = $inbox->getHasOlder() ? $inbox->getOldestCursor() : null;

                    $timeElapsed = microtime(true) - $startedAt;

                    // Simulate real human behavior
                    if ($timeElapsed <= $maxExecutionTime) {
                        sleep(rand(1, 3));
                    } else {
                        break;
                    }

                }

            } while ($cursorId !== null);

        } catch (\Exception $e) {

        }

        return array_reverse($threads);
    }
}
