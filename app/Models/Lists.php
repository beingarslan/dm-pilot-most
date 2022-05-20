<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;

class Lists extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'name',
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

    public function items()
    {
        return $this->hasMany('App\Models\ListsItem');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getText()
    {
        $message          = $this->items()->inRandomOrder()->first();
        $message->used_at = now();
        $message->save();

        return $message->text;
    }

    public function getRandomUser()
    {
        return $this->items()->ofType('users')->inRandomOrder()->first();
    }
}
