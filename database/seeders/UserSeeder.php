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
        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@carmarketplace.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        // Dealer Users
        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "dealer$i@example.com"],
                [
                    'name' => "Dealer $i",
                    'password' => Hash::make('password'),
                    'role' => 'dealer',
                    'is_verified' => true,
                    'verified_at' => now(),
                ]
            );
        }

        // Buyer Users
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Buyer $i",
                'email' => "buyer$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'is_verified' => true,
                'verified_at' => now(),
            ]);
        }
    }

    }

