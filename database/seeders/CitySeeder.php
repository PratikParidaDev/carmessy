<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use Illuminate\Support\Str;


class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'Mumbai', 'state' => 'Maharashtra', 'is_popular' => true],
            ['name' => 'Delhi', 'state' => 'Delhi', 'is_popular' => true],
            ['name' => 'Bangalore', 'state' => 'Karnataka', 'is_popular' => true],
            ['name' => 'Hyderabad', 'state' => 'Telangana', 'is_popular' => true],
            ['name' => 'Ahmedabad', 'state' => 'Gujarat', 'is_popular' => true],
            ['name' => 'Chennai', 'state' => 'Tamil Nadu', 'is_popular' => true],
            ['name' => 'Kolkata', 'state' => 'West Bengal', 'is_popular' => true],
            ['name' => 'Pune', 'state' => 'Maharashtra', 'is_popular' => true],
            ['name' => 'Jaipur', 'state' => 'Rajasthan', 'is_popular' => false],
            ['name' => 'Surat', 'state' => 'Gujarat', 'is_popular' => false],
            ['name' => 'Lucknow', 'state' => 'Uttar Pradesh', 'is_popular' => false],
            ['name' => 'Kanpur', 'state' => 'Uttar Pradesh', 'is_popular' => false],
            ['name' => 'Nagpur', 'state' => 'Maharashtra', 'is_popular' => false],
            ['name' => 'Indore', 'state' => 'Madhya Pradesh', 'is_popular' => false],
            ['name' => 'Thane', 'state' => 'Maharashtra', 'is_popular' => false],
        ];

        foreach ($cities as $city) {
            $slug = Str::slug($city['name']);

            City::updateOrCreate(
                ['slug' => $slug],
                array_merge($city, ['slug' => $slug])
            );
        }
    }

    
}
