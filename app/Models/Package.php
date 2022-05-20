<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'title',
        'price',
        'interval',
        'settings',
        'is_featured',
        'is_hidden',
    ];

    protected $casts = [
        'settings'    => 'array',
        'is_featured' => 'boolean',
        'is_hidden'   => 'boolean',
    ];

    public function getPlanIdAttribute()
    {
        return Str::lower(
            'plan-'
            . $this->id . '-'
            . Str::slug($this->title, '-') . '-'
            . $this->whole_price . '-'
            . $this->fraction_price . '-'
            . config('pilot.CURRENCY_CODE') . '-'
            . $this->interval
        );
    }

    public function getPriceInCentsAttribute()
    {
        return $this->price * 100;
    }

    public function getWholePriceAttribute()
    {
        return floor($this->price);
    }

    public function getFractionPriceAttribute()
    {
        return ltrim(round($this->price - $this->whole_price, 2), '0.');
    }

    public function getAccountsLimitAttribute()
    {
        return $this->settings['accounts_count'];
    }

    public function getStorageLimitAttribute()
    {
        return $this->settings['storage'];
    }

    public function getMessagesLimitAttribute()
    {
        return $this->settings['messages_count'];
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function getPermissionsAttribute()
    {
        $all_permissions = config('pilot.PERMISSIONS');

        asort($all_permissions);

        $permissions = [];
        foreach ($all_permissions as $permission => $description) {
            $permissions[$permission] = [
                'description' => $description,
                'can'         => $this->hasPermissionTo($permission),
            ];
        }

        return $permissions;
    }

    public function hasPermissionTo($permission)
    {
        if (array_key_exists($permission, $this->settings)) {
            return $this->settings[$permission];
        }
        return false;
    }

}
