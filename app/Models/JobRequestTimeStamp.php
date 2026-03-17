<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class JobRequestTimeStamp extends Model
{
    use HasUuids;

    protected $fillable = [
        ,
        'description',
        'request_id',
        'action',
        'look_for',
        'date',
        'time'
    ];

    public function requests()
    {
        return $this->belongsTo(JobRequest::class);
    }

}
