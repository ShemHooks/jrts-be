<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::create([
            'id' => Str::uuid(),
            'department_code' => 'ICTD',
            'dept_name' => 'Information Communication Technology Division',
            'acronym' => 'ICTD',
            'dept_head_id' => null,
            'parent_id' => null,
            'status' => 'active',
        ]);
    }
}