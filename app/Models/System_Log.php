<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class System_Log extends Model
{

    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'user_role',
        'action',
        'is_hidden'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
