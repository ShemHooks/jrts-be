<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'department_code',
        'dept_name',
        'acronym',
        'dept_head_id',
        'parent_id',
    ];

    public function deptAdmin()
    {
        return $this->belongsTo(User::class);
    }

    public function parentDept()
    {
        return $this->belongsTo(Department::class);
    }

    public function childDept()
    {
        return $this->hasMany(Department::class);
    }
}
