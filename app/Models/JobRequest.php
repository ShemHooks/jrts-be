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
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function requestingOffice()
    {
        return $this->belongsTo(Department::class, 'requested_from');
    }

    public function requestTimeStamp()
    {
        return $this->hasMany(JobRequestTimeStamp::class, 'request_id');
    }

    public function technicians()
    {
        return $this->belongsToMany(
            User::class,
            'job_request_technicians',
            'request_id',
            'technician_id'
        );
    }

}
