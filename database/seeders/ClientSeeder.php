<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Sample Client',
            'email' => 'client@example.com',
            'password' => Hash::make('password123'),
            'employee_id' => 'EMP-002',
            'is_activated' => false,
            'suffix' => null,
            'position' => 'ICTD Administrator',
            'role' => 'client',
            'required_change' => false,
            'account_status' => 'active',
        ]);

    }
}
