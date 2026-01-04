<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@carmarketplace.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@carmarketplace.com',
                'password' => Hash::make('superadmin123'),
                'role' => 'super_admin',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: superadmin@carmarketplace.com');
        $this->command->info('Password: superadmin123');
    }
}
