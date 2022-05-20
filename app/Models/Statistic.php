<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    public $timestamps = false;

    protected $dates = [
        'sync_at',
    ];

    protected $fillable = [
        'user_id',
        'account_id',
        'type',
        'sync_at',
        'count',
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

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function scopeMedia($query)
    {
        return $query->where('type', config('pilot.STATISTICS_MEDIA'));
    }

    public function scopeFollowing($query)
    {
        return $query->where('type', config('pilot.STATISTICS_FOLLOWING'));
    }

    public function scopeFollowers($query)
    {
        return $query->where('type', config('pilot.STATISTICS_FOLLOWERS'));
    }
}
