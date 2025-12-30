<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates a pre-seeded admin account.
     * Admin should NOT be created via public registration.
     */
    public function run(): void
    {
        // Create or update admin user
        User::updateOrCreate(
            ['email' => 'admin@carmarketplace.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'), // Change this password in production
                'role' => 'admin',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@carmarketplace.com');
        $this->command->info('Password: admin123');
        $this->command->warn('Please change the admin password after first login!');
    }
}

