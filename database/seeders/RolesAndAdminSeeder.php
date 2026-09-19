<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // ১. রোলগুলো তৈরি করা
        $roles = ['Super Admin', 'Company Admin', 'Employee', 'Candidate'];
        
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // ২. ডিফল্ট সুপার অ্যাডমিন তৈরি করা
        $admin = User::firstOrCreate([
            'email' => 'admin@whexsoft.com' // আপনি আপনার পছন্দমতো ইমেইল দিতে পারেন
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $admin->assignRole('Super Admin');
    }
}