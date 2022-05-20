<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RssItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'rss_id',
        'title',
        'url',
        'image',
    ];

    public function rss()
    {
        return $this->belongsTo('App\Models\Rss');
    }
}
