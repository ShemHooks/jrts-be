<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Department extends Model
{

    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'department_code',
        'dept_name',
        'acronym',
        'dept_head_id',
        'parent_id',
        'status'
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
