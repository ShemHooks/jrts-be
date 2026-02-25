<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'department_code',
        'dept_name',
        'dept_head_id'
    ];

    public function deptAdmin()
    {
        return $this->belongsTo(User::class);
    }
}
