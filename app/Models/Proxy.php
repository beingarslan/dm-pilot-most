<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at',
    ];

    protected $fillable = [
        'server',
        'country',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function accounts()
    {
        return $this->hasMany('App\Models\Account');
    }

    public function getUseCountAttribute()
    {
        return $this->accounts()->withoutGlobalScopes()->where('proxy_id', $this->id)->count();
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereDate('expires_at', '>', now())->orWhereNull('expires_at');
        })->where('is_active', true);

    }
}
