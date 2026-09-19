<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Company;
use App\Models\Candidate;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ১. Roles তৈরি করা
        $roles = ['Super Admin', 'Company Admin', 'Employee', 'Candidate'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ২. Super Admin তৈরি করা
        $superAdmin = User::firstOrCreate([
            'email' => 'admin@whexsoft.com' // সুপার অ্যাডমিন ইমেইল
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $superAdmin->assignRole('Super Admin');

        // ৩. ডেমো কোম্পানি এবং তাদের অ্যাডমিন তৈরি করা
        $demoCompanies = [
            ['name' => 'Grow Wave', 'email' => 'contact@growwave.com', 'phone' => '01335198861'],
            ['name' => 'Lenslumino', 'email' => 'hello@lenslumino.com', 'phone' => '01335198862'],
            ['name' => 'BuzzBlu', 'email' => 'info@buzzblu.com', 'phone' => '01335198863'],
        ];

        foreach ($demoCompanies as $index => $compData) {
            // কোম্পানি সেভ
            $company = Company::firstOrCreate(
                ['email' => $compData['email']],
                ['name' => $compData['name'], 'phone' => $compData['phone'], 'status' => 'active']
            );

            // ওই কোম্পানির জন্য Company Admin সেভ
            $adminEmail = 'admin@' . strtolower(str_replace(' ', '', $compData['name'])) . '.com';
            $companyAdmin = User::firstOrCreate([
                'email' => $adminEmail
            ], [
                'name' => $compData['name'] . ' Admin',
                'company_id' => $company->id,
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);
            $companyAdmin->assignRole('Company Admin');

            // টেস্টিংয়ের জন্য ওই কোম্পানিতে একজন ডেমো ক্যান্ডিডেট অ্যাড করা
            Candidate::firstOrCreate([
                'email' => 'candidate' . $index . '@test.com'
            ], [
                'company_id' => $company->id,
                'name' => 'Demo Candidate ' . ($index + 1),
                'phone' => '0170000000' . $index,
                'position' => 'Web Developer',
                'status' => 'Pending',
            ]);
        }

     // ডেমো পজিশন অ্যাড করা
            \App\Models\Position::firstOrCreate(
                ['company_id' => $company->id, 'title' => 'Web Developer'],
                ['questions' => json_encode(['HTML কী?', 'Vue JS কেন ব্যবহার করা হয়?']), 'is_active' => true]
            );
            \App\Models\Position::firstOrCreate(
                ['company_id' => $company->id, 'title' => 'Graphic Designer'],
                ['questions' => json_encode(['Color Theory কী?', 'Photoshop এর কাজ কী?']), 'is_active' => true]
            );
    }
}   