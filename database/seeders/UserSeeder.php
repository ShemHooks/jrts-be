<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'employee_id' => 'EMP-001',
            'is_activated' => true,
            'suffix' => null,
            'position' => 'ICTD Administrator',
            'role' => 'admin',
            'required_change' => false,
            'account_status' => 'active',
        ]);


    }
}