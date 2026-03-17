<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JobRequest extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'requested_by',
        'description',
        'status',
        'requested_from',
        'look_for'
    ];

    public function requester()
    {
        return $this->belongsTo(User::class);
    }

    public function requestingOffice()
    {
        return $this->belongsTo(Department::class);
    }

    public function RequestTimeStamp()
    {
        return $this->hasMany(JobRequestTimeStamp::class);
    }

}
