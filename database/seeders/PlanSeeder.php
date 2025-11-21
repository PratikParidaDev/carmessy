<?php

namespace Database\Seeders;
use App\Models\Plan;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $plans = [
            [
                'name' => 'Basic',
                'description' => 'Perfect for small dealers starting out',
                'price' => 999.00,
                'duration_days' => 30,
                'featured_listings' => 0,
                'regular_listings' => 5,
                'verified_badge' => false,
                'priority_support' => false,
                'features' => [
                    '5 Regular Listings',
                    'Basic Analytics',
                    'Email Support',
                ],
            ],
            [
                'name' => 'Professional',
                'description' => 'For growing dealerships',
                'price' => 2999.00,
                'duration_days' => 30,
                'featured_listings' => 3,
                'regular_listings' => 15,
                'verified_badge' => true,
                'priority_support' => false,
                'features' => [
                    '15 Regular Listings',
                    '3 Featured Listings',
                    'Verified Badge',
                    'Advanced Analytics',
                    'Priority Email Support',
                ],
            ],
            [
                'name' => 'Premium',
                'description' => 'For established dealers',
                'price' => 5999.00,
                'duration_days' => 30,
                'featured_listings' => 10,
                'regular_listings' => 50,
                'verified_badge' => true,
                'priority_support' => true,
                'features' => [
                    '50 Regular Listings',
                    '10 Featured Listings',
                    'Verified Badge',
                    'Premium Analytics',
                    '24/7 Priority Support',
                    'Homepage Banner',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
    
}
