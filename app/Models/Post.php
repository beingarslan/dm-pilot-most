<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use InstagramAPI\Response\Model\Location;
use Spatie\MediaLibrary\Models\Media;

class Post extends Model
{
    public $timestamps = false;

    protected $dates = [
        'scheduled_at',
        'posted_at',
    ];

    protected $fillable = [
        'account_id',
        'type',
        'ig',
        'caption',
        'status',
        'comment',
        'scheduled_at',
        'posted_at',
    ];

    protected $casts = [
        'ig' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', config('pilot.POST_STATUS_SCHEDULED'));
    }

    public function scopePublished($query)
    {
        return $query->where('status', config('pilot.POST_STATUS_PUBLISHED'));
    }

    public function scopeFailed($query)
    {
        return $query->where('status', config('pilot.POST_STATUS_FAILED'));
    }

    public function getPreviewImageAttribute()
    {
        // First try to get to get from local media
        $media = Media::where('model_id', $this->account->user->id)
            ->whereIn('id', $this->ig['media'])
            ->orderByRaw("FIELD(`id`, '" . join("', '", $this->ig['media']) . "')")
            ->first();

        if ($media) {
            return asset($media->getUrl('preview'));
        }

        // Then try from Instagram data
        if ($this->ig) {
            if (isset($this->ig['image_versions'][1]['url'])) {
                return $this->ig['image_versions'][1]['url'];
            }

            if (isset($this->ig['image_versions'][0]['url'])) {
                return $this->ig['image_versions'][0]['url'];
            }
        }

        // Only then show N/A
        return asset('public/img/no-image.png');
    }

    public function getAllMediaAttribute()
    {
        return Media::where('model_id', $this->account->user->id)
            ->whereIn('id', $this->ig['media'])
            ->orderByRaw("FIELD(`id`, '" . join("', '", $this->ig['media']) . "')")
            ->get();
    }

    public function getUrlAttribute()
    {
        if ($this->ig) {
            if (isset($this->ig['code'])) {
                return 'https://www.instagram.com/p/' . $this->ig['code'] . '/';
            }
        }

        return null;
    }

    public function getFirstCommentAttribute()
    {
        if ($this->ig) {
            return (isset($this->ig['first_comment']) ? $this->ig['first_comment'] : null);
        }

        return null;
    }

    public function getLocationAttribute()
    {
        if ($this->ig) {
            return (isset($this->ig['location']) ? $this->ig['location'] : null);
        }

        return null;
    }

    public function getLocationNameAttribute()
    {
        if ($this->ig) {
            if (isset($this->ig['location'])) {
                $location = unserialize($this->ig['location']);

                if ($location instanceof Location) {
                    return $location->hasName() ? $location->getName() : null;
                }
            }
        }

        return null;
    }

    public function getIsPublishedAttribute()
    {
        return $this->status == config('pilot.POST_STATUS_PUBLISHED');
    }

    public function getIsScheduledAttribute()
    {
        return $this->status == config('pilot.POST_STATUS_SCHEDULED');
    }

    public function getIsFailedAttribute()
    {
        return $this->status == config('pilot.POST_STATUS_FAILED');
    }

}
