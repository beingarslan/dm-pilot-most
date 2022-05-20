<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;
use InstagramAPI\Response\Model\Location;

class Rss extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'account_id',
        'name',
        'url',
        'template',
        'location',
        'first_comment',
        'is_active',
    ];

    protected $casts = [
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

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function items()
    {
        return $this->hasMany('App\Models\RssItem');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getLocationNameAttribute()
    {
        if ($this->location) {
            $location = unserialize($this->location);

            if ($location instanceof Location) {
                return $location->hasName() ? $location->getName() : null;
            }
        }

        return null;
    }

}
