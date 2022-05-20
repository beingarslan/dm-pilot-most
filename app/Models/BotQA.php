<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotQA extends Model
{
    protected $table = 'bot_qa';

    public $timestamps = false;

    protected $fillable = [
        'bot_id',
        'ordering',
        'hears',
        'message_type',
        'message',
    ];

    protected $casts = [
        'hears'   => 'array',
        'message' => 'array',
    ];

    public function bot()
    {
        return $this->belongsTo('App\Models\Bot');
    }
}
