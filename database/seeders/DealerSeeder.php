<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dealer;
use App\Models\User;
use App\Models\City;

class DealerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $dealerUsers = User::where('role', 'dealer')->get();
        $cities = City::all();

        foreach ($dealerUsers as $index => $user) {
            Dealer::create([
                'user_id' => $user->id,
                'business_name' => "Premium Auto Dealer " . ($index + 1),
                'description' => "We are a trusted car dealer with over 10 years of experience in the automotive industry.",
                'phone' => '+91' . rand(7000000000, 9999999999),
                'whatsapp' => '+91' . rand(7000000000, 9999999999),
                'address' => fake()->address(),
                'city_id' => $cities->random()->id,
                'pincode' => fake()->postcode(),
                'working_hours' => [
                    'monday' => '9:00 AM - 7:00 PM',
                    'tuesday' => '9:00 AM - 7:00 PM',
                    'wednesday' => '9:00 AM - 7:00 PM',
                    'thursday' => '9:00 AM - 7:00 PM',
                    'friday' => '9:00 AM - 7:00 PM',
                    'saturday' => '9:00 AM - 6:00 PM',
                    'sunday' => 'Closed',
                ],
                'is_verified' => true,
                'is_premium' => $index < 2, // First 2 dealers are premium
                'premium_until' => $index < 2 ? now()->addMonths(3) : null,
                'rating' => rand(35, 50) / 10,
                'total_reviews' => rand(5, 50),
            ]);
        }
    }
    
}
