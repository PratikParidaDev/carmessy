<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Make;

class MakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $makes = [
            ['name' => 'Maruti Suzuki', 'country' => 'India', 'is_popular' => true, 'order' => 1],
            ['name' => 'Hyundai', 'country' => 'South Korea', 'is_popular' => true, 'order' => 2],
            ['name' => 'Tata', 'country' => 'India', 'is_popular' => true, 'order' => 3],
            ['name' => 'Mahindra', 'country' => 'India', 'is_popular' => true, 'order' => 4],
            ['name' => 'Honda', 'country' => 'Japan', 'is_popular' => true, 'order' => 5],
            ['name' => 'Toyota', 'country' => 'Japan', 'is_popular' => true, 'order' => 6],
            ['name' => 'Kia', 'country' => 'South Korea', 'is_popular' => true, 'order' => 7],
            ['name' => 'Volkswagen', 'country' => 'Germany', 'is_popular' => false, 'order' => 8],
            ['name' => 'Skoda', 'country' => 'Czech Republic', 'is_popular' => false, 'order' => 9],
            ['name' => 'Renault', 'country' => 'France', 'is_popular' => false, 'order' => 10],
            ['name' => 'Ford', 'country' => 'USA', 'is_popular' => false, 'order' => 11],
            ['name' => 'Nissan', 'country' => 'Japan', 'is_popular' => false, 'order' => 12],
            ['name' => 'BMW', 'country' => 'Germany', 'is_popular' => false, 'order' => 13],
            ['name' => 'Mercedes-Benz', 'country' => 'Germany', 'is_popular' => false, 'order' => 14],
            ['name' => 'Audi', 'country' => 'Germany', 'is_popular' => false, 'order' => 15],
        ];

        foreach ($makes as $make) {
            Make::create($make);
        }
    }

    
}
