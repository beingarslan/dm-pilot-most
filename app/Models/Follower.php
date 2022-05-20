<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'account_id',
        'type',
        'username',
        'fullname',
        'pk',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function scopeFollowers($query)
    {
        return $query->where('type', config('pilot.FOLLOWER_TYPE_FOLLOWERS'));
    }

    public function scopeFollowing($query)
    {
        return $query->where('type', config('pilot.FOLLOWER_TYPE_FOLLOWING'));
    }
}
