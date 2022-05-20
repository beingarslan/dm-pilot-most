<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListsItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'list_id',
        'text',
        'used_at'
    ];

    public function lists()
    {
        return $this->belongsTo('App\Models\Lists');
    }
}
