<?php

namespace App\Models;

use App\Notifications\SubscriptionPaid;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'package_id',
        'reference',
        'gateway',
        'total',
        'is_paid',
        'currency',
        'options',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'options' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withDefault();
    }

    public function package()
    {
        return $this->belongsTo('App\Models\Package')->withDefault();
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function applyPayment()
    {
        $now = now();

        // Extend only if user is on the same package and subscribed
        if ($this->user->subscribed() && $this->user->package_id == $this->package_id) {
            $now = $this->user->package_ends_at;
        }

        switch ($this->package->interval) {
            case 'day':
                $package_ends_at = $now->addDay();
                break;
            case 'week':
                $package_ends_at = $now->addWeek();
                break;
            case 'month':
                $package_ends_at = $now->addMonth();
                break;
            case 'year':
                $package_ends_at = $now->addYear();
                break;
            default:

                break;
        }

        $this->user->update([
            'package_id'      => $this->package_id,
            'package_ends_at' => $package_ends_at,
            'trial_ends_at'   => null,
        ]);

        $this->user->notify((new SubscriptionPaid($this))->onQueue('mail'));
    }

}
