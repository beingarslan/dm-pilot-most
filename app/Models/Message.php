<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $dates = [
        'send_at',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'account_id',
        'message_type',
        'options',
        'status',
        'comment',
        'send_at',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OwnerScope);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withDefault();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withDefault();
    }

    public function scopeOnQueue($query)
    {
        return $query->where('status', config('pilot.MESSAGE_STATUS_ON_QUEUE'));
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', config('pilot.MESSAGE_STATUS_SUCCESS'));
    }

    public function scopeFailed($query)
    {
        return $query->where('status', config('pilot.MESSAGE_STATUS_FAILED'));
    }

    public function getRecipientsAttribute()
    {
        $recipients = [];

        if (isset($this->options['to']['users']) && count($this->options['to']['users'])) {
            foreach ($this->options['to']['users'] as $recipient) {
                $recipients[] = $recipient['username'];
            }
            return join(', ', $recipients);
        }

        if (isset($this->options['to']['thread_id'])) {
            return __('Direct thread');
        }
    }
}
