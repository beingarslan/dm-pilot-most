<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use Notifiable, HasMediaTrait;

    protected $dates = [
        'email_verified_at',
        'package_ends_at',
        'trial_ends_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_admin',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'package_id',
        'package_ends_at',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 120, 120)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 500, 500)
            ->nonQueued();
    }

    public function accounts()
    {
        return $this->hasMany('App\Models\Account');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Message');
    }

    public function messages_on_queue()
    {
        return $this->messages()->onQueue();
    }

    public function messages_sent()
    {
        return $this->messages()->success();
    }

    public function messages_failed()
    {
        return $this->messages()->failed();
    }

    public function lists()
    {
        return $this->hasMany('App\Models\Lists');
    }

    public function autopilots()
    {
        return $this->hasManyThrough('App\Models\Autopilot', 'App\Models\Account');
    }

    public function statistic()
    {
        return $this->hasMany('App\Models\Statistic');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function rss()
    {
        return $this->hasMany('App\Models\Rss');
    }

    public function bots()
    {
        return $this->hasMany('App\Models\Bot');
    }

    public function package()
    {
        $trial_permissions = array_map(function ($permission) {
            return true;
        }, config('pilot.PERMISSIONS'));

        return $this->belongsTo('App\Models\Package')->withDefault([
            'settings' => array_merge([
                'storage'        => config('pilot.TRIAL_STORAGE'),
                'accounts_count' => config('pilot.TRIAL_ACCOUNTS_COUNT'),
                'messages_count' => config('pilot.TRIAL_MESSAGES_COUNT'),
            ], $trial_permissions),
        ]);
    }

    public function subscribed()
    {
        if (is_null($this->package_ends_at)) {
            return false;
        }

        return $this->package_ends_at->isFuture();
    }

    public function onTrial()
    {
        if (is_null($this->trial_ends_at)) {
            return false;
        }

        return $this->trial_ends_at->isFuture();
    }
}
