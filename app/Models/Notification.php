<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Notification extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'note',
        'is_broadcast',
        'receiver'
    ];

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver');
    }
}
