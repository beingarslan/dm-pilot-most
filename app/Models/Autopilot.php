<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autopilot extends Model
{
    protected $dates = [
        'starts_at',
        'ends_at',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'account_id',
        'name',
        'action',
        'lists_id',
        'text',
        'starts_at',
        'ends_at',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function lists()
    {
        return $this->belongsTo('App\Models\Lists');
    }
}
